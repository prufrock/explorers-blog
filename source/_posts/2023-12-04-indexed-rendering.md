---
extends: _layouts.post
section: content
title: Metal Indexed Rendering
date: 2023-12-04
description: Render the same thing with fewer vertices.
cover_image:  https://res.cloudinary.com/demmholkv/image/upload/v1701722430/blog/indexed-rendering.png
excerpt: Indexed rendering allows you to get more bang for your buck when rendering by allowing the code to make a single command with the same vertices and a list of transformations per instance.
categories: [swift]
author: David Kanenwisher
---

Indexed rendering allows you to get more bang for your buck when rendering by allowing the code to make a single command with the same vertices and a list of transformations per instance.

I’m going to do this with as simple a bit of code as possible to avoid confusion about extra parameters. Most importantly, I’m going to skip passing in textures and just use a single color.

First, get your device, command queue, and library ready:

```swift
guard let device = MTLCreateSystemDefaultDevice() else {
    fatalError("""
        I looked in the computer and didn't find a device...sorry =/
    """)
}

guard let commandQueue = device.makeCommandQueue() else {
    fatalError("""
        What?! No comand queue. Come on!
    """)
}

guard let library = device.makeDefaultLibrary() else {
    fatalError("""
        What in the what?! The library couldn't be loaded.
    """)
}

```

Now set up the shader in `Shaders.metal`.

```cpp
#include <metal_stdlib>
#include <simd/simd.h>
using namespace metal;

struct Vertex
{
    float3 position [[attribute(0)]];
};

struct VertexOut {
    float4 position [[position]];
    // when rendering points you need to specify the point_size or else it grabs it from a random place.
    float point_size [[point_size]];
};

vertex VertexOut indexed_main(
    Vertex v [[stage_in]],
    constant matrix_float4x4 &projection[[buffer(1)]],
    constant matrix_float4x4 *indexedModelMatrix [[buffer(2)]],
    uint vid [[vertex_id]],
    uint iid [[instance_id]]
    ) {
    VertexOut vertex_out {
        .position = projection * indexedModelMatrix[iid] * float4(v.position, 1),
        .point_size = 1.0
    };

    return vertex_out;
}

fragment float4 fragment_main(constant float4 &color [[buffer(0)]]) {
    return color;
}
```

I think `indexed_main` is about the simplest indexed shader I can come up with. Here’s a breakdown of the arguments:
* `Vertex v [[stage_in]]` is the current vertex
* `constant matrix_float4x4 &uniforms [[buffer(1)]]` are all world to NDC transformations that are the same for all models.
* `constant matrix_float4x4 *indexedModelMatrix [[buffer(2)]]` is a big ol’ array of all the transformations that take the models from model space into world space. The instance id has to be used to apply the correct one.
* `uint vid [[vertex_id]]` the vertex id, or the index of the current vertex
* `uint iid [[instance_id]]` the instance id, or the index of the current instance

With that in place now you can set up a pipeline state object:
```swift
let vertexDescriptor = MTLVertexDescriptor()
vertexDescriptor.attributes[0].format = MTLVertexFormat.float3
vertexDescriptor.attributes[0].bufferIndex = 0
vertexDescriptor.attributes[0].offset = 0
vertexDescriptor.layouts[0].stride = MemoryLayout<SIMD3<Float>>.stride

let descriptor = MTLRenderPipelineDescriptor()
descriptor.vertexFunction = library.makeFunction(name: "vertex_main")
descriptor.fragmentFunction = library.makeFunction(name: "fragment_main")
descriptor.colorAttachments[0].pixelFormat = .bgra8Unorm
descriptor.depthAttachmentPixelFormat = .depth32Float
descriptor.vertexDescriptor = vertexDescriptor

vertexPipeline = try! device.makeRenderPipelineState(descriptor: descriptor)
```

Nothing too special here other than to take note that vertices are being passed into buffer index 0, corresponding to what we see in the shader for the `Vertex` struct.

I defined a simple square for rendering:
```swift
struct Square {
    let v: [SIMD3<Float>] = [
        F3(-1, 1, 0), F3(1, 1, 0), F3(1, -1, 0), F3(-1,-1, 0),
    ]
    let indexes: [UInt16] = [0, 1, 2, 0, 3, 2]
    let primitiveType: MTLPrimitiveType = .triangle
}
```

Notice here how there are 4 vertices and 6 indexes. The indexes pick which vertices to use, avoiding duplication of the actual index data.

Now you can prepare buffers for these:
```swift
let model = Square()
let indexBuffer = device.makeBuffer(bytes: model.indexes, length: MemoryLayout<UInt16>.stride * model.indexes.count)!
let vertexBuffer = device.makeBuffer(bytes: model.v, length: MemoryLayout<Float3>.stride * model.v.count, options: [])
```

Now in your code that is called each time draw is called, setup the view, create the command buffer and command encoder:
```swift
view.device = device
view.clearColor = MTLClearColor(red: 0.0, green: 0.0, blue: 0.0, alpha: 0.0)

guard let commandBuffer = commandQueue.makeCommandBuffer() else {
    fatalError("""
               Ugh, no command buffer. They must be fresh out!
               """)
}

guard let descriptor = view.currentRenderPassDescriptor, let encoder = commandBuffer.makeRenderCommandEncoder(descriptor: descriptor) else {
    fatalError("""
               Dang it, couldn't create a command encoder.
               """)
}
```

Now I get a little fancy here because I am using [lecs-swift](https://github.com/prufrock/lecs-swift), but you can do the same thing without it by looping over the objects you want to render and creating the 4x4 transformation matrix for each.
```swift
var finalTransforms: [Float4x4] = []

world.ecs.select([LECSPosition2d.self, Rotation3d.self]) { world, row, columns in
    let position = row.component(at: 0, columns, LECSPosition2d.self)
    let rotation = row.component(at: 1, columns, Rotation3d.self)

    finalTransforms.append(
        // upright to world
        Float4x4.translate(F2(position.x, position.y))
        * Float4x4.scale(x: 0.25, y: 0.25, z: 1.0)
        * rotation.m
    )
}
```

Then, use a command encoder to send a `drawIndexedPrimitives` command to the GPU:

```swift
encoder.setRenderPipelineState(indexedVertexPipeline)
encoder.setDepthStencilState(depthStencilState)
encoder.setVertexBuffer(vertexBuffer, offset: 0, index: 0)
// projection is your 4x4 projection matrix
encoder.setVertexBytes(&projection, length: MemoryLayout<Float4x4>.stride, index: 1)
encoder.setVertexBytes(&finalTransforms, length: MemoryLayout<Float4x4>.stride * finalTransforms.count, index: 2)

var fragmentColor = Float4(Color.orange)

encoder.setFragmentBuffer(vertexBuffer, offset: 0, index: 0)
encoder.setFragmentBytes(&fragmentColor, length: MemoryLayout<Float3>.stride, index: 0)

encoder.drawIndexedPrimitives(
    type: model.primitiveType,
    indexCount: index.count,
    indexType: .uint16,
    indexBuffer: indexBuffer,
    indexBufferOffset: 0,
    instanceCount: finalTransforms.count
)
```

It’s very important that the buffers and indexes passed to `setVertexBytes` line up with the parameters to `vertex_main`.

With that done you can end encoding and present the frame:
```swift
encoder.endEncoding()

guard let drawable = view.currentDrawable else {
    fatalError("""
               Wakoom! Attempted to get the view's drawable and everything fell apart! Boo!
               """)
}

commandBuffer.present(drawable)
commandBuffer.commit()
```

If you run into trouble here are some places to check:
* Ensure all the correct arguments are being passed to the shaders through `setVertexBytes`.
* Make sure you provide the correct counts to the `length` and `count` arguments.
* Double check you have the correct types when using `MemoryLayout<>.stride`
* Use frame capture in the Metal debugger to see what arguments are actually being passed to the shader.