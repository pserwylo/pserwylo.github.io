---
layout: post
title: Sustainable server infrastructure for open source games
description: 
summary: 
tags: software-dev retrowars
minute: 1
---

Lets design an open source multiplayer game server arcitecture that is:
1. Resiliant to the main developer being unable to perpetually maintain an online server.
2. Able to scale up as demand increases, by allowing community members to contribute and run game servers.

Specficially, this is for the [Super Retro Mega Wars](https://github.com/retrowars/retrowars) game.
Even more specifically, I want people to be able to play retro games in realtime against eachother long after I have the capacity or finances to maintain an online server to facilitate multiplayer gameplay.

## v1.0 - Basic multiplayer via a local network
Multiplayer can be as simple or difficult as we want. Lets assume simple, because this article is not about minimizing latency, detecting and preventing cheaters, etc.
In our case, lets assume the game design doesn't care all that much about latency, and it doesn't really care about preventing cheaters.

These assumptions mean that we can create a proof of concept whereby one player opens up a server socket, and another connects a client socket to this.

Although it will work well on a LAN, this doesn't allow people around the world to connect to eachother due to NAT and other considerations.
For that, we'll need one or more publicly available servers on the internet.

## v2.0 - Central internet based game servers
The local networking code can be extracted into a server-side application with little modification.
Hosting this is not particularly difficult either, we deploy the server-side application on a VPS somewhere and embed the URL of this server into the application.

However, what happens if the server goes down? The IP changes? The domain is not renewed? The maintainer gets hit by a bus?
Under each of these scenarios, we are left with a game that has a perpetually broken multiplayer feature.
Every person who discovers this game and installs it, will find that they can only play single player and when they try to play multiplayer, it will error.

## v3.0 A more robust open source multiplayer architecture

### v3.1 - Players manually enter server details
The simplest technical solution is to let people run their own servers and let players manually enter details of a server to play.
How the player gets the server details is not our problem.
Perhaps there are wiki pages or forum posts that enumerate game servers.

While technically simple, this is not a plesant user experience.

### v3.2 - Hard code a list of servers in the game
To improve the user experience, we want players to be able to open the game and play online with the click of a button.
For this, the game needs to know which servers are available.
This can be done by hard coding a list of servers in the game.
When new servers become available or others are decomissioned, a new version of the game is published.

The problem here is that those who have not updated the game will have a stale list of servers.

### v3.3 - Online list of servers managed by game maintainers
Instead of hardcoding a list of servers, the game can refer to a URL that contains a list of servers.
As new servers are brought to the attention of the maintainers, the list can be updated.

This ticks almost all the boxes, but the single point of failure is still the game maintainers.
If they stop maintaining the list of servers, then the game will very quickly stop working.

#### v3.4 - Online list of servers managed by the community
Finally, we want this online list of servers to be maintainable by the community, so that in the case that the maintainers are no longer able to manage it, community members can do so.

This not only solves the key point of failure issue, but also fits very well with the ethos of free and open source software projects.

## Community maintained server list
The way we will do this for Super Retro Mega Wars is to have:
* A JSON file with details of publicly available servers.
* Host this on a GitHub Pages URL associated with the repository (e.g. `https://retrowars.github.io/servers.json`).
* Encourage community contributors on the GitHub Pages repository.
* Document a process for proposing changes to the server list.
* Automatically publish the server list when merge requests are accepted.

This way, if the original maintainers of the game are unable to continue maintaining the server list, then the community is stil able to ensure that multiplayer games are supported.

## Community run servers
In order for the community to be able to ensure continued online play is possible, it needs to be as simple as possible to run your own game server.
Some of the decisions Super Retro Mega Wars is making in this area will be discussed in a separate blog post.
