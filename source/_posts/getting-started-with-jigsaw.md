---
extends: _layouts.post
section: content
title: Getting Started With Jigsaw
date: 2020-02-13
description: You've got some ideas kicking around your heard for a blog. You know how to use Laravel pretty well and want to get to writing things down now! Jigsaw the Laravel based static site generator may be just what you need.
cover_image: /assets/img/post-cover-image-2.png
---

I pretty much followed the Jigsaw's [installation guide](https://jigsaw.tighten.co/docs/installation/) to get started. I'll throw in a quick summary here in case you don't want to have both tabs open:

First, create the project directory: `$ mkdir cool-blog`.

Second, install Jigsaw with composer:
```bash
$ cd cool-blog
$ composer require tightenco/jigsaw
```

Finally, initialize your project and make it a blog:
```
./vendor/bin/jigsaw init blog
```

Then if you want to take a look at what it looks like run this command:
```bash
$ npm run local
```

This creates a `build_local` directory that allows you to preview your site. Run this command to see what that looks like:
```bash
npm run watch
```
Run watch is great because it shows local changes as you make them.

If you want to change what shows up in the title and the base URL take a look at `config.php` and `config.production.php` and make some adjustments. 

If you want to see what your blog looks when it's released run this command:
```bash
$ npm run production
```

As for getting it deployed Jigsaw has a great guide for different targets:
[Deploying your site](https://jigsaw.tighten.co/docs/deploying-your-site/)

My target was a special system we use at Bushel that utilizes docker. I had commit the `build_production` folder to make it work like so:

```bash
git add -f build_production && git commit -m "Build for deploy"
```

I need the -f because the .gitignore file has an entry to ignore everything in the build directory `/build_*/`.

Then I created a Dockerfile that uses the `copy` command to copy it to the correct directory:
```
copy ./build_production/ /var/www/public/
```

Then I deployed it out to the server and you can see the result!

What are you going to write about in Jigsaw?