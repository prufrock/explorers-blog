---
extends: _layouts.post
section: content
title: I'll Receive You Some Test Events!
date: 2020-02-15
description: A fix for the Intellij message "Test events were not received" when running tests with Gradle and Kotlin.
cover_image: /assets/img/post-cover-image-2.png
excerpt: I ran into some trouble tonight try to get Intellij to run all my tests. It kept saying "Test events were not received". I scratched my head and wondered aloud what could be the cause of this.
categories: [kotlin]
---

I sat down in front of my laptop to learn some GraphQL. I was flipping through my open windows and I discovered I had some uncommitted Kotlin code in the repo where I am learning Kotlin. This wouldn't do. No, this wouldn't do.

I went to commit the code and Intellij helpfully told me there were some warnings that I should review before committing.

"OK", I thought, "probably not that important. I'll just run the tests and if they pass I'll commit." If it only it were that easy...queue Jefferson Airplane cuz we're going down a rabbit hole. Intellij gave me a very understated message in my test runner window "Test events were not received". I was pretty sure this was related to Gradle.

As a quick aside, I don't know much about Gradle yet. I need to know more about Gradle. Eventually I'll be able to understand Gradle. Until then, I fully expect to lean on my knowledgeable co-workers to set up Gradle projects correctly.

Some quick [DuckDuckGo](https://duckduckgo.com)'ing and I found there was an answer on StackOverflow. 

[“Test events were not received” when run tests using Intellij](https://stackoverflow.com/questions/57795263/test-events-were-not-received-when-run-tests-using-intellij)

I spoke the words, "Yay! An answer to my exact question!"

They recommended switching test runners. You go to "File -> Settings ->Build,Execution, Deployment -> Build Tools -> Gradle" and set the "Run tests using" to "Intellij". This worked for a hot second. Then I got skeptical(this often causes me trouble). "Shouldn't this just work?", I thought, "Gradle test works in my shell. Why not in Intellij?". I fiddled with some settings mostly in the aforementioned window and broke the tests such that I couldn't get them to work anymore.

I got a little despondent at this point. I wondered if I'd ever be able to run all my tests in Intellij without having to grok all of Gradle. I just wanted to get back to learning about GraphQL!

Then I had heard a light bulb "click" and I right clicked on the "test" folder. There I found a "Run "All Tests"" option.

I clicked it.

They all ran. I clicked it again. They all ran. I tried the gradle test task in Intellij. Agony again "Test events were not received". It was different I was starting to understand the pain now.

When I ran via "Run "All Tests"" I noticed that it showed "<default package>" at the top. I don't understand what that means exactly. It made me wonder if I had the packages correct in my tests. I started checking them.

That's when I found it. One of them had the package set to "acceptance". I switched it to "com.dkanen". Then bingo bango it worked from Intellij's gradle test task! Well, or so I thought.

Friends, I have to admit I ran into a wall on this one. I can never get gradle test to work more than once in Intellij without running gradle clean first. It's always like this:

1. gradle clean
2. gradle test
3. goto 1

I'm going to live with "Run "All Tests"" for now.

Have you run into "Test events were not received" in Intellij and found a reliable way to fix it?

I'm going back to GraphQL...for now.