---
extends: _layouts.post
section: content
title: No Observable Object
date: 2020-05-05
description: It's not always clear why views don't render in Xcode's preview.
cover_image: /assets/img/type-inference.svg
excerpt: I have no idea what the ultraviolet service is, what it means that the code is 12, and it seems pretty clear the "Rendering service was interrupted". 
categories: [SwiftUI]
author: David Kanenwisher
---

I ran into some trouble after I opened up the app I've been toying with in Xcode. The SwiftUI preview wouldn't render. I clicked "Diagnostics" and it said:

```text
|  Error Domain=com.apple.dt.ultraviolet.service Code=12 "Rendering service was interrupted" UserInfo={NSLocalizedDescription=Rendering service was interrupted}
```

I have no idea what the ultraviolet service is, what it means that the code is 12, and it seems pretty clear the "Rendering service was interrupted". I did manage to find some more information by looking real close.

Depending on the size of your screen, on mine it was rather small, there's a little "i" with a circle around after the crashed message. I clicked that then clicked "Show Crash Logs".

After scrolling through a bunch of information I don't understand I struck gold. I saw something I could understand:

```text
Fatal error: No ObservableObject of type AppData found. A View.environmentObject(_:) for AppData may be missing as an ancestor of this view.: file SwiftUI, line 0
```

Thankfully, I'd recently gained a higher understanding of ObservableObjects plus AppData is a name of a variable in my project. Thankfully and reasonably, AppData is an ObservableObject. How do I know it's an ObservableObject? Why I made it implement ObservableObject protocol:

```swift
import SwiftUI

class AppData: ObservableObject {
    @Published var imageCache = ImageCache()
}
```

So the fatal error seems to be telling me that AppData is missing from the current view. This was the preview for my ContentView so the first place I go is the SceneDelegate. It looks like that's passing AppData:

```swift
window.rootViewController = UIHostingController(rootView: contentView.environmentObject(appData))
```

"Ah but this is the preview!", I cry with a finger pointed firmly and vertically in the air.

I take a look at PreviewProvider in ContentView.

```swift
struct ContentView_Previews: PreviewProvider {
    static var previews: some View {
        ContentView()
    }
}
```

"Well there's your problem" points out the surly mechanic in my head.

If you are like the surly mechanic in my head then you likely noticed that `ContentView()` is bereft of a call to "environmentObject". This is the source of the fatal error reported earlier. A quick touch up and it's good to go:

```swift
struct ContentView_Previews: PreviewProvider {
    static var previews: some View {
        ContentView().environmentObject(AppData())
    }
}
```

That did the trick! I can see my exceptional preview.

What hard to decipher error messages are you getting from SwiftUI? Let me know by dropping me a line on the [contact page](/contact).