---
extends: _layouts.post
section: content
title: Spring Cache Quick Guide
date: 2023-12-06
description: A short guide to using Spring Cache
cover_image: 
excerpt: Spring Cache is simple but still has a few tricks
categories: [kotlin]
author: David Kanenwisher
---

The caching in Spring is managed by annotations.

* The CacheManager needs to be configured with the names of the caches.
* Need to make sure classes using the annotations are injected so the ApplicationContext can supply the configured CacheManager.

```kotlin
@Configuration
@EnableCaching
class CachingConfig {
    @Bean
    fun cacheManager(): CacheManager {
        return ConcurrentMapCacheManager("skill", "skillList")
    }
}
```
A cache manager with two caches configured: skill and skillList.

```kotlin
package com.example.servingwebcontent.services.skills

import org.springframework.cache.annotation.CacheEvict
import org.springframework.cache.annotation.Cacheable
import org.springframework.data.repository.findByIdOrNull
import org.springframework.stereotype.Service

@Service
class SkillService(val skillRepository: SkillRepository) {

    @Cacheable("skill")
    fun findById(id: Long): Skill? {
        println("findById cache miss")
        return skillRepository.findByIdOrNull(id)
    }

    @Cacheable("skill")
    fun findAll(): Iterable<Skill> = skillRepository.findAll()

    @CacheEvict("skill", allEntries=true)
    fun saveSkill(skill: Skill): Skill = skillRepository.save(skill)
}
```
The `SkillService` stores the results of the queries `findById` and `findAll` in the cache and evicts all the entries whenever `saveSkill` is called.


```kotlin
@Cacheable("skill", key = "#id", unless = "#result == null")
fun findById(id: Long): Skill? {
    println("findById cache miss")
    return skillRepository.findByIdOrNull(id)
}
```
Cache with the id passed unless the result is null.


```kotlin
package com.example.servingwebcontent.services.skills

import org.springframework.cache.annotation.CacheEvict
import org.springframework.cache.annotation.CachePut
import org.springframework.cache.annotation.Cacheable
import org.springframework.cache.annotation.Caching
import org.springframework.data.repository.findByIdOrNull
import org.springframework.stereotype.Service

@Service
class SkillService(val skillRepository: SkillRepository) {

    @Cacheable("skill", key = "#id", unless = "#result == null")
    fun findById(id: Long): Skill? {
        return skillRepository.findByIdOrNull(id)
    }

    @Cacheable("skillList", key = "#userId", unless = "#result.empty")
    fun findAll(userId: Long): Iterable<Skill> {
        return skillRepository.findAll()
    }

    @Caching(
        evict = [
            CacheEvict("skillList", allEntries = true)
        ],
        put = [
            CachePut("skill", key = "#result.id")
        ]
    )

    fun saveSkill(skill: Skill): Skill = skillRepository.save(skill)
}
```

* Uses a `skill` and a `skillList` cache
* `findById` uses the `skill` cache based on the `id` passed in and stores the value unless it’s null.
* `findAll` uses the `skillList` cache based on the on `userId` passed in and stores the value unless it’s empty(expecting a list).
* `saveSkill` uses `@Caching` to operate on both caches. It evicts all entries in `skillList` and updates the entry for the saved skill based on the id in the result.

## Troubleshooting
Cache logging is super helpful when things aren’t getting cached how you expect. Remember to read the whole line because it is very verbose. I misunderstood it for a while because I assumed I knew which cache it was working on.

Log Spring Cache output
```text
logging.level.org.springframework.cache=TRACE
```

Injecting the CacheManager to see what’s actually stored is a good way to see what the keys look like and how the cache is changing over time:
```kotlin
@Autowired
private lateinit var cacheManager: CacheManager
```
