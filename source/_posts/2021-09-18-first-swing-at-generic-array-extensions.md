---
extends: _layouts.post
section: content
title: Swift First Swing at Generic Array Extension
date: 2021-09-18
description: Leveling up my understanding of generic extensions on Arrays
excerpt: When you need to create a generic for `Array` you may either need `Sequence` or `Collection` or just `Array`. I might lack some understanding here because it seems trickier to understand which to use then I expect it to be.
categories: [swift]
author: David Kanenwisher
---

When you need to create a generic for `Array` you may either need `Sequence` or `Collection` or just `Array`. I might lack some understanding here because it seems trickier to understand which to use then I expect it to be.

With this I made a somewhat unnecessary extension because after I made it I found `replaceSubrange`. Either way though I figured out how to make a generic extension on `Array` that returns an `Array` containing the type that the `Array` holds with the predefined type `Element`.
```swift
extension Array {
   func replace(index i: Int, element: Element)  -> [Element] {
       Array(self[0 ..< i]) + [element] + Array(self[(i+1) ..< count])
   }
}
```

While digging around in the code I found this extension on `Array` from `Collection.Array.swift` jumped out at me:
```swift
extension ContiguousArray {
    @inlinable public mutating func replaceSubrange<C>(
        _ subrange: Range<Int>,
        with newElements: C
    ) where Element == C.Element, C : Collection
}
```
It receives a generic `Collection` of type `C` and specifies that the elements of `C` are of type `C.Element`. This allows the caller to tell the compiler the type at the call site and for Swift to continue to know the type.  