---
extends: _layouts.post
section: content
title: Surface Normals
date: 2022-09-24
description: Putting surface normals in place
cover_image: https://res.cloudinary.com/demmholkv/image/upload/c_fill,g_auto,h_250,w_970/b_rgb:000000,e_gradient_fade,y_-0.5/v1664066859/blog/normals-all-over.jpg
excerpt: I think this is what I want because it gives the surface a top and bottom and a left a right for the lighting to shade. The red is the darkest on the right because where the value of x is the highest.
categories: [swift]
author: David Kanenwisher
---

My first attempt is confusing and clearly not correct.

![wireframe view of a game green lines for walls, red lines for normals, the normals are scattered all over](https://res.cloudinary.com/demmholkv/image/upload/q_80,h_400/v1664066859/blog/normals-all-over.jpg "normals all over")

I isolate a few wall tiles to make it easier to see the problem:

![a green wireframe cube with red lines as normals pointing diagonal off the bottom](https://res.cloudinary.com/demmholkv/image/upload/q_80,h_400/v1664066675/blog/isolate-normals.jpg "isolate normals")

I get the normals to appear horizontal.

![a green wireframe cube red lines as normals pointing horizontally out from it](https://res.cloudinary.com/demmholkv/image/upload/q_80,h_400/v1664067851/blog/horizontal-normals.jpg "horizontal normals")

I add half the vertical normals.

![a green wireframe cube red lines as normals pointing horizontall out from it and some pointing from the bottom of it](https://res.cloudinary.com/demmholkv/image/upload/q_80,h_400/v1664068066/blog/vertical-normals.jpg "vertical normals")

I’m passing the normals through to the shader but there may be something a little off.

![walking through a yellow 3D hallway with green and blue triangular splashes of color](https://res.cloudinary.com/demmholkv/image/upload/q_80,h_400/v1664068670/blog/yellow-hall.gif "bright yellow hall")

I think the trouble might be that I don’t have enough normals.

```
// .normal
$0.attributes[VertexAttribute.normal.rawValue].format = MTLVertexFormat.float3
$0.attributes[VertexAttribute.normal.rawValue].bufferIndex = VertexAttribute.normal.rawValue
$0.attributes[VertexAttribute.normal.rawValue].offset = 0
$0.layouts[VertexAttribute.normal.rawValue].stride = MemoryLayout<Float3>.stride
```

I think I still have something wrong with my understanding of normals. I think the normals are more like UV coordinates. They a property of the surface of the object. They are just a direction from the surface of the object.

All of the surfaces are flat so I may be able to just use `float3(1.0, 1.0, 1.0)` in the shader as the normal for all the vertices and `return float4(normal.x, normal.y, normal.z, 1);`. This is what I get:

![a white image of 3D game with only a door and part of a wall](https://res.cloudinary.com/demmholkv/image/upload/q_80,h_400/v1664069123/blog/all-white.jpg "all white")

I switch to the vertex’s normals `float3(in.normal.x, in.normal.y, in.normal.z)` I get:

![The 3D walls are show squares the gradually become more red in one direction and more green in another.](https://res.cloudinary.com/demmholkv/image/upload/q_80,h_400/v1664069643/blog/green-and-red-walls.jpg "green and red walls")

I think this is what I want because it gives the surface a top and bottom and a left a right for the lighting to shade. The red is the darkest on the right because where the value of x is the highest. The green is darkest on the top where the value of y is the highest. I’m not sure if that’s quite right but it seems better. The ceiling might be a bit of a problem...for a different day.

Lets see what this looks like with some lighting, starting with hemispheric lighting:

```
float4 sky = float4(0.34, 0.9, 1.0, 1.0);
float4 earth = float4(0.29, 0.58, 0.2, 1.0);

float intensity = in.normal.y * 0.5 + 0.5;
return mix(mix(sky, earth, intensity), float4(colorSample), in.normal.y * 0.5 + 0.5);
```

The idea here being that the top is a bluish color and bottom is a greenish color[1].

![The 3D walls have a variety of colors to make them look like they are underground with a blue gradient coming from the bottom.](https://res.cloudinary.com/demmholkv/image/upload/q_80,h_400/v1664070152/blog/hemispheric-lighting.jpg "hemispheric lighting")

Then just for fun with the world transform:

![Animation of walking through an undergound maze with bright teal areas](https://res.cloudinary.com/demmholkv/image/upload/q_80,h_400/v1664070637/blog/hemispheric-lighting-world-transform.gif "hemispheric lighting world transform")

I love the colors from this. Eventually I’ll have to figure out how to intentionally get something similar.


[1] Thanks for the lighting tutorials Metal By Tutorials! By Caroline Begbie & Marius Horga
