---
extends: _layouts.post
section: content
title: Sed Strikes
date: 2023-05-06
description: Sed strikes like lightening!
cover_image: 
excerpt: They all have a sequence of characters in them that prevents it from compiling.
categories: []
author: David Kanenwisher
---

There is a land full of files. Heaps and heaps of files. All of these files can be compiled into the greatest program ever known. There’s just one problem: They all have a sequence of characters in them that prevents it from compiling.

Our hero Shell has many weapons they can wield against these wicked sequences. Shell is cunning and crafty and knows many ways to slay such a foe as this. Today they choose `sed`. An ancient weapon known for its ability to manipulate sequences in powerful ways. In the land of macOS it is known as `gsed`. But there’s trouble, `gsed` can only attack one file at a time but there are legions of files.

Shell remembers a great spell, `xargs` , to be used when foes are numerous. With these two weapons Shell is nearly prepared to defeat these foul sequences. How will they seek them out? What oracle will point them towards the blight!

Surely `find` can track down each stain! Shell combines these three potent weapons. `gsed` to eradicate each blot, `xargs` to multiply `gsed` strength, and `find` to track the infestation down. With these the darkness is eradicated from the files and the program is finally able to compile!

The command as it was written:
```shell
find src/main/kotlin/com/dkanen \( -type d -name .kt -prune \) -o -type f -print0 | xargs -0 gsed -i 's/graphmapnav/rotationrumpus/'
```