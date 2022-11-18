---
extends: _layouts.post
section: content
title: Don't Forget Point Size
date: 2022-11-17
description: Output is weird when point size isn't set
cover_image: https://res.cloudinary.com/demmholkv/image/upload/v1668741273/blog/trouble-no-point-size.pnga
excerpt: It turn
categories: [swift]
author: David Kanenwisher
---

I ran into some trouble when I didn't specify the `point_size` in the output from the vertex function.

![a white screen with a red point flickering across it](https://res.cloudinary.com/demmholkv/image/upload/v1668740611/blog/no-point-size.gif "jumpy point")

It took me quite a bit of head scratching to figure out what was going on. It lead me to the [Metal Shading Language Specification](https://developer.apple.com/metal/Metal-Shading-Language-Specification.pdf) where I found that all of the possible values are predefined.

After seeing `position` in there it makes the interpolation in the fragment function make more sense. I had been trying to figure out how the fragment function knows which attribute of the input is the value to interpolate. Clearly, with `position` being part of the specification it simpy knows.

After updating the shader with the point it works as expected.

```
VertexOut vertex_out {
    .position = transform * float4(v.position, 1),
    .point_size = 20.0
};
```

![a white screen with a red point in the middle](https://res.cloudinary.com/demmholkv/image/upload/v1668741687/blog/steady-point_omj8vr.png "the point holds steady")

Fascinating!