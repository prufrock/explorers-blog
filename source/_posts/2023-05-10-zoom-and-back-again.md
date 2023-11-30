---
extends: _layouts.post
section: content
title: Zoom and Back Again
date: 2023-05-10
description: Output is weird when point size isn't set...build
cover_image: https://res.cloudinary.com/demmholkv/image/upload/v1668741273/blog/trouble-no-point-size.pnga
excerpt: Finally, I realized that it was the click location that had to change.
categories: []
author: David Kanenwisher
---

I struggled to get [graphmapnav](https://github.com/prufrock/graphmapnav) to handle scaling everything properly over the past week. It seemed like every time I got one thing another thing broke. Each time I patched one of these problems up I got closer to understanding the problem.

Working out that zooming is an affine transformation so the objects being scaled has to have the origin at the source of the scale, after throwing out caring about “effectiveWidth” and “effectiveHeight” or the dimensions of the screen after zoom, realizing the start and ends of the edges should scale with graphics position since they care about being displayed properly, etc. etc. etc. I got down to one problem.

The collision boxes were either way too big or way too small. I attempted a bunch of clever solutions. All of them would radically throw off the size of the collision rectangle in one way or another. I even had a tracking of the original radius and position so scaling wouldn’t compound on each update. Finally, I realized that it was the click location that had to change.

The click location has to go through screen, to NDC, to world and back. I had forgotten to apply zoom to this projection. After I did this pieces started falling into place. I put it together that zoom should only affect the rendered world. It doesn’t change the world itself. I was able to get rid of all the hacks in the world code to compensate for this important misunderstanding I’d had. Now I can click on the objects no matter how zoomed in I am.