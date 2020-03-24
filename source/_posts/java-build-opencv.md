---
extends: _layouts.post
section: content
title: Build OpenCV for Java/Kotlin on macOS
date: 2020-03-23
description: Building OpenCV for Java can be a bit of a trip.
cover_image: /assets/img/3-ink-bottles.svg
excerpt: I soon discovered there was more to it than I thought.
categories: [kotlin]
author: David Kanenwisher
---

The other day I went to bring OpenCV [OpenCV](https://opencv.org/) into an [OpenRNDR](https://openrndr.org/) project. I soon discovered there was more to it than I first thought.

Let's start with a few housekeeping things:

1. Homebrew
2. Ant
3. SDKMAN

If you don't have Homebrew, you'll either need to get it or find another way to install Ant.

You need to have Ant to build OpenCV. You can check if it's installed on the command line by checking it's version:

```text
$ ant -v
Apache Ant(TM) version 1.10.7 compiled on September 1 2019
Trying the default build file: build.xml
Buildfile: build.xml does not exist!
Build failed
```

If you don't have Ant, I recommend installing it via homebrew like so:

```text
$ brew install ant
```

Do you already have SDKMAN? It's not absolutely necessary but it makes it easy to switch between versions and to know they are configured in such a way OpenCV's build process can find it. You can find the install steps here: [https://sdkman.io/install](https://sdkman.io/install).

SDKMAN doesn't have the official JDK in it but you can add in any you have installed. You can either place them in the SDKMAN directory `/Users/davidkanenwisher/.sdkman/candidates/java` or link them. I have my JDK installed in `/Library/Java/JavaVirtualMachines/jdk1.8.0_121.jdk/Contents/Home/
` so I linked it into SDKMAN like so:

```text
ln -s /Library/Java/JavaVirtualMachines/jdk1.8.0_121.jdk/Contents/Home/ /Users/davidkanenwisher/.sdkman/candidates/java/1.8.0_241-oracle
```

Lastly, lets install and use your preferred java version. I'm going with Java 11 open JDK. With SDKMAN this is pretty easy. In your terminal run this command and answer yes to "set as default":

```text
$ sdk install java 11.0.2-open
```

Feel free to use your preferred JDK though I don't know enough about OpenCV and Java to know if a certain JDK's won't work.

Let's get down to building OpenCV.

First, go to the OpenCV website and download the sources of the version you want to build: [https://opencv.org/releases/](https://opencv.org/releases/). I'm going with 4.2.0.

This unzips into the folder `opencv-4.2.0`. Don't switch into that directory yet! In the same directory as `opencv-4.2.0` create a folder `build_opencv-420` and switch into it `build_opencv-420`:

```text
$ mkdir build_opencv-420
$ cd build_opencv-420
```

Before you can build OpenCV you need configure and create the cmake file:

```text
$ cmake -DBUILD_SHARED_LIBS=OFF -DCMAKE_BUILD_TYPE=Release -DBUILD_EXAMPLES=ON -DBUILD_opencv_java=ON ../opencv-4.2.0 &> output.txt
```

Now take a peak at `output.txt` and check to see if Java support is going to be built. In there you should see:

```text
--     To be built:                 calib3d core dnn features2d flann gapi highgui imgcodecs imgproc java ml objdetect photo python3 stitching ts video videoio
```

```text
--   Java:
--     ant:                         /usr/local/bin/ant (ver 1.10.7)
--     JNI:                         /Users/davidkanenwisher/.sdkman/candidates/java/current/include /Users/davidkanenwisher/.sdkman/candidates/java/current/include/darwin /Users/davidkanenwisher/.sdkman/candidates/java/current/include
--     Java wrappers:               YES
--     Java tests:                  YES
```

Ok, now it's time to build OpenCV. Your poor computer is going to have to work pretty hard to make this happen so make sure you can live without it for an hour or so depending on your hardware. When you're ready run this command:

```text
$ make -j8
``` 

If all goes well you should get the message:
```text
BUILD SUCCESSFUL
```

Amongst a whole bunch of other files you should have:

```text
bin/opencv-420.jar
lib/libopencv_java420.dylib
```

The first, `opencv-420.jar`, is the jar file you'll need to import into your project.

The second, `libopencv_java420.dylib`, is the natively compiled library that you'll need to point to when you run your application.

Now that you have OpenCV ready to run in your Java/Kotlin application what awesome uses are you going to put it to? Send me a message on the [contact page](/contact).