<?php declare(strict_types=1);

function send_query_to_server(string $packet, string $serverIp, int $timeoutSecs): string
{
    $ctx = \stream_context_create(['socket' => ['bindto' => '0:54321']]);

    if (!$socket = \stream_socket_client("udp://{$serverIp}:53", $errNo, $errStr, 0.0, STREAM_CLIENT_CONNECT, $ctx)) {
        throw new \RuntimeException("Failed to create client socket: {$errNo}: {$errStr}");
    }

    \stream_socket_sendto($socket, $packet);

    $r = [$socket];
    $w = $e = [];

    if (!\stream_select($r, $w, $e, $timeoutSecs)) {
        throw new \RuntimeException("Request timed out after {$timeoutSecs} seconds");
    }

    return \fread($socket, 524);
}
