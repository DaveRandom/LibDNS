<?php

    // Simple server that responds with the address configured in $remoteIP to all
    // A record lookup requests, and relays any other request via the server configured
    // in $relayServerIP

    use \DaveRandom\LibDNS\Packet,
        \DaveRandom\LibDNS\DataType,
        \DaveRandom\LibDNS\Messages\Request,
        \DaveRandom\LibDNS\Messages\Response,
        \DaveRandom\LibDNS\Records\Resource,
        \DaveRandom\LibDNS\Records\Query,
        \DaveRandom\LibDNS\DataTypes\IPv4Address,
        \DaveRandom\LibDNS\DataTypes\Vectors\SOA;




    $localIP = '0.0.0.0';       // local bind address
    $relayServerIP = '8.8.8.8'; // server to use for relaying unanwerable requests
    $remoteIP = '127.0.0.1';    // address for static response to A records

    $heartbeat = 10;            // poll interval for timeouts in beats/sec
    $timeout = 2;               // timeout is seconds for relayed requests




    class Resolver
    {
        private $lookupQuestionFactory;
        private $resourceFactory;
        private $remoteAddr;
        private $packetFactory;
        private $requestFactory;
        private $responseFactory;
        private $relayServerAddr;
        private $relayServerPort;

        private $idCounter = 0;
        private $pendingRelayQuestions = [];

        private $socket;

        public function __construct(
            LookupQuestionFactory $lookupQuestionFactory,
            PacketFactory $packetFactory,
            RequestFactory $requestFactory,
            ResponseFactory $responseFactory,
            ResourceFactory $resourceFactory,
            IPv4Address $remoteAddr,
            $relayServerAddr,
            $relayServerPort = 53
        ) {
            $this->lookupQuestionFactory = $lookupQuestionFactory;
            $this->packetFactory = $packetFactory;
            $this->requestFactory = $requestFactory;
            $this->responseFactory = $responseFactory;
            $this->resourceFactory = $resourceFactory;
            $this->remoteAddr = $remoteAddr;
            $this->relayServerAddr = $relayServerAddr;
            $this->relayServerPort = $relayServerPort;
        }
    
        private function createSocket()
        {
            $uri = "udp://$this->relayServerAddr:$this->relayServerPort";
            if (!$this->socket = stream_socket_client($uri, $errNo, $errStr)) {
                throw new \RuntimeException("Creating server socket failed: $errNo: $errStr");
            }
        }

        private function getNextId()
        {
            $result = $this->idCounter++;

            if ($this->idCounter >= 65536) {
                $this->idCounter = 0;
            }

            return $result;
        }

        public function resolve(Query $question)
        {
            $lookupQuestion = $this->lookupQuestionFactory->create($question);

            if ($question->getType() === Resource::TYPE_A) {
                $lookupQuestion->setAnswer($this->resourceFactory->create($question->getName(), Resource::TYPE_A, Resource::CLASS_IN, 1440, $this->remoteAddr));
            } else {
                $id = $this->getNextId();
                $this->pendingRelayQuestions[$id] = $lookupQuestion;

                $request = $this->requestFactory->create($id);
                $request->addQuestionRecord($question);
                $request->isRecursionDesired(true);

                if (!isset($this->socket)) {
                    $this->createSocket();
                }
                stream_socket_sendto($this->socket, $request->writeToPacket());
            }

            return $lookupQuestion;
        }

        public function getSocket()
        {
            if (!isset($this->socket)) {
                $this->createSocket();
            }

            return $this->socket;
        }

        public function processPendingResponses()
        {
            do {
                $data = stream_socket_recvfrom($this->socket, 512);
                $packet = $this->packetFactory->create($data);

                $response = $this->responseFactory->create();
                $response->loadFromPacket($packet);
                $id = $response->getID();

                $answers = $response->getAnswerRecords();
                $answer = count($answers) > 0 ? $answers[0] : null;

                $this->pendingRelayQuestions[$id]->setAnswer($answer);
                unset($this->pendingRelayQuestions[$id]);

                $r = [$this->socket];
                $w = $e = null;
            } while (stream_select($r, $w, $e, 0));
        }
    }

    class LookupRequest
    {
        private $resolver;
        private $responseFactory;

        private $request;
        private $clientAddress;

        private $timeout;

        private $lookupQuestions = [];

        public function __construct(Resolver $resolver, ResponseFactory $responseFactory, Request $request, $clientAddress, $timeout)
        {
            $this->resolver = $resolver;
            $this->responseFactory = $responseFactory;
            $this->request = $request;
            $this->clientAddress = $clientAddress;

            $this->timeout = microtime(true) + $timeout;
        }

        public function resolve()
        {
            foreach ($this->request->getQuestionRecords() as $question) {
                $this->lookupQuestions[] = $this->resolver->resolve($question);
            }
        }

        public function isResolved()
        {
            $result = true;
            
            foreach ($this->lookupQuestions as $lookupQuestion) {
                if (!$lookupQuestion->isResolved()) {
                    $result = false;
                    break;
                }
            }

            return $result;
        }

        public function isTimedOut()
        {
            return microtime(true) >= $this->timeout;
        }

        public function getClientAddress()
        {
            return $this->clientAddress;
        }
        
        public function getResponse()
        {
            $response = $this->responseFactory->create($this->request->getID());

            foreach ($this->lookupQuestions as $lookupQuestion) {
                $question = $lookupQuestion->getQuestion();
                $answer = $lookupQuestion->getAnswer();

                $response->addQuestionRecord($question);
                if ($answer) {
                    $response->addAnswerRecord($answer);
                }
            }

            return $response;
        }
    }

    class LookupRequestFactory
    {
        private $resolver;
        private $responseFactory;
        private $defaultTimeout;
    
        public function __construct(Resolver $resolver, ResponseFactory $responseFactory, $defaultTimeout = 2)
        {
            $this->resolver = $resolver;
            $this->responseFactory = $responseFactory;
            $this->defaultTimeout = $defaultTimeout;
        }
        
        public function create(Request $request, $clientAddress, $timeout = null)
        {
            return new LookupRequest($this->resolver, $this->responseFactory, $request, $clientAddress, $timeout ?: $this->defaultTimeout);
        }
    }

    class LookupQuestion
    {
        private $question;
        private $answer;

        private $haveAnswer = false;

        public function __construct(Query $question)
        {
            $this->question = $question;
        }

        public function setAnswer(Resource $answer = null)
        {
            $this->answer = $answer;
            $this->haveAnswer = true;
        }

        public function getAnswer()
        {
            return $this->answer;
        }

        public function getQuestion()
        {
            return $this->question;
        }

        public function isResolved()
        {
            return $this->haveAnswer;
        }
    }
    
    class LookupQuestionFactory
    {
        public function create(Query $question)
        {
            return new LookupQuestion($question);
        }
    }

    class ResourceFactory
    {
        public function create($name, $type, $class, $ttl, DataType $data)
        {
            return new Resource($name, $type, $class, $ttl, $data);
        }
    }

    class RequestFactory
    {
        public function create($id = null)
        {
            return new Request($id);
        }
    }

    class ResponseFactory
    {
        public function create($id = null)
        {
            return new Response($id);
        }
    }

    class PacketFactory
    {
        public function create($data)
        {
            return new Packet($data);
        }
    }

    class Server
    {
        private $packetFactory;
        private $requestFactory;
        private $lookupRequestFactory;

        private $address;
        private $port;

        private $socket;

        public function __construct(PacketFactory $packetFactory, RequestFactory $requestFactory, LookupRequestFactory $lookupRequestFactory, $address, $port = 53)
        {
            $this->packetFactory = $packetFactory;
            $this->requestFactory = $requestFactory;
            $this->lookupRequestFactory = $lookupRequestFactory;

            $this->address = $address;
            $this->port = $port;
        }
        
        public function listen()
        {
            $this->socket = stream_socket_server("udp://$this->address:$this->port", $errNo, $errStr, STREAM_SERVER_BIND);
        }

        public function getSocket()
        {
            return $this->socket;
        }

        public function getPendingRequest()
        {
            $data = stream_socket_recvfrom($this->socket, 512, 0, $clientAddress);
            $packet = $this->packetFactory->create($data);

            $request = $this->requestFactory->create();
            $request->loadFromPacket($packet);
            
            return $this->lookupRequestFactory->create($request, $clientAddress);
        }
        
        public function sendResponse(LookupRequest $lookupRequest)
        {
            $packet = $lookupRequest->getResponse()->writeToPacket();
            file_put_contents('packet.bin', $packet);
            stream_socket_sendto($this->socket, $lookupRequest->getResponse()->writeToPacket(), 0, $lookupRequest->getClientAddress());
        }
    }

    class LookupRequestCollection implements \Iterator
    {
        private $requests = [];
        private $position = 0;
        
        public function add(LookupRequest $request)
        {
            $this->requests[] = $request;
        }
        
        public function remove(LookupRequest $request)
        {
            if (false !== $key = array_search($request, $this->requests, true)) {
                array_splice($this->requests, $key, 1);
            }
        }

        public function rewind() {
            $this->position = 0;
        }

        public function current() {
            return $this->requests[$this->position];
        }

        public function key() {
            return $this->position;
        }

        public function next() {
            $this->position++;
        }

        public function valid() {
            return isset($this->requests[$this->position]);
        }
    }

    $packetFactory = new PacketFactory;
    $requestFactory = new RequestFactory;
    $responseFactory = new ResponseFactory;

    $resolver = new Resolver(
        new LookupQuestionFactory,
        $packetFactory,
        $requestFactory,
        $responseFactory,
        new ResourceFactory,
        new IPv4Address($remoteIP),
        $relayServerIP
    );

    $server = new Server(
        $packetFactory,
        $requestFactory,
        new LookupRequestFactory($resolver, $responseFactory),
        $localIP
    );

    $pendingRequests = new LookupRequestCollection;


    function __autoload($className) {
        include str_replace('\\', '/', preg_replace('#^\\\\?DaveRandom#i', '../src', $className)).'.php';
    }


    $server->listen();
    $interval = (int) (1000000 / $heartbeat);

    echo "Server running\n";

    while (TRUE) {

        $r = [$server->getSocket(), $resolver->getSocket()];
        $w = $e = [];
        stream_select($r, $w, $e, 0, $interval);

        if (in_array($server->getSocket(), $r)) {
            $lookupRequest = $server->getPendingRequest();
            echo "Received request from client " . $lookupRequest->getClientAddress() . "\n";

            $pendingRequests->add($lookupRequest);
            $lookupRequest->resolve();
        }

        if (in_array($resolver->getSocket(), $r)) {
            echo "Received response from relay server\n";;
            $resolver->processPendingResponses();
        }

        $respond = [];
        foreach ($pendingRequests as $lookupRequest) {
            if ($lookupRequest->isResolved()) {
                echo "Request from " . $lookupRequest->getClientAddress() . " is resolved\n";
                $respond[] = $lookupRequest;
            } else if ($lookupRequest->isTimedOut()) {
                echo "Request from " . $lookupRequest->getClientAddress() . " has timed out\n";
                $respond[] = $lookupRequest;
            }
        }

        foreach ($respond as $lookupRequest) {
            echo "Sending response to " . $lookupRequest->getClientAddress() . "\n";
            $server->sendResponse($lookupRequest);
            $pendingRequests->remove($lookupRequest);
        }

    }
