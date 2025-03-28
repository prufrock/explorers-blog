---
extends: _layouts.post
section: content
title: KMP Xcode says No definition found
date: 2025-03-25
description: Possible resolution to your No definition found issues.
cover_image: 
excerpt: This error indicates you may have forgotten to setup the binding for iOS for that class. If it's working for Android it's likely you defined the binding in `composeApp/androidMain` or even `shared/androidMain`.
categories: [kotlin]
author: David Kanenwisher
---

When working with Kotlin Multi-Platform and Koin you may run into the issue below when you try to run your iOS app.

```text
Uncaught Kotlin exception: org.koin.core.error.NoDefinitionFoundException: No definition found for type 'com.dkanen.kmpgqllogintwo.database.repositories.playerstate.PlayerStateRepositoryDatabase'. Check your Modules configuration and add missing type and/or qualifier!
```

This error indicates you may have forgotten to set up the binding for iOS for that class. If it's working for Android it's likely you defined the binding in `composeApp/androidMain` or even `shared/androidMain`.

If you need a distinct binding for iOS, then the simplest place to add that binding is in an `expect` function in the `shared` module. You might be able to do it in the iOS code in Swift, but it's likely not worth the hassle of figuring out all the conversions for the Koin module builder.

You could also share the binding between the two, if the dependencies are essentially the same or Koin can handle injecting platform dependencies. Let's start with the distinct binding, then finish with the shared binding.

You may already have some code like this. It's pretty common from the KMP starters and tutorials.

```kotlin
import org.koin.core.context.startKoin
import org.koin.core.module.Module
import org.koin.dsl.KoinAppDeclaration
import org.koin.dsl.module

# platformModule each platform implements
expect fun platformModule(): Module

# initialize Koin
fun initKoin(extraModules: List<Module>, appDeclaration: KoinAppDeclaration = {}) {
    startKoin {
        appDeclaration()
        modules(
            // Expand all the extra modules passed in.
            *extraModules.toTypedArray(),
            platformModule(),
        )
    }
}
```

This code uses `platformModule()` to allow each platform to specify platform-specific dependencies.

In the iOS implementation of `platformModule()` you bind the class with any platform-specific dependencies.

```kotlin
actual fun platformModule() = module {
    single {
        val driver = createSqlDriver()
        val wrapper = AppDatabaseWrapper(Database(driver))
        
        PlayerStateRepositoryDatabase(wrapper)
    }
}
```

If you need to use the class directly in your Swift code, you need a little helper class to handle the injection.
```kotlin
class KoinDependencies : KoinComponent {
    val playerStateRepository: PlayerStateRepositoryDatabase by inject()
}
```

Then you can use `KoinDependencies` to inject your class in Swift.
```swift
let playerStateRepository: PlayerStateRepository = KoinDependencies().playerStateRepository
```

You can also flip this around a bit and share your classes binding between Android and iOS. First, you have to make the platform-specific part injectable. You can do that by adjusting the previous `platformModule()` code:

```kotlin
actual fun platformModule() = module {
    single {
        val driver = createSqlDriver()
        val wrapper = AppDatabaseWrapper(Database(driver))
    }
}
```

If you need some more detail on implementing a platform specific `AppDatabaseWrapper` see Jetbrain's tutorial [Share data access layer](https://www.jetbrains.com/help/kotlin-multiplatform-dev/multiplatform-ktor-sqldelight.html#create-the-android-application). They call it a `DatabaseDriverFactory`. 

Then bind your class in the shared `initKoin` function.
```kotlin
fun initKoin(extraModules: List<Module>, appDeclaration: KoinAppDeclaration = {}) {
    startKoin {
        appDeclaration()
        modules(
            *extraModules.toTypedArray(),
            platformModule(),
            module {
                single<PlayerStateRepositoryDatabase> {
                    PlayerStateRepositoryDatabase(get())
                }
            }
        )
    }
}
```

If your class has an Android specific binding in `androidMain` make sure to delete it to avoid confusion later.

That's it for this article. Hope it helps!