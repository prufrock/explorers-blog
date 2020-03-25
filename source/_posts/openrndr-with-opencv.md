---
extends: _layouts.post
section: content
title: Use OpenCV with OpenRNDR
date: 2020-03-24
description: Make OpenCV available with the OpenRNDR template
cover_image: /assets/img/3-ink-bottles.svg
excerpt: Now you're OpenRNDR projects can see things!
categories: [kotlin]
author: David Kanenwisher
---

If you're like me you probably got started with [OpenRNDR](https://openrndr.org/) by using their [template](https://github.com/openrndr/openrndr-template). Then you start fiddling with it and hooked up a camera or a kinect. At which point you may have wondered how in the world you do something with the pictures or video you're getting.

A quick search and you found yourself reading about OpenCV and scratching your head. At which point you may have thought, "How in the heck do I add this to my project?"

You proceed to do some more searches. After a bit of that you're pretty sure OpenCV isn't meant for you.

Friend, let me tell you something, you too can use OpenCV in your OpenRNDR project!

First, you'll need to compile OpenCV. Sure, it sounds daunting. I think my article on [compiling OpenCV for Java](blog/java-build-opencv) can help. Go take a look.

Whew, did you get through it? It's quite a trip but now you should have the essential components you need to add OpenCV to your OpenRNDR project.

The other day I went to bring OpenCV [OpenCV](https://opencv.org/) into an [OpenRNDR](https://openrndr.org/) project. I soon discovered there was more to it than I first thought.
