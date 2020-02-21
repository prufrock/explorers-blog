---
extends: _layouts.post
section: content
title: First Attempt At Collections
date:  2020-02-19
description: How I started using Kotlin's list and determined how to iterate over it. 
cover_image: /assets/img/4-squares.svg
excerpt: Currently, the Dispatcher only supports a subscriber so the first assertion failed since the first subscriber was replaced by the second. How was I going to create a list of subscribers inside of the Dispatcher?
categories: [kotlin]
author: David Kanenwisher
---

I need to figure out someway to satisfy my new test which checked to see if two different functions could subscribe to and receive an event from the Broadcaster. Up to this point, in Test Drive Development(TDD) fashion, my tests had allowed me to get away with sending an event to only one subscriber. I wanted to get this functionality into the Broadcaster so I added this test:

```kotlin
    @Test
    fun `more than one Subscriber can receive a broadcast`() {
        val dispatcher = Dispatcher()

        var firstValue = ""

        dispatcher.subscribe { event -> firstValue = event }

        var secondValue = ""

        dispatcher.subscribe { event -> secondValue = event }

        dispatcher.broadcast("event")


        assertEquals("event", firstValue)
        assertEquals("event", secondValue)
    }
```

Currently, the Dispatcher only supports a subscriber so the first assertion failed since the first subscriber was replaced by the second. How was I going to create a list of subscribers inside of the Dispatcher? I'd seen a few things about lists in the [Big Nerd Ranch Kotlin book](https://www.bignerdranch.com/books/kotlin-programming-the-big-nerd-ranch-guide-2/) and in the [Kotlin documentation](https://kotlinlang.org/docs/reference/collections-overview.html). With that and the knowledge I'd won so far I had a place to start. 

I began by creating a `subscriberList` with the type `(String) -> Unit` to match the type of the lambdas I knew I wanted to stuff in there. My previous experience with accepting a lambda as a parameter and assigning into a variable had prepared for this part. I'd seen the carrot syntax `<Type>` in documentation so figured that'd be the way to go. One last thing I ran into trouble with was Kotlin wouldn't let leave `subscriberList` null so I had to initialize it with `listOf()` something I saw in the Big Nerd Ranch book. I stuck all this together and got a compiling `subcriberList` declaration.

```kotlin
    var subscriberList: List<(String) -> Unit> = listOf()
```

Cool. Making progress. Now I needed to add a subscriber to my list. I ran into a problem pretty quick. I started updating `fun subscribe` with `subscriberList`. I put the `.` at end of the variable so I could call a method to add an element to the list. I figured I could autocomplete my way to the problem. I typed "a". Didn't see anything. I typed "add". Nothing. I switched to "app" hoping for "append". Nothing. What was going on here? Why weren't there any methods to add something to my list. A quick peak back in the Big Nerd Ranch book and I realize I need a mutable list!

```kotlin
    var subscriberList: MutableList<(String) -> Unit> = mutableListOf()
```

Now I have an "add" method on my list. I put that in place like so:

```kotlin
    fun subscribe(subscriberFunction: (String) -> Unit) {
        subscriber = subscriberFunction
        subscriberList.add(subscriber)
    }
```

One more thing to do. When I broadcast an event it needs to go to all of the subscribers in `subscriberList`. How do I iterate over this thing? I always liked using `map` in other languages when I want to loop over the set with a succinct lambda. I give that go here:

```kotlin
    fun broadcast(event: String) {
        subscriber(event)
        subscriberList.map { subscriber -> subscriber(event)}
    }
```

I know there are some other ways to iterate over a list so I give those a shot out of curiosity.

There's `for` which reminds of PHP's [foreach](https://www.php.net/manual/en/control-structures.foreach.php):

```kotlin
    fun broadcast(event: String) {
        subscriber(event)
        for (subscriber in subscriberList) {
            subscriber(event)
        }
    }
```

Then there's `forEach` which is a bit more functional:

```kotlin
        subscriberList.forEach { subscriber -> subscriber(event) }
```

You know what I think I might like that more. I find that I tend to think `map` implies the list is going to be modified by the loop. The [Kotlin documentation](https://kotlinlang.org/docs/reference/collection-transformations.html) has this to say about `map`:

> The mapping transformation creates a collection from the results of a function on the elements of another collection.

The word transformation there stands out to me. I'm not really transforming the collection and I also don't need the result of that transformation. `forEach` seems much more appropriate. The __semantics__ of `forEach` fit the situation better which means I'll be able to think about the code faster when I come back to it.

There's one last bit of syntactical sugar I'd like to try `it`. I've see it a bunch of times in various discussions of how lambdas work on collections. I think I can use `it` here it short up this code just a smidgen:

```kotin
    fun broadcast(event: String) {
        subscriber(event)
        subscriberList.forEach { it(event) }
    }
```

It works! With `it` I don't need to declare the element I'm work on as a `subscriber`. Instead Kotlin just assumes the variable is `it`. Kotlin knows, from the type information, that `subscriberList` holds lambdas. Therefore anything coming out of `subscribeList` can be called like a function. From that `it` can be called as a function. Pretty neat!

Here's how it all came together*:
```kotlin
package com.dkanen

class Dispatcher {

    val subscriberList: MutableList<(String) -> Unit> = mutableListOf()

    fun subscribe(subscriberFunction: (String) -> Unit) {
        subscriberList.add(subscriberFunction)
    }

    fun broadcast(event: String) {
        subscriberList.map { subscriber -> subscriber(event)}
    }

    fun subscribe(newSubscriber: Subscriber) {
        subscriberList.add({event -> newSubscriber.receive(event)})
    }
}
```
*You may have noticed in the examples above, except the last, I still had references to the old variables like `subscriber`. It has to do with the way I keep the methods working while I am refactoring. This keeps with test driven development(TDD). I'll write about that some more a different time.

What ways have you found to bring collections into your projects? Let me know by dropping me a line on the [contact page](/contact).