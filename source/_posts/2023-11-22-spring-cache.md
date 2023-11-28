---
extends: _layouts.post
section: content
title: Spring Cache
date: 2023-11-28
description: Output is weird when point size isn't set...build
cover_image: 
excerpt: The other day I’m trying to get the cache in Spring to cache a value.
categories: [kotlin]
author: David Kanenwisher
---

The other day I’m trying to get the cache in Spring to cache a value. It isn’t anything complicated I’m just trying to store some values with the annotations. I set it up, so I can see whether functions are called that shouldn’t be if the cache is hit. Frustratingly, the functions keep getting called!

I try a bunch of different things. I explicitly declare a cache manager. I fiddle with the annotations; adding keys, cache manager names, variables. I define a factory method so the ApplicationContext can inject the class I’m trying to cache. I can’t get any of it to work.

It’s getting to the end of the day here. I’ve got to get dinner started soon. I move a bunch of stuff around and use `@Autowired` to inject the service into the controller. Suddenly, there are values in the cache. Why wasn’t it working before!?

I move a few more annotations around and clean up a bunch of the junk I added. I reduce it down to the simplest possible and practically the same as the documentation. It works! I simply had to ensure the `ApplicationContext` injected the service using the cache rather than instantiating it directly.

Today’s lesson: When in doubt `@Autowire`. 