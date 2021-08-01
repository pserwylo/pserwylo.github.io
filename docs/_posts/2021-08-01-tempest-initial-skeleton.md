---
layout: post
title: "Tempest: Initial skeleton"
description: 
summary: 
tags: software-dev retrowars tempest
minute: 5
---

## First steps: Adding a blank game skeleton

Before getting into the specifics of Tempest, I thought I'd quickly review what is involved in adding a new game.

This can be seen in commit [4d7d89d2](https://github.com/retrowars/retrowars/commit/4d7d89d2dcf0885a84aefeec5d61914dab1c098c).

[<img class="border" src="{{ site.base_url }}/assets/images/tempest/commit-initial-skeleton.png" alt="Screenshto of initial commit" />](https://github.com/retrowars/retrowars/commit/4d7d89d2dcf0885a84aefeec5d61914dab1c098c)

This is the minimum required to get to this point:

<img class="border" src="{{ site.base_url }}/assets/images/tempest/tempest-game-select.png" alt="Game select screen with the new Tempest game shown" />

<img class="border" src="{{ site.base_url }}/assets/images/tempest/tempest-empty-game.png" alt="Empty Tempest game screenshot" />

## Code review of these changes

I'm a big fan of code reviews. It is a wonderful way to mentor others, and an even better way to learn yourself.
Although reviewing your own code kind of defeats the point, I hope that doing so in a public forum like this will let me be a bit more reflective, critical, and constructive.

### First thoughts

I don't like the size of that commit.

Well, it isn't so much the size, but rather the random locations with which code needs to be added.
If it is non-obvious to myself as the original author, it does not bode well for others in the community attempting to implement games.

Here are some of the things I find odd and should perhaps refactor in the future...

### Adding a controller

Most games require a controller to be shown on screen. The steps to add a controller for Tempest are:

*Adding an actor to `OptionsScreen.kt`*

```java
addActor(
    IconButton(skin, Games.tempest.icon(sprites)) {
        Gdx.app.postRunnable {
            game.screen = ControllerSelectScreen(
                game,
                Games.tempest,
                TempestSoftController.layouts,
            ) { index -> TempestSoftController(index, game.uiAssets) }
        }
    }
)
```

Why do we need to call `addActor` on `OptionsScreen`? Why do we need to create a button? Why do we need to hard code a reference to `Games.tempest` twice and `TempestSoftController` twice? Why does every game that wants to have a controller need to call `Gdx.app.postRunnable { game.screen = ControllerSelectScreen(...) }`?

*Create a controller and manually add to HUD in `TempestGameScreen`*

```java
private val controller = TempestSoftController(Options.getSoftController(Games.tempest), game.uiAssets)

init {
    addGameOverlayToHUD(controller.getActor())
}
```

*Proposed improvement*

The base `GameScreen` base class should have an open function which optionally returns a controller:

```
open fun getController(): SoftController? = null
```

The `OptionsScreen` can then query the (already present) list of `Games.allSupported` (which we had to populate as part of the initial game adding process) and ask each one for a controller.
Those that return a controller will end up with an entry in the options screen.

The `GameScreen` can then use the fact that a controller exists to decide whether or not to add one to the HUD instead of relying on the base class to have to call `addGameOverlayToHUD(controller.getActor())` at some point.

This may require refactoring the `SoftController` class hierarchy so that it can be created and queried without having to instantiate an actor or specify which controller layout to use.
That is to say, the options screen should be able to know that a game has a controller without having to create a specific instantiation of one.

### Displaying a message at the start of the game

*Call `showMessage()` during `init`*

```
init {
    showMessage("Shoot the enemies", "Don't let them touch you")
}
```

This is easy to do, and only needs a single line to be added.
However, people adding new games have to remember to do this.

*Proposed improvement*

Force the implementing game to provide these messages to the constructor.
That way, the compiler will tell the game designer that these are required pieces of information to make the game behave like all other games.

## Next steps

I will have a go at implementing the proposed feedback prior to starting Tempest properly.
Hopefully it shouldn't take too long and then we can progress with getting the next game done.
