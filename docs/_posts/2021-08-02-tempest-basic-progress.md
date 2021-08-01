---
layout: post
title: "Tempest: Basic progress"
description: 
summary: 
tags: software-dev retrowars tempest
minute: 5
---

In order to start making progress on this game, the initial work will:
* Ignore the third dimension
* Ignore enemies
* Focus on the break down of levels into segments in a loop
* Allow the player to move around these segments

I must confess that I've never actually played before, but I do really like the videos I've seen, so please go easy on me :)
My reference point for how the levels look and how the game is played is this YouTube video:

[<img class="border" src="{{ site.base_url }}/assets/images/tempest/tempest-youtube.png" alt="Link to YouTube video of Tempest gameplay" />](https://www.youtube.com/watch?v=jfaCrdBABUY)

## First three levels

The [first](https://github.com/retrowars/retrowars/commit/7d394a163803f4354aa0acdd65db6039f2c6995d) [three](https://github.com/retrowars/retrowars/commit/aa54ae7b55073e7b46eef811c758e3f92644e785) [commits](https://github.com/retrowars/retrowars/commit/34ce2d14e12e23a0f52798ccf1484df7b48203d4) were to add the [first](https://github.com/retrowars/retrowars/commit/7d394a163803f4354aa0acdd65db6039f2c6995d#diff-2c109f1921ae52b4b70e9979350c924b9e92d9468cd4e64a75e9e84d0a02f191R14) [three](https://github.com/retrowars/retrowars/commit/aa54ae7b55073e7b46eef811c758e3f92644e785#diff-2c109f1921ae52b4b70e9979350c924b9e92d9468cd4e64a75e9e84d0a02f191R39) [levels](https://github.com/retrowars/retrowars/commit/34ce2d14e12e23a0f52798ccf1484df7b48203d4#diff-2c109f1921ae52b4b70e9979350c924b9e92d9468cd4e64a75e9e84d0a02f191R77).

[Each level is broken into a list of `Segment`s](https://github.com/retrowars/retrowars/commit/7d394a163803f4354aa0acdd65db6039f2c6995d#diff-2c109f1921ae52b4b70e9979350c924b9e92d9468cd4e64a75e9e84d0a02f191R9), where each `Segment` represents a start and an end point on the screen using a pair of libgdx `Vector2`s:

```java
class TempestGameState {
    val level: Level
}

data class Level(
    val segments: List<Segment>,
)

data class Segment(
    val start: Vector2,
    val end: Vector2,
) 
```

And then each level is hand crafted using whatever necessary (e.g. hard coded start and end points for each segment in the rectangular second and third levels, but trigonometry in the first "round" level).

<img class="border" src="{{ site.base_url }}/assets/images/tempest/tempest-level-1.png" alt="Screenshto of initial commit" />

<img class="border" src="{{ site.base_url }}/assets/images/tempest/tempest-level-2.png" alt="Screenshto of initial commit" />

<img class="border" src="{{ site.base_url }}/assets/images/tempest/tempest-level-3.png" alt="Screenshto of initial commit" />

## Player position and movement 

By [recording which segment the current player is on](https://github.com/retrowars/retrowars/commit/7a4b2bdfe57653de1fc3fd1506f1af9c0487144a#diff-2c109f1921ae52b4b70e9979350c924b9e92d9468cd4e64a75e9e84d0a02f191R12), it becomes straightforward to:
* [Highlight this segment differently during rendering](https://github.com/retrowars/retrowars/commit/7a4b2bdfe57653de1fc3fd1506f1af9c0487144a#diff-858e4d54a1131cd1f2f208bbcde55df53e211eab84c636550212e2ceb184cae8R93).
* [Respond to input](https://github.com/retrowars/retrowars/commit/7a4b2bdfe57653de1fc3fd1506f1af9c0487144a#diff-858e4d54a1131cd1f2f208bbcde55df53e211eab84c636550212e2ceb184cae8R47) by changing the segment to the next one (with the exception that we need to [support wrapping around the loop](https://github.com/retrowars/retrowars/commit/7a4b2bdfe57653de1fc3fd1506f1af9c0487144a#diff-858e4d54a1131cd1f2f208bbcde55df53e211eab84c636550212e2ceb184cae8R73)).

<video class="border" style="max-width: 960; max-height: 512" controls>
    <source src="{{ site.base_url }}/assets/videos/tempest/tempest-initial-rotation.mp4" type="video/mp4" />
</video>

The player segment is recorded in the game state as a reference to one of the level segments.
We could equally store an index to the segment in question, but this worked well for the Snake game and made a lot of comparisons much simpler.

```java
class TempestGameState(private val worldWidth: Float, private val worldHeight: Float) {
    val level: Level = makeThirdLevel(worldWidth, worldHeight)

    var playerSegment = level.segments[0]
}
```

The input handling is taken from the tetris game, whereby we need to ensure that only one keypress is registered each frame.
Otherwise, on faster devices, the game will respond too quickly.

Therefore, instead of asking whether a button is pressed at the start of each frame (as we do with Asteroids), we instead record a button status when the button is first pressed, and then clear that status each frame after processing it:

```java
class TempestGameState {
    ...
    var moveCounterClockwise = ButtonState.Unpressed
    var moveClockwise = ButtonState.Unpressed
}

class TempestGameScreen {
    ...
    controller!!.listen(
            TempestSoftController.Buttons.MOVE_COUNTER_CLOCKWISE,
            { state.moveCounterClockwise = if (state.moveCounterClockwise == ButtonState.Unpressed) ButtonState.JustPressed else ButtonState.Held },
            { state.moveCounterClockwise = ButtonState.Unpressed })

    controller!!.listen(
            TempestSoftController.Buttons.MOVE_CLOCKWISE,
            { state.moveClockwise = if (state.moveClockwise == ButtonState.Unpressed) ButtonState.JustPressed else ButtonState.Held },
            { state.moveClockwise = ButtonState.Unpressed })

    override fun updateGame(delta: Float) {
        ...
        movePlayer()
        resetInput()
    }

    private fun movePlayer() {
        val currentIndex = state.level.segments.indexOf(state.playerSegment)

        if (state.moveClockwise == ButtonState.JustPressed) {
            state.playerSegment = state.level.segments[(currentIndex + 1) % state.level.segments.size]
        } else if (state.moveCounterClockwise == ButtonState.JustPressed) {
            state.playerSegment = state.level.segments[(state.level.segments.size + currentIndex - 1) % state.level.segments.size]
        }
    }

    private fun resetInput() {
        state.moveCounterClockwise = ButtonState.Unpressed
        state.moveClockwise = ButtonState.Unpressed
    }
```


## Next steps

Next will either be the addition of firing from the players current position, or the addition of a 3rd dimension (which will make it easier to see if the firing logic is working as expected).
