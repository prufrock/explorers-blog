---
extends: _layouts.post
section: content
title: Moving a Dot with MetalKit
date: 2021-09-23
description: Got a green dot to move across the screen with MetalKit.
cover_image: https://res.cloudinary.com/demmholkv/image/upload/v1632440487/blog/xcode-green-dot_q06spo.png
excerpt: The key part of this project for me was figuring out how to set up a game loop that gets along with MetalKit.
categories: [swift]
author: David Kanenwisher
---

I got [a dot to animate across the screen](https://youtu.be/w_zOEUQF5BA) with MetalKit. It doesn't seem like much but it's been quite a trip to get here. All of the code to make it work can be found in [Metal003GameLoop](https://github.com/prufrock/MetalForge/tree/main/Metal003GameLoop). 

The key part of this project for me was figuring out how to set up a game loop that gets along with MetalKit while avoiding the more complex [CADisplayLink](https://developer.apple.com/documentation/quartzcore/cadisplaylink). This meant using the `draw` method of `MTKViewDelegate` to update the game *and* draw the result.

```swift
extension Drawer: MTKViewDelegate {
    func mtkView(_ view: MTKView, drawableSizeWillChange size: CGSize) {
        print(#function)
    }

    func draw(in view: MTKView) {
        let current = CACurrentMediaTime()
        let delta = current - previous
        previous = current

        world.update(elapsed: delta)

        render(in: view)
    }
}
```

I still have a lot to figure out but this is some exciting progress.