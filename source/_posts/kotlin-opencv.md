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

I prefer Gradle with Kotlin script so I'll be using that for this guide.

Lets start by essentially following Gradle's [Building Kotlin JVM Libraries](https://guides.gradle.org/building-kotlin-jvm-libraries/) guide.

Create a project directory from the shell:
```shell script
$ mkdir -p example-opencv
$ cd example-opencv
```

Then create the project with gradle:
```shell script
$ gradle init
```
When prompted choose these options:
* Select type of project to generate 2: application
* Select implementation language 4: Kotlin
* Select build script DSL 2: Kotlin
* Project name (default: opencv-start): example-opencv
* Source package (default: opencv.start): example-opencv

Then add some controls on the JDK version to the top:
```text
import org.jetbrains.kotlin.gradle.tasks.KotlinCompile

configure<JavaPluginConvention> {
    sourceCompatibility = JavaVersion.VERSION_11
}
tasks.withType<KotlinCompile> {
    kotlinOptions.jvmTarget = "11"
}
```
This makes it easier when we have to set the version to compile OpenCV against.
