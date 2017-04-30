[![Build Status](https://travis-ci.org/hollodotme/phpmq.svg?branch=master)](https://travis-ci.org/hollodotme/phpmq)
[![Latest Stable Version](https://poser.pugx.org/hollodotme/phpmq/v/stable)](https://packagist.org/packages/hollodotme/phpmq) 
[![Total Downloads](https://poser.pugx.org/hollodotme/phpmq/downloads)](https://packagist.org/packages/hollodotme/phpmq) 
[![Coverage Status](https://coveralls.io/repos/github/hollodotme/phpmq/badge.svg?branch=master)](https://coveralls.io/github/hollodotme/phpmq?branch=master)

# PHPMQ

A lightweight PHP message queue

At the moment this is a proof-of-concept implementation.

## Description

This implementation aims to implement the following requirements.

For details of the client-endpoint-communication see the message [protocol documentation](./docs/MessageProtocol.md).

### Message endpoint

* Establish a communication endpoint via network or unix domain socket. (both should be possible)
* Accept connections to that endpoint and constantly receive messages from clients

### Message senders

* Can connect/disconnect to/from the message endpoint
* Can request a named queue
* Can send arbitrary amount of messages to the queue

### Message queues

* Create named queues as they are requested by the sender
* Flush queues on demand
* Releases acknowledged messages
* Give status feedback on existing queues
* First in - First out

### Message persistence
 
* Persist incoming messages to an SQLite3 memory storage
* Mirror the SQLite3 memory storage to a SQLite3 file storage to keep messages beyond a reboot
* Mirror to file storage in background to keep persistence fast 

### Message consumers

* Can connect/disconnect to/from the message endpoint
* Can consume a variable amount of messages from one or multiple named queues
* Must acknowledge the consumed message to release it from the queue 

### Message distribution

* Distribute messages equally to all connected consumers
* Reallocates distribution on dis-/connect of consumers
* Dispatches a message to other consumer, if message was dispatched but not acknowledged and the respective consumer disconnected

## Contributing

Contributions are welcome and will be fully credited. Please see the [contribution guide](CONTRIBUTING.md) for details.


