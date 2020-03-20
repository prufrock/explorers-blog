---
extends: _layouts.post
section: content
title: Starting Up Kotlin and OpenCV
date: 2020-03-22
description: Where Kotlin and OpenCV show they are the best of friends.
cover_image: /assets/img/3-ink-bottles.svg
excerpt: There a lot a of hoops to jump to bridge the gap between Kotlin and OpenCV.
categories: [kotlin]
author: David Kanenwisher
---

I started playing with [OpenRNDR](https://openrndr.org/) after hearing about it on [Talking Kotlin](https://talkingkotlin.com/openrndr-with-edwin-jakobs/). I saw there was [Kinect](https://guide.openrndr.org/#/10_OPENRNDR_Extras/C02_Kinect) support. Once I had a connect I suddenly found myself want to use [OpenCV](https://opencv.org/). At which point I promptly feel mostly wonderfully into a deep, deep rabbit hole of getting OpenCV to work in Kotlin.

First, lets get a project started in Intellij. Open up Intellij and go to `File > Project`. From the menu that opens up choose `Kotlin > JVM | IDEA` and click `Next`.

In the `New Project` wizard set the project name to "kotlin-opencv". Set the `Project SDK` to `11`. If you want to use a different version you'll need to make sure to build OpenCV against that version. Then click `Finish` to create the project.