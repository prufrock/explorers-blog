---
extends: _layouts.post
section: content
title: Lambda Sans Return
date: 2020-02-17
description: I created a lambda in Kotlin the other day and was perplexed that I couldn't avoid giving it a return type.
cover_image: /assets/img/post-cover-image-2.png
excerpt: I created a lambda in Kotlin the other day and was perplexed that I couldn't avoid giving it a return type.
categories: [kotlin]
---

I created a lambda in Kotlin the other day and was perplexed that I couldn't avoid giving it a return type. Then I heard about `Unit`. Supposedly, I could return `Unit` and not have to worry about the return type on my lambdas. I don't know much about `Unit` but I had to see this in action.

I took some code I've been fiddling with, a sort of observer object that I call a Dispatcher. It accepts a lambda as way to receive an event from the Dispatcher. The test for it looks like so:

```kotlin
package com.dkanen

import kotlin.test.Test
import kotlin.test.*

class DispatcherTest {

    @Test
    fun `when a lambda is passed to subscribe it can receive a broadcast`() {
        val dispatcher = Dispatcher()

        var passedValue = ""
        val subscribeFunction: (String) -> String = { event ->
            passedValue = event
            passedValue
        }

        dispatcher.subscribe(subscribeFunction)
        dispatcher.broadcast("event")

        assertEquals("event", passedValue)
    }
}
```

Did you see the `subscribeFunction` that has a weired second line `passedValue`? Kotlin's lambda like to keep things short and omit the return statement for the last line. I need to have that strange line there because I need to satisfy the type signature and return a String.

The implementation of Dispatcher that makes the test pass:

```kotlin
package com.dkanen

class Dispatcher {

    var subscriber: (String) -> String = { "test"  }

    fun subscribe(subscriberFunction: (String) -> String) {
        subscriber = subscriberFunction
    }

    fun broadcast(event: String) {
        subscriber(event)
    }
}
```

Notice how the `var subscriber` has a type signature of `(String) -> String`. This means that the lambda has to take a String as an argument and return a String. I can make it return a String for the sake of making it work. I don't like it though because it suggests to the reader they should do something with that String. Now that I know about `Unit` I have what I need to rid my code of this confusion.

I go in and update Dispatcher:

```kotlin
package com.dkanen

class Dispatcher {

    var subscriber: (String) -> Unit = {}

    fun subscribe(subscriberFunction: (String) -> Unit) {
        subscriber = subscriberFunction
    }

    fun broadcast(event: String) {
        subscriber(event)
    }
}
```

Notice how subscriber type signature is now `(String) -> Unit`.

Then I update the test:

```kotlin
package com.dkanen

import kotlin.test.Test
import kotlin.test.*

class DispatcherTest {

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
}
```

Checkout how `subscribeFunction` matches the type signature of of Dispatcher's `subscribe` by removing the implied return. Awesome sauce.

I am a bit curious about `Unit` but that's a topic for a different day.

What cool ways are you finding to shorten your lambdas in Kotlin?
