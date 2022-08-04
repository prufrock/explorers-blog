---
extends: _layouts.post
section: content
title: Sprite Sheets in Metal Shader
date: 2022-08-03
description: All the cubes you could ask for.
cover_image: https://res.cloudinary.com/demmholkv/image/upload/v1659576217/blog/shader-sprite-sheet_p9okql.png
excerpt: I got it working to render the textures for the weapons from a sprite sheet rather than from a separate texture for each animation frame.
categories: [swift]
author: David Kanenwisher
---

I got it working to render the textures for the weapons from a sprite sheet rather than from a separate texture for each animation frame. This is pretty exciting since before I used a shader that looked like this:

```
fragment float4 fragment_with_texture(VertexOut in [[stage_in]],
                              texture2d<half> texture0 [[ texture(0) ]],
                              texture2d<half> texture1 [[ texture(1) ]],
                              texture2d<half> texture2 [[ texture(2) ]],
                              texture2d<half> texture3 [[ texture(3) ]],
                              texture2d<half> texture4 [[ texture(4) ]],
                              texture2d<half> texture5 [[ texture(5) ]],
                              texture2d<half> texture6 [[ texture(6) ]],
                              texture2d<half> texture7 [[ texture(7) ]],
                              texture2d<half> texture8 [[ texture(8) ]],
                              texture2d<half> texture9 [[ texture(9) ]],
                              texture2d<half> texture10 [[ texture(10) ]],
                              texture2d<half> texture11 [[ texture(11) ]],
                              texture2d<half> texture12 [[ texture(12) ]],
                              texture2d<half> texture13 [[ texture(13) ]],
                              texture2d<half> texture14 [[ texture(14) ]],
                              texture2d<half> texture15 [[ texture(15) ]],
                              texture2d<half> texture16 [[ texture(16) ]],
                              texture2d<half> texture17 [[ texture(17) ]],
                              texture2d<half> texture18 [[ texture(18) ]],
                              texture2d<half> texture19 [[ texture(19) ]],
                              texture2d<half> texture20 [[ texture(20) ]],
                              constant float4 &color [[buffer(0)]]
                                      ) {
```
I had to pass in so many separate textures to get this to work. I had a rough idea of how to get a sprite sheet to work after doing the work to get to render text from a sprite sheet for the RetroRampage tutorial.

I took that idea and applied it to applying textures to weapons. This translated easily enough because weapons, like pretty much everything else in the game, are being rendered to square billboards. I ended up with a fragment shader that looks like this:

```
fragment float4 fragment_sprite_sheet(
                                      VertexOutOnlyPositionAndUv in [[stage_in]],
                                      texture2d<half> texture [[ texture(0) ]],
                                      constant float4 &color [[buffer(0)]]
                                      ) {
```

It only takes one texture now since it's the sprite sheet. I had hoped most of the work for the spite sheet could happen in the fragment shader. It didn't work out since the fragment shader gets interpolated values so it can determine the color of each pixel. I honestly don't quite understand the magic here. Instead, the vertex shader ends up having to do most of the heavy lifting.

The vertex shader now needs to know the dimensions of the sprite sheet and the sprite to display. Now that information gets passed into the fragment shader as `uint` via `textureId` and a shared struct `SpriteSheet`. The `SpriteSheet` struct knows the height width of texture and of the sprites in the texture. This it possible to determine what texture to use with the single value `textureId`.

```
    float txX = in.texcoord.x;
    float txY = in.texcoord.y;
    int spritesPerRow = int(spriteSheet.textureWidth / spriteSheet.spriteWidth);
    int spriteX = textureId % spritesPerRow;
    int spriteY = textureId / spritesPerRow;
    float txOffsetX = spriteSheet.spriteWidth / spriteSheet.textureWidth;
    float txOffsetY = spriteSheet.spriteHeight / spriteSheet.textureHeight;
    if (txX == 1.0) {
        txX = txOffsetX + txOffsetX * spriteX;
    } else if (txX == 0.0) {
        txX = txOffsetX * spriteX;
    }
    if (txY == 1.0) {
        txY = txOffsetY + txOffsetY * spriteY;
    } else if (txY == 0.0) {
        txY = txOffsetY * spriteY;
    }

    VertexOutOnlyPositionAndUv vertex_out {
        .position = finalTransform * float4(in.position, 1),
        .uv = float2(txX, txY)
    };
```

Now the uv coordinates for each vertex are adjusted to show only the part of the sprite sheet needed for the current animation frame.



![Image of a lo-res game with a magic wand showing](https://res.cloudinary.com/demmholkv/image/upload/v1659581153/blog/sprite-sheet-wand_i7svzt.png "Wand rendered in game with a sprite sheet")