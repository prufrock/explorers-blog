---
extends: _layouts.post
section: content
title: Kotlin Folder Got To Have It
date: 2020-03-31
description: I ran into some trouble tonight when I didn't make a kotlin folder.
cover_image: /assets/img/4-ink-bottles.svg
excerpt: I thought it was just a part of Gradle tutorial. Who knew you needed it?
categories: [kotlin]
author: David Kanenwisher
---

I wanted to make a simple Kotlin project that I could use to try out different bits of code I found when reading other peoples code. I figured I'd just use [Gradle tutorial](https://guides.gradle.org/building-kotlin-jvm-libraries/) to get me started but with a few tweaks. Namely, I wanted to use my own folder name `org/example` and remove the `kotlin` folder since I figured it was part of the tutorial. It turns out I was wrong.

I created a project with this structure:

```text
$ tree
.
├── build.gradle.kts
└── src
    ├── main
    │   └── com
    │       └── dkanen
    │           └── MyLibrary.kt
    └── test
        └── com
            └── dkanen
                └── MyLibraryTest.kt
```

Notice the lack of a `kotlin` folder.

I set my Gradle file to look like the one in the tutorial up to the part about adding support for tests:

```text
plugins {
    kotlin("jvm") version "1.3.61" 
}

repositories {
    jcenter() 
}

dependencies {
    implementation(kotlin("stdlib"))
    testImplementation("junit:junit:4.12")
}
```

I ran `gradle test` but the output was suspicious:

```text
 gradle test

BUILD SUCCESSFUL in 1s
```

Shouldn't there have been something about my tests? I tinkered for a bit to no avail.

I thought maybe my tests are running so I adjusted them to make them fail. I got the same output as before; something was clearly wrong at this point.

I loaded it up in Intellij. In there I found that Intellij was refusing to run the tests claiming, "No test events were received."

I was perplexed. I looked back at the examples and saw the `kotlin` folder. I figured it was worth a shot.

I adjusted my directory structure like so:

```text
.
├── build.gradle.kts
└── src
    ├── main
    │   └── kotlin
    │       └── com
    │           └── dkanen
    │               └── MyLibrary.kt
    └── test
        └── kotlin
            └── com
                └── dkanen
                    └── MyLibraryTest.kt
```

Notice the existence of the `kotlin` folder!

Now when I run with my failing test I get:

```text
$ gradle test

> Task :test FAILED

com.dkanen.MyLibraryTest > testMyLanguage FAILED
    java.lang.AssertionError at MyLibraryTest.kt:9

1 test completed, 1 failed

FAILURE: Build failed with an exception.

* What went wrong:
Execution failed for task ':test'.
> There were failing tests. See the report at: file:///Users/davidkanenwisher/IntelliJProjects/folder-trouble/build/reports/tests/test/index.html

* Try:
Run with --stacktrace option to get the stack trace. Run with --info or --debug option to get more log output. Run with --scan to get full insights.

* Get more help at https://help.gradle.org

BUILD FAILED in 1s
3 actionable tasks: 3 executed
```

Yay! It works now.

What was the last pesky problem you ran into getting Kotlin to work in Gradle? Let me know on the [contact page](/contact).