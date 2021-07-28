---
layout: post
title: WebSocket game server in Kotlin using Ktor
description: 
summary: 
tags: software-dev kotlin libgdx
minute: 5
---

## Free vs commercial software infrastructure

As explained in a [previous post]({% post_url 2021-07-03-sustainable-server-infra-for-open-source-games %}), free software games that have infrastructure requirements are slightly different then their commercial counterparts.
Normally, free software projects don't cost anything other then the time (and sometimes sanity!) of the contributors.
However, as soon as there are requirements on infrastructure, such as a multiplayer game, this normally results in a financial cost.
Given that these projects are usually a labour of love and don't earn money other than donations, this cost can be significant, depending on the popularity of the project and therefore the traffic involved.
Commercial games can cover this cost, but free software projects often cannot.

## Preparing retrowars for online multiplayer

The [retrowars](https://github.com/retrowars) project is a free software multiplayer Android game.
Earlier versions only supported local network multiplayer, which has no infrastructure requirements, but the goal was always to support true multiplayer via the public internet.
This requirement naturally means server infrastructure is required (or elaborate P2P setups which is beyond the scope of this game).

Porting of the local network server to a public internet server was very straightforward, the same code worked essentially as is without modification.

Specifically, it made use of the [KryoNet](https://github.com/EsotericSoftware/kryonet) library which does a great job of:
* Serializing and deserializing Java objects using an efficient binary protocol.
* Discovering servers on the local network

## A desire for HTTP (via WebSockets)

Given the goal of letting people run their own server, the thought process was to make sure that the server can be deployed on commonly available infrastructure as a service.
A popular approach for deploying Java apps is Heroku, which allows people to deploy with a single command, and not have to worry about setting up a virtual machine, installing Java, etc.

When trying to deploy the KryoNet version of the server, it was immediately apparent that using non-HTTP traffic for networking would not work with Heroku.
Furthermore, this experience also made it clear that deploying the server to non-Heroku environments may also cause issues, as many firewalls only allow HTTP traffic through.

This resulted in a refactoring of the retrowars server to use HTTP and websockets instead of KryoNet.

### WebSockets, JSON, Ktor, JmDNS, and Coroutines

Converting the server from KryoNet meant replacing:
* [Kryo serialization](https://github.com/EsotericSoftware/kryo) -> [GSON](https://github.com/google/gson)
* [KryoNet networking](https://github.com/EsotericSoftware/kryonet) -> [Ktor WebSockets](https://ktor.io/docs/websocket.html)
* [KryoNet local network discovery](https://github.com/EsotericSoftware/kryonet#lan-server-discovery) -> [JmDNS](https://github.com/jmdns/jmdns)
* [KryoNet threads](https://github.com/EsotericSoftware/kryonet#threading) -> Ktor coroutines

This was my first experience with Kotlin coroutines, which took a lot of reading and experimenting with before making substantial progress (and I'm still not 100% happy with my usage of them at this point - though I'm sure it will improve with experience).

### WebSocket performance

Many game development blogs discuss best practice with regards to networking technologies.
TCP is a bit slower than UDP, and there is not much discussion of using WebSockets other than for web-based HTML games.
In practice, it seems that other than the overhead of establishing a HTTP connection and upgrading it to a WebSocket connection, the actual sending of messages should be the same as sending bytes directly down a socket.
The retrowars game does not have high performance networking requirements that something such as an FPS may require, so this is even less of an issue.
In practice, the performance of WebSockets is perfectly sufficient for the retrowars game.

