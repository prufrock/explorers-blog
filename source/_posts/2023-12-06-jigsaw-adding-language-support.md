---
extends: _layouts.post
section: content
title: Syntax Highlighting in Jigsaw
date: 2023-12-06
description: New languages need to be added to main.js
cover_image:  
excerpt: New languages need to be added to main.js
categories: [jigsaw]
author: David Kanenwisher
---

Syntax highlighting in Jigsaw is done as you would expect. You create a Markdown code block and add the name of the language after the first set of backticks like so ` ```php `.

If you use a language like Kotlin that isn't supported out of the box by Jigsaw you need to register it in `source/_assets/js/main.js` like so:

```javascript
hljs.registerLanguage('kotlin', require('highlight.js/lib/languages/kotlin'));
```

After that's done all the rendered code blocks should have an `hljs` tag on them:
```html
<code class="language-kotlin hljs">
```