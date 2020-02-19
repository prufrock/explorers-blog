---
extends: _layouts.post
section: content
title: Shorthand Function Syntax
date: 2020-02-18
description: Save yourself a some typing(and reading) with Kotlin's shorthand function syntax.
excerpt: I'd seen some goofy syntax referred to in Kotlin posts and books and today I figured out how to use it!
---

I'd seen some goofy syntax referred to in Kotlin posts and books and today I figured out how to use it!

I had some code that looked like this:

```kotlin
    @Test
    fun `when a lambda is passed to subscribe it can receive a broadcast`() {
        val dispatcher = Dispatcher()

        var passedValue = ""
        val subscribeFunction: (String) -> Unit = { event ->
            passedValue = event
        }

        dispatcher.subscribe(subscribeFunction)
        dispatcher.broadcast("event")

        assertEquals("event", passedValue)
    }
```

The whole thing with `(String) ->Unit` didn't seem necessary. It seems like the type should be inferred. Do I even need `subscribeFunction`? I was pretty sure I'd seen something in the Big Nerd Ranch Kotlin book about a simple syntax for functions. I started clicking through it. After a few trips around the same chapters I found a section title "Shorthand syntax"! That's it! that's what I'm looking for!

Ecstatic, I flipped back to my editor and updated my code to look like so:

```kotlin
    @Test
    fun `when a lambda is passed to subscribe it can receive a broadcast`() {
        val dispatcher = Dispatcher()

        var passedValue = ""

        dispatcher.subscribe { event -> passedValue = event }
        dispatcher.broadcast("event")

        assertEquals("event", passedValue)
    }
```

Notice how I was able to get rid of the function's type signature `(String) -> Unit` and remove the variable `subscribeFunction`. All I need is the simple block `{ event -> passedValue = event }`.

What a day my friends! What. A. Day.

What nifty uses have you come up with for the shorthand syntax for functions? Let me know by dropping me a line on the [contact page](/contact).