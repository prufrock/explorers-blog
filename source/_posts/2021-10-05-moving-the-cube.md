---
extends: _layouts.post
section: content
title: Moving the Cube
date: 2021-10-05
description: The cube is moving!
cover_image: https://res.cloudinary.com/demmholkv/image/upload/v1632451436/blog/xcode-green-cube_hval5y.png
excerpt: The big adjustment here was separating the model from the location of the object. This meant I translated the location of the object on the CPU but translated the object itself on GPU.
categories: [swift]
author: David Kanenwisher
---

After a little bit of re-arranging I was able to get the [cube moving](https://youtu.be/S7AV-UgXneQ). The most interesting parts of the code can be found in [Drawer.swift](https://github.com/prufrock/MetalForge/blob/main/Metal003GameLoop/Metal003GameLoop/Shared/Drawer.swift).

The big adjustment here was separating the model from the location of the object. This meant I translated the *location* of the object on the CPU but translated the object itself on GPU.

```swift
        @discardableResult
        func translate(_ x: Float, _ y: Float, _ z: Float) -> Node {
            location = location.translate(x, y, z)
            transformation = float4x4.translate(
                x: location.rawValue.x + x,
                y: location.rawValue.y + y,
                z: location.rawValue.z + z
            )

            return self
        }
```

In the code above the location is translated and a transformation matrix is created with the same amount of translation. This transformation matrix is part of the Node so the rendering code is able to get a hold of it.

```swift
        var transform = matrix_identity_float4x4
                // projection
                * float4x4.perspectiveProjection(nearPlane: 0.2, farPlane: 1.0)
                // model
        		* world.node.transformation
        		        		
        encoder.setVertexBytes(&transform, length: MemoryLayout<float4x4>.stride, index: 1)
```

Above the rendering code combines the perspective project matrix(camera) with the node transformation(model). It's then passed to the encoder so the GPU can pick it up and perform the transformation.

I also nearly forgot to actually have the cube translate based on elapsed game time so made a quick adjustment for that:

```swift
    func draw(in view: MTKView) {
        let current = CACurrentMediaTime()
        let delta = current - previous
        previous = current

        world.update(elapsed: delta)

        render(in: view)
    }
```

And really what's the point of rendering 3D graphics in realtime if you can't interact with them?! I added a bit to pause the animation when the screen is touched(not terribly exciting but gave me a chance to try it out).

```swift
extension ViewController {
    override func mouseDown(with event: NSEvent) {
        drawer!.click()
        print("x: \(event.locationInWindow.x) y: \(event.locationInWindow.y)")
    }
}

extension Drawer {
    func click() {
        world.click()
    }
}

class GameWorld: World {
    // skip a bunch of stuff
    func click() {
        switch state {
        case .playing:
            state = .paused
        case .paused:
            state = .playing
        }
    }
    // skip a bunch of other stufff
}
```

I've been especially interested in state-machines lately and how they can be used to model reactive systems. It worked pretty well to pass the click event down to the GameWorld which then changed `state`. Then when `Drawer` asks `GameWorld` for updates `GameWorld` simply checks `state` to determine what to do. This is extremely simplistic but still a good way to get my feet wet.