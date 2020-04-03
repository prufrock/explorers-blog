---
extends: _layouts.post
section: content
title: Use OpenCV with OpenRNDR
date: 2020-04-02
description: Make OpenCV available with the OpenRNDR template
cover_image: /assets/img/3-ink-bottles.svg
excerpt: Friend, let me tell you something, you too can use OpenCV in your OpenRNDR project!
categories: [kotlin]
author: David Kanenwisher
---

If you're like me you probably got started with [OpenRNDR](https://openrndr.org/) by using their [template](https://github.com/openrndr/openrndr-template). Then you start fiddling with it and hooked up a camera or a kinect. At which point you may have wondered how in the world you do something with the pictures or video you're getting.

A quick search and you found yourself reading about OpenCV and scratching your head. At which point you may have thought, "How in the heck do I add this to my project?"

You proceed to do some more searches. After a bit of that you're pretty sure OpenCV isn't meant for you.

Friend, let me tell you something, you too can use OpenCV in your OpenRNDR project!

First, you'll need to compile OpenCV. Sure, it sounds daunting. I think my article on [compiling OpenCV for Java](blog/java-build-opencv) can help. Go take a look.

Whew, did you get through it? It's quite a trip but now you should have the essential components you need to add OpenCV to your OpenRNDR project:

1. The OpenCV jar.
2. The compiled native libraries.

Let's go ahead and add OpenCV to OpenRNDR. I'm going to assume you're using some flavor of [Intellij IDEA](https://www.jetbrains.com/idea/download/). If not, you may be able to adapt what I'm doing here to your IDE.

Let's start with the `build.gradle.kts`. We're going to need to do a couple things in this file:

1. Add dependency on the OpenCV jar.
2. Update the version of Java to the one you built the OpenCV jar against.

For the first step you'll need the path to the OpenCV jar you built. For me that's `/Users/davidkanenwisher/Projects/build_opencv-349/bin/opencv-349.jar`.

Now open `build.gradle.kts`. Find `dependencies` and inside the curly braces `{}` add the line `implementation(files("/Users/davidkanenwisher/Projects/build_opencv-349/bin/opencv-349.jar"))`. Make sure to replace my path with your path. You're `dependencies` section should look something like this:

```text
dependencies {

    /*  This is where you add additional (third-party) dependencies */

//    implementation("org.jsoup:jsoup:1.12.2")
//    implementation("com.google.code.gson:gson:2.8.6")

    //<editor-fold desc="Managed dependencies">
    runtimeOnly(openrndr("gl3"))
    runtimeOnly(openrndrNatives("gl3"))
    implementation(openrndr("openal"))
    runtimeOnly(openrndrNatives("openal"))
    implementation(openrndr("core"))
    implementation(openrndr("svg"))
    implementation(openrndr("animatable"))
    implementation(openrndr("extensions"))
    implementation(openrndr("filter"))

    implementation("org.jetbrains.kotlinx", "kotlinx-coroutines-core","1.3.3")
    implementation("io.github.microutils", "kotlin-logging","1.7.8")

    when(applicationLogging) {
        Logging.NONE -> {
            runtimeOnly("org.slf4j","slf4j-nop","1.7.29")
        }
        Logging.SIMPLE -> {
            runtimeOnly("org.slf4j","slf4j-simple","1.7.29")
        }
        Logging.FULL -> {
            runtimeOnly("org.apache.logging.log4j", "log4j-slf4j-impl", "2.13.0")
            runtimeOnly("com.fasterxml.jackson.core", "jackson-databind", "2.10.1")
            runtimeOnly("com.fasterxml.jackson.dataformat", "jackson-dataformat-yaml", "2.10.1")
        }
    }

    if ("video" in openrndrFeatures) {
        implementation(openrndr("ffmpeg"))
        runtimeOnly(openrndrNatives("ffmpeg"))
    }

    if ("panel" in openrndrFeatures) {
        implementation("org.openrndr.panel:openrndr-panel:$panelVersion")
    }

    for (feature in orxFeatures) {
        implementation(orx(feature))
    }

    if ("orx-kinect-v1" in orxFeatures) {
        runtimeOnly(orxNatives("orx-kinect-v1"))
    }

    if ("orx-olive" in orxFeatures) {
        implementation("org.jetbrains.kotlin", "kotlin-scripting-compiler-embeddable")
    }

    implementation(kotlin("stdlib-jdk8"))
    testImplementation("junit", "junit", "4.12")
    //</editor-fold>

    implementation(files("/Users/davidkanenwisher/Projects/build_opencv-349/bin/opencv-349.jar"))
}
```

Mostly just pay attention to the last line. It's the one we added. If other parts are different it may be different versions of the template.

The second step you only need to do if didn't build OpenCV against Java 8. I built mine against Java 11. This meant I had to update these lines in `build.gradle.kts` to reflect that:

```text
configure<JavaPluginConvention> {
    sourceCompatibility = JavaVersion.VERSION_1_8
}
tasks.withType<KotlinCompile> {
    kotlinOptions.jvmTarget = "1.8"
}
```

I changed these from `JavaVersion.VERSION_1_8` to `JavaVersion.VERSION_11` and `1.8` to `11` like so:

```text
configure<JavaPluginConvention> {
    sourceCompatibility = JavaVersion.VERSION_11
}
tasks.withType<KotlinCompile> {
    kotlinOptions.jvmTarget = "11"
}
```

The last step before we get to see if it works. There's likely a better way to do this but it's the best I've come up with so far. In Intellij go to `File > Project Structure`. On there go to `Modules`. You'll see some folders with your project at the top if it's still named `openrndr-template` that's what you'll see otherwise it will be your project name. Click `main` below that.

At that point you should see in the list `opencv-349.jar`. Note the name may be different depending on the folder you built it in.  Double-click on that. Then a little window should pop up titled "Configure Module Library". Click the little bitty `+` button in the lower left corner.

A file navigator window should open. Navigate to the file where you compiled opencv and select `lib`. For me that is `/Users/davidkanenwisher/Projects/build_opencv-349/lib`. You should see "Native Library Locations" with the path to the directory you just added.

Now let's see if this puppy runs! Open `TemplateProgram.kt` and change it to:

```kotlin
import org.opencv.core.Core
import org.opencv.core.CvType
import org.opencv.core.Mat
import org.opencv.core.Scalar
import org.openrndr.application

fun main() = application {

    System.loadLibrary(Core.NATIVE_LIBRARY_NAME)

    print("Welcome to OpenCV " + Core.VERSION)

    println("Welcome to OpenCV " + Core.VERSION)
    val m = Mat(5, 10, CvType.CV_8UC1, Scalar(0.0))
    println("OpenCV Mat: $m")
    val mr1: Mat = m.row(1)
    mr1.setTo(Scalar(1.0))
    val mc5: Mat = m.col(5)
    mc5.setTo(Scalar(5.0))
    println(
        """
            OpenCV Mat data:
            ${m.dump()}
            """.trimIndent()
    )
}
```

Then click the little green arrow next to `main()` and you should get this output:

```text
Welcome to OpenCV 3.4.9Welcome to OpenCV 3.4.9
OpenCV Mat: Mat [ 5*10*CV_8UC1, isCont=true, isSubmat=false, nativeObj=0x7fa51864a6e0, dataAddr=0x7fa51864a740 ]
         OpenCV Mat data:
         [  0,   0,   0,   0,   0,   5,   0,   0,   0,   0;
1,   1,   1,   1,   1,   5,   1,   1,   1,   1;
0,   0,   0,   0,   0,   5,   0,   0,   0,   0;
0,   0,   0,   0,   0,   5,   0,   0,   0,   0;
0,   0,   0,   0,   0,   5,   0,   0,   0,   0]
```

I hope it all went well. You should be able to add face detection, edge detection, or whatever other neat OpenCV features you'd like to add to your project now! Let me know what you get up to with OpenRNDR and OpenCV by dropping me a line on the [contact page](/contact).