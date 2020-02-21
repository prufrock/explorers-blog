---
extends: _layouts.post
section: content
title: Single Expression Functions
date: 2020-02-13
description: I recently learned about single expression functions and am amazed at how simple the function you can created with them are.
cover_image: /assets/img/post-cover-image-2.png
excerpt: Many of the functions in the Kotlin standard library are implemented as Single Expression Functions. I started wondering how I could use them in my own code.
categories: [kotlin]
author: David Kanenwisher
---

I was listening to Talking Kotlin [Kotlin Cookbook](https://pca.st/09ybe338). The author of the Kotlin Cookbook, Ken Kousen, mentioned that one of the features he really likes about Kotlin are single expression functions. He even pointed out how cool it was that many of the functions in the Kotlin standard library are implemented as Single Expression Functions. At this point my curiosity had piqued and I had to know more.

I did some rapid googling and found myself staring at Programming Kotlin by Stephen Samuel and Stefan Bocutiu. There was a very short chapter in there on Single Expression Functions but after staring at it for a few minutes I had to try it out.

I've got this dirt simple class that I was able to change from this:
```kotlin
com.dkanen

class Adventurer(val name: String) {
    
    var location: Int = 0

    fun walk(): Int {
        return (++location)
    }

    fun talk(): String {
        return "You talk to no one in particular."
    }

    fun listen(): String {
        return "There is no sound to hear."
    }

    fun talk(listener: Adventurer): String {
       return "Talking to yourself may be a sign of genius"
    }

    fun heard(): String {
       return "How fair the beets at this establishment?"
    }

}
```

To this:
```kotlin
com.dkanen

class Adventurer(val name: String) {

    var location: Int = 0

    fun walk(): Int = (++location)

    fun talk(): String = "You talk to no one in particular."

    fun listen(): String = "There is no sound to hear."

    fun talk(listener: Adventurer): String = "Talking to yourself may be a sign of genius"

    fun heard(): String = "How fair the beets at this establishment?"

}
```

Do you see the difference? Pretty neat eh?

What do you think you could use Single Expression Functions to simplify?