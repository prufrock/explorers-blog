---
extends: _layouts.post
section: content
title: Rendering a Bunch of Cubes
date: 2021-10-16
description: All the cubes you could ask for.
cover_image: https://res.cloudinary.com/demmholkv/image/upload/v1633453571/blog/cubes-on-xcode_zoryur.jpg
excerpt: ???
categories: [swift]
author: David Kanenwisher
---

If you thought one moving cube was cool you are gonna love [Lots of Moving Cubes](https://youtu.be/4wH-3VwPxtQ)!

In some ways this wasn't as hard as I thought it would be. I simply put a bunch of cubes on the screen!

I wanted a new cube to appear in a random place each time the screen is touched. I made this happen by moving the code the creates the point into the code that updates the world in response to a "click" event:

```swift
    func click() {
        switch state {
        case .playing:
            state = .paused
            self.nodes.append(
                Node(
                    location: Point(
                        Float.random(in: -1...1),
                        Float.random(in: self.cameraBottom...self.cameraTop),
                        Float.random(in: 0...1)
                    ),
                    vertices: VerticeCollection().c[.cube]!
                )
            )
        case .paused:
            state = .playing
        }
    }
```

The biggest change was moving the random Point code out of Vertices. This became even more important as I tried to keep the points in side the camera by reference the top and bottom of the camera. This made me realize that it's likely some models need to information about the camera. This allows them to make decisions about their geometry if they need to show up in direct relation to the camera in someway.

I also wanted the newest cube to appear red so you can track where it goes. This took a bit more adjusting so the color of the Vertices can be changed after the fact. It also revealed a need to have more control of the logic around what cube is red in one place. I didn't fix this yet but I hope to get to it soon.