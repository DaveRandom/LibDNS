LibDNS
======

DNS protocol implementation in pure PHP

Status
------

This library is currently undergoing a ground-up rewrite. The old codebase has been archive to the 2.x branch. The
current master is version 3, a completely new API which is simpler and should be easier to work with, as well as
improving performance and extensibility. V3 also includes much more complete list of IANA-registered record types,
classes and other elements. 

The v3 API has now largely stabilised and work on migrating to the new API is encouraged as soon as possible. It is
unlikely there will be any more releases from the 2.x branch.

A migration guide will be produced in line with the first RC, but the code in the `examples` directory can be used to
infer most of the required changes.
