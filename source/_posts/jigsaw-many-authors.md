---
extends: _layouts.post
section: content
title: Support Many Authors in Jigsaw
date: 2020-02-21
description: A quick guide on how to support more than one author in Jigsaw.
cover_image: /assets/img/3-ink-bottles.svg
excerpt: If you were like me and found "siteAuthor" in config.php and assumed Jigsaw only supported one author, then, my friend, you've come to the right place.
categories: [jigsaw]
author: David Kanenwisher
---

If you were like me and found `siteAuthor` in config.php and assumed Jigsaw only supported one author, then, my friend, you've come to the right place. The process of adding more than one author isn't too difficult you just have to know it's possible and where to make the necessary tweaks.

When you first install Jigsaw you'll have a `config.php` that starts with a section that looks like this:

config.php
```text
    'baseUrl' => '',
    'production' => false,
    'siteName' => 'Blog Starter Template',
    'siteDescription' => 'Generate an elegant blog with Jigsaw',
    'siteAuthor' => 'Author Name',

    // collections
    'collections' => [
        'posts' => [
            'author' => 'Author Name', // Default author, if not provided in a post
            'sort' => '-date',
            'path' => 'blog/{filename}',
        ],
        'categories' => [
            'path' => '/blog/categories/{filename}',
            'posts' => function ($page, $allPosts) {
                return $allPosts->filter(function ($post) use ($page) {
                    return $post->categories ? in_array($page->getFilename(), $post->categories, true) : false;
                });
            },
        ],
    ],

```

Now `siteAuthor` struck me as sounding like a pretty big deal and I, incorrectly, assumed it was meant to imply the site could only be authored by one person. Further down there is a `collections->posts->author` which I figured set the author on all of the posts since I didn't see an `author` in example posts.

It turns out that `siteAuthor` is only used in `source/_layouts/rss.blade.php` out of the box. That may have change in your Jigsaw blog so it'd be worth searching the `source` directory for `siteAuthor`. In `rss.blade.php` `siteAuthor` sets the author of the blog. This means if you didn't want there to be only one author you can set it to something more general like `Engineering Blog` or `Cosmetology Blog`. Then either all of your engineer friends or cosmetology friends could blog there.

The `collections->posts->author` in `config.php` is the author that's used when no author is specified on a post. That begs the question: How do you specify an author on a post? It's pretty straightforward. When you make a post add an `author` attribute to the meta information at the top like so:

meta information with author at the top of a post:
```text
---
extends: _layouts.post
section: content
title: Support Many Authors in Jigsaw
date: 2020-02-21
description: A quick guide on how to support more than one author in Jigsaw.
cover_image: /assets/img/3-ink-bottles.svg
excerpt: If you were like me and found "siteAuthor" in config.php and assumed Jigsaw only supported one author, then, my friend, you've come to the right place.
categories: [jigsaw]
author: David Kanenwisher
---
```

Now that I know how to do it I feel a bit silly. It's a feeling I get quite often after I learn something new. What surprising simply but useful thing have you learned recently? Let me know by dropping me a line on the [contact page](/contact).