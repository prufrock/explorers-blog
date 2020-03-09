---
extends: _layouts.post
section: content
title: When Type Inference Doesn't Cut It
date: 2020-03-08
description: It turns out you can't always use Kotlin's type inference. There are times when you need to explicitly call out the type. 
cover_image: /assets/img/post-cover-image-2.png
excerpt: I thought I had this covered by creating the `Event` interface and making each implement this interface. No dice, I starting getting `Type mismatch` errors from Intellij.
categories: [kotlin]
author: David Kanenwisher
---

In order to learn Kotlin I've been working my way through a goofy program called "adventure-venture". I honestly don't have a clear idea of what it does but so far it has adventurers in a tavern that want to talk to each other. I'm also trying to understand event based systems so the adventurers interact with the world through events.

In the process of trying to get the adventurers to talk to each other I've been refactoring the way the system works. When I refactor I like to try and keep all of the tests passing as I advance forward with the refactoring. That way it's clear when I am changing behavior and when I am not. It's in this way that I discovered a situation where I can't rely on type inference.

I got my code to the point where I had these two tests:

```kotlin
    @Test
    fun `it can dispatch an empty event object`() {
        val dispatcher = Dispatcher()

        var passedValue = EmptyEvent()

        dispatcher.eventSubscribe { event -> passedValue = event }

        assertEquals("", passedValue.name)
    }

    @Test
    fun `it can dispatch an emitted event object`() {
        val dispatcher = Dispatcher()

        var passedValue = EmittedEvent("emittedSound")

        dispatcher.eventSubscribe { event -> passedValue = event }

        assertEquals("emittedSound", passedValue.name)
    }
```

Notice that in the first test I have `var passedValue = EmptyEvent()` and in the second test I have a similar line `var passedValue = EmittedEvent("emittedSound")`. Why not just leave them like this then? Well, they don't compile because `dispatcher.eventSubscribe` expects a lambda of type `(Event) -> Unit`. I thought I had this covered by creating the `Event` interface and making each implement this interface. No dice, I starting getting `Type mismatch` errors from Intellij on this line:

```kotlin
        dispatcher.eventSubscribe { event -> passedValue = event }
```

After staring at my code a bit a thought occurred to me. The type of `event` above is `Event` while the type of `passedValue` is either `EmptyEvent` or `EmittedEvent`. This means I must not be able to assign a more specific type, `EmptyEvent` or `EmittedEvent`, to a less specific type `Event`. This lead to me think I need to declare the type as `Event` so that `passedValue` can always hold an `Event`.

I added explicit type information to `passedValue` by changing it to `passedValue: Event` like so:

```kotlin
    @Test
    fun `it can dispatch an empty event object`() {
        val dispatcher = Dispatcher()

        var passedValue: Event = EmptyEvent()

        dispatcher.eventSubscribe { event -> passedValue = event }

        assertEquals("", passedValue.name)
    }

    @Test
    fun `it can dispatch an emitted event object`() {
        val dispatcher = Dispatcher()

        var passedValue: Event = EmittedEvent("emittedSound")

        dispatcher.eventSubscribe { event -> passedValue = event }

        assertEquals("emittedSound", passedValue.name)
    }
```

That did the trick and my tests start passing. I do wonder if generics could help out here but I haven't played with them much yet.

How about you? Let me know if you've learned anything interesting about Kotlin's type inference by dropping me a line on the [contact page](/contact).