---
extends: _layouts.post
section: content
title: Oh GraphQL, May I Have Some Users?
date: 2020-02-16
description: Where in I struggle and then succeed to get some users from the users query in GraphQL.
cover_image: /assets/img/post-cover-image-2.png
excerpt: Sure GraphQL is straightforward. That doesn't mean it's obvious(to me anyway).
categories: [graphql]
author: David Kanenwisher
---

I've heard GraphQL is easy to pick up. I've done a little reading on the official [GraphQL site](https://graphql.org/) and watch some videos on YouTube. I figure I got what I need to get started. I just need a GraphQL server.

I went the through the [tutorial](https://lighthouse-php.com/tutorial/#installation) on Lighthouse PHP's website. Well, not the whole thing(yet), just the installation part. I stopped there because I got back the user like in their example.

GraphQL Query for a user
```graphql
{
  user(id: 1) {
    id
    name
    email
  }
}
```

With this result:
```graphql
{
  "data": {
    "user": {
      "id": "1",
      "name": "Shea Spencer DDS",
      "email": "ismael.fisher@example.net"
    }
  }
}
```

Notice how the of **shape** of the query and the result are similar? Maybe you're wondering why I said **shape**? It seems to be the fancy way the GraphQL folks like to say that, ignoring the "data" key, the query and the result have the same keys in the same order. Therefore if I add or remove keys from the query I can change what comes back. For instance I can remove "email" from the query.

GraphQL query for user without "email":
```graphql
{
  user(id: 1) {
    id
    name
  }
}
```

Result of the GraphQL query:
```graphql
{
  "data": {
    "user": {
      "id": "1",
      "name": "Shea Spencer DDS"
    }
  }
}
```

See how email is gone? Pretty neat, eh? I didn't have to do anything special in my PHP code to make that happen. Lighthouse just handles it for me. Pretty damn spiffy if you ask me. Ok, ok, let's get to the part where I confuse myself and then eventually resolve that confusion in a shower of JSON.

I saw this query for "user(id: 1)". (Can I take you aside for a moment and explain something to you? So those parentheses after "user" that's called an argument. Supposedly any field in GraphQL can take an argument. No more fuddling with the URL non-sense. Rockin' right?). I thought, "If I can get one user I must be able to get 2 users, 3 users, all of the users?" Being the intrepid explorer that I am I tried this query:

First attempt to get users from GraphQL:
```graphql
{
  # switched user to users
  # you can put comments in GraphQL queries
  # so rad
  users {
    id
    name
    email
  }
}
```

This is when I realize...The game is a foot!
```graphql
{
  "errors": [
    {
      "message": "Cannot query field \"id\" on type \"UserPaginator\".",
      "extensions": {
        "category": "graphql"
      },
      "locations": [
        {
          "line": 3,
          "column": 5
        }
      ]
    },
    {
      "message": "Cannot query field \"name\" on type \"UserPaginator\".",
      "extensions": {
        "category": "graphql"
      },
      "locations": [
        {
          "line": 4,
          "column": 5
        }
      ]
    },
    {
      "message": "Cannot query field \"email\" on type \"UserPaginator\".",
      "extensions": {
        "category": "graphql"
      },
      "locations": [
        {
          "line": 5,
          "column": 5
        }
      ]
    }
  ]
}
```

"I don't want a UserPaginator!", I shake my fist at the screen, "What is this nonsense. I read something about a schema! Show me the schema you foul ruffian!"

The GraphQL Playground has a little schema drawer on the left side. It doesn't response to shouting but it does respond, like a friendly button, to clicks. I scroll around a bit and find what I'm looking for:

```graphql
type UserPaginator {
  paginatorInfo: PaginatorInfo!
  data: [User!]!
}
```

Ah ha! There's the User I want. For some reason it's shouting "User!" at me but I'll worry about that later. For now I venture forward. Everybody is always talking about the shapes of queries so I take a run at shaping my query to get at the "User!" in "UserPaginator".

UserPaginator show me Users!
```graphql
{
  users {
    UserPaginator {
      data {
        name
      }
    }
  }
}
```

I guess not:
```graphql
{
  "errors": [
    {
      "message": "Cannot query field \"UserPaginator\" on type \"UserPaginator\".",
      "extensions": {
        "category": "graphql"
      },
      "locations": [
        {
          "line": 3,
          "column": 5
        }
      ]
    }
  ]
}
```

Drat! That wasn't it. Back to the schema because I clearly don't understand what I think I'm working with. I find a "users" query defined in the schema. I'm pretty sure I got a hold of something here.

```graphql
type Query {
  users(
    first: Int = 10
    page: Int
  ): UserPaginator
  user(id: ID): User
}
```

Does that mean "users" returns a UserPaginator? If so then I think I should treat the "users" element as a "UserPaginator" and put fields I want in there.

Query for the fields I want on UserPaginator:
```graphql
{
  users {
    # data is a field on UserPaginator
      data {
        id
        name
        email
      }
  }
}
```

The result. I hit pay dirt!
```graphql
{
  "data": {
    "users": {
      "data": [
        {
          "id": "1",
          "name": "Shea Spencer DDS",
          "email": "ismael.fisher@example.net"
        },
        {
          "id": "2",
          "name": "Prof. Soledad Larson",
          "email": "mmorissette@example.net"
        },
        {
          "id": "3",
          "name": "Dr. Coleman Boehm",
          "email": "senger.kolby@example.net"
        },
        {
          "id": "4",
          "name": "Jazmyn Padberg",
          "email": "london72@example.net"
        },
        {
          "id": "5",
          "name": "Parker Stokes",
          "email": "valentina.gulgowski@example.org"
        },
        {
          "id": "6",
          "name": "Raven McDermott",
          "email": "fpouros@example.org"
        },
        {
          "id": "7",
          "name": "Alec Pouros",
          "email": "erdman.herminio@example.org"
        },
        {
          "id": "8",
          "name": "Fanny Harber",
          "email": "erich80@example.com"
        },
        {
          "id": "9",
          "name": "Emelia Rogahn",
          "email": "thurman65@example.org"
        },
        {
          "id": "10",
          "name": "Jayne Klocko",
          "email": "kaya.rutherford@example.net"
        }
      ]
    }
  }
}
```

That's how I figured out how to get fields on the users query. I can't say I fully understand why UserPaginator gets to override "users" in the query. Also, why is the element "data" rather than "users"? It seems like I'm losing some useful type information there.

My questions aside, how's your GraphQL journey going?