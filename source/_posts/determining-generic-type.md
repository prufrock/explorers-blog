---
extends: _layouts.post
section: content
title: Determining Generic Type
date: 2021-09-15
description: Generics need a type to know their type.
cover_image: /assets/img/type-inference.svg
excerpt: 
categories: [kotlin]
author: David Kanenwisher
---

Generics need a way to know the generic type it's working. This seems obvious when you say but building an API that
reasonably makes this happen can be tricky.

The coroutine function `async` does this by accepting a lambda. The return type of the lambda ends up becoming the return type of the generic. This is more clever than I initially realized. Let's take a look at usage:

```kotlin
    @Test
    fun `runBlocking returns Deferred T`() {
        runBlocking {
            val deferred = async {
                listOf<String>("volcano sword")
            }
            val result = deferred.await()
            assertEquals("volcano sword", result[0])
        }
    }
```

You can see in the example above that I pass a lambda(the bare mustache braces) with an implicit return of `List<String>`. This allows async to create a `Deferred<T>` object with a type of `List<String>` by declaring that `T` is the return type of the lambda. There's a lot happening in teh definition of `async` but if you follow the generic type `T` you can see how this is happening"

```kotlin
//file commonMain/Builders.common.kt
package kotlinx.coroutines

//...skip a bunch of stuff

public fun <T> CoroutineScope.async(
    context: CoroutineContext = EmptyCoroutineContext,
    start: CoroutineStart = CoroutineStart.DEFAULT,
    block: suspend CoroutineScope.() -> T
): Deferred<T> {
    val newContext = newCoroutineContext(context)
    val coroutine = if (start.isLazy)
        LazyDeferredCoroutine(newContext, block) else
        DeferredCoroutine<T>(newContext, active = true)
    coroutine.start(start, coroutine, block)
    return coroutine
}
```

The type of the result of a lambda passed into a generic is one way to determine the generic of the function. One more nugget of knowledge on the path to understanding how to build generics into your projects.