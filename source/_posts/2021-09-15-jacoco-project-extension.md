---
extends: _layouts.post
section: content
title: JaCoCo Doesn't Like New Version of Kotlin
date: 2021-09-15
description: Ran into trouble after upgrading to new version of Kotlin
cover_image: /assets/img/type-inference.svg
excerpt: I recently ran into a spot of trouble with JaCoCo after upgrading to a new version of Kotlin.
categories: [kotlin, gradle]
author: David Kanenwisher
---

 I recently ran into a spot of trouble with JaCoCo after upgrading to a new version of Kotlin. The project was on Kotlin `1.3.72` and I bumped it all the way to `1.5.30`. I was a bit surprised that this all seemed to go smoothly until JaCoCo kicked out a weird error at me:
 
```text
Unexpected SMAP line: *S
```

What?! I wasn't sure what to make of that. Thankfully a google search turned up a [github issue](https://github.com/jacoco/jacoco/issues/1187). Basically, I needed to update my version of JaCoCo to `0.8.7`. This seemed simple enough until I found myself digging around in our various gradle.build files unable to find a `dependencies` block with JaCoCo in it. All I could find was the JaCoCo plugin being applied: `apply plugin: 'jacoco'`.

After the significance of it being a plugin sunk in I google for "jacoco plugin" and ended on Gradle's [JaCoCo plugin page](https://docs.gradle.org/current/userguide/jacoco_plugin.html). This show a helpful little block:

```text
jacoco {
    toolVersion = "0.8.7"
}
```

I put this in my build.gradle at the project level(or not nested inside any mustaches since that implies the project) and my build worked again! Hooray!

What is the JaCoCo plugin using that allows it to set a property at the project level though? [Project Extensions](https://docs.gradle.org/current/userguide/custom_plugins.html#sec:mapping_extension_properties_to_task_properties)! These allow you to give the power to the user of the build script to configure tasks. Mind Blown.