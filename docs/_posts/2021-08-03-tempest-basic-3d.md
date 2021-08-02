---
layout: post
title: "Tempest: Basic 3D"
description: 
summary: 
tags: software-dev retrowars tempest
minute: 5
---

Prior to adding enemies, I thought it best to get a working proof of concept third dimension for Tempest.

## Allowing for perspective

This was mostly fine, except for the minor hiccup that retrowars [assumes all games want an `OrthographicCamera`](https://github.com/retrowars/retrowars/commit/384316c63c2e33f5d306778435c196af3ac74052#diff-430e74b10257387cd5713bbef5ad92bf0fb3ea4453106ad13b9f8fd7e1461e78L48).

I'm not very happy with my solution of [passing in a flag if you want perspective](https://github.com/retrowars/retrowars/commit/384316c63c2e33f5d306778435c196af3ac74052#diff-430e74b10257387cd5713bbef5ad92bf0fb3ea4453106ad13b9f8fd7e1461e78R39), and will likely refactor that later, but for now it works well.
Here is a discussion on why [flag arguments](https://www.martinfowler.com/bliki/FlagArgument.html) are normally not the solution to a problem you face.

Here are the results:

<video class="border" style="max-width: 960; max-height: 512" controls>
    <source src="{{ site.base_url }}/assets/videos/tempest/tempest-initial-3d.mp4" type="video/mp4" />
</video>

## Offsetting the perspective

After patting myself on the back about how it mostly looked like the YouTube videos of original Tempest, I quickly noticed that the perspective in the original game is actually offset a little.
The camera doesn't look directly down the centre of the level, but starts a bit below centre.

After accounting for this by translating the camera in my version down slightly, it really is starting to shape up nicely.

<img class="border" src="{{ site.base_url }}/assets/images/tempest/tempest-perspective.png" alt="Screenshot of work in progress perspective in Tempest" />

<img class="border" src="{{ site.base_url }}/assets/images/tempest/tempest-perspective-2.png" alt="Screenshot of work in progress perspective in Tempest" />

## Next steps

Next I will tighten up some of the measurements (currently the depth is just arbitrarily chosen as a fixed value).
After that, I think shooting from the players current position, then the addition of enemies.
