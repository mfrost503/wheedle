## Wheedle
#### A PHP Twitter library

<img src="https://travis-ci.org/mfrost503/wheedle.svg?branch=master"/>
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mfrost503/wheedle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mfrost503/wheedle/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/mfrost503/wheedle/badges/build.png?b=master)](https://scrutinizer-ci.com/g/mfrost503/wheedle/build-status/master)

This library is currently a work in progress, more examples to follow as they get built out!

Wheedle provides a common sense way to interacting with the Twitter API. Built using Guzzle and Snaggle, you'll never have to worry about those pesky OAuth 1 signatures. Getting started is as simple as passing in your tokens.

Here's an example of how easy is it to get started!

Table of Contents:
* [Tweet](#tweet)
    * [Retrieve](#retrieve)
    * [Mentions](#mentions)
    * [Home Timeline](#home-timeline)
    * [User Timeline](#user-timeline)
    * [My Tweets](#my-tweets---retweeted-by-others)
    * [Retrieve Retweets](#retrieve-retweets)
    * [Send a tweet](#send-a-tweet)
    * [Retweet an existing tweet](#retweet-an-existing-tweet)
    * [Delete an existing tweet](#delete-an-existing-tweet)
* [Direct Message](#direct-message)

### The Basics

This library is a balance of convenience methods and an intuitive HTTP Client built on Guzzle. By utilizing Guzzle and Snaggle,
we're able to minimize the amount of setup required to start making Authenticated API requests against the Twitter API. Nearly every
endpoint in the Twitter API is going to have additional options that are available to you. If there is a convenience method for that
particular endpoint, the options will be provided, however it may be necessary to consult [Twitter API Docs](https://dev.twitter.com/rest/public) for more information on your particular endpoint.

The aim is to take the pain out of dealing with the Twitter API and other wrappers and libraries that don't give you what you need.
We aim to provide as much convenience and ease of use as possible. If you see something that doesn't seem right or could be done better,
contributions (issues, pull requests, feature requests) are always welcome.

### Tweet

The Tweet class will allow you to retrieve and post tweets (or statuses).

#### Retrieve

To retrieve a tweet, you'll need to know the ID of the tweet and pass it as a parameter to the retrieve method:

```php
<?php
use Snaggle\Client\Credentials\AccessCredentials;
use Snaggle\Client\Credentials\ConsumerCredentials;
use Wheedle\TwitterClient;
use Wheedle\Tweet;
use Wheedle\Exceptions\RateLimitExceededException;
use Wheedle\Exceptions\UnauthorizedRequestException;
use \RuntimeException;

$accessToken = new AccessCredentials('YOUR_ACCESS_TOKEN', 'YOUR_ACCESS_SECRET');

$consumerToken = new ConsumerCredentials('YOUR_CONSUMER_KEY', 'YOUR_CONSUMER_SECRET');

$client = new TwitterClient($accessToken, $consumerToken);

$tweet = new Tweet($client);

try {

    $data = $tweet->retrieve(123456);

} catch (RuntimeException $e) {
    // handle Runtime Exception
} catch (RateLimitExceededException $e) {
    // handle rate limit exceeded exception
} catch (UnauthorizedRequestException $e) {
    // handle unauthorized request
}
```

Optionally you can pass an array of parameters to give you back what you need, for retrieve the parameters are:

* **trim_user** *boolean* returns a user object with just numerical ID when true
* **include_my_retweet** *boolean* when true any tweets RT'd by authenticated user will have current_user_retweet node
* **include_entites** boolean *entities* node will be excluded when set to false

These parameters are completely optional, so you can include all or none of them

#### Mentions

To retrieve a collection of tweets in which the authenticated user was mentioned (@yourhandle), you can use the retrieveMentions method:

```php
<?php
use Snaggle\Client\Credentials\AccessCredentials;
use Snaggle\Client\Credentials\ConsumerCredentials;
use Wheedle\TwitterClient;
use Wheedle\Tweet;
use Wheedle\Exceptions\RateLimitExceededException;
use Wheedle\Exceptions\UnauthorizedRequestException;
use \RuntimeException;

$accessToken = new AccessCredentials('YOUR_ACCESS_TOKEN', 'YOUR_ACCESS_SECRET');

$consumerToken = new ConsumerCredentials('YOUR_CONSUMER_KEY', 'YOUR_CONSUMER_SECRET');

$client = new TwitterClient($accessToken, $consumerToken);

$tweet = new Tweet($client);

try {

    $data = $tweet->retrieveMentions();

} catch (RuntimeException $e) {
    // handle Runtime Exception
} catch (RateLimitExceededException $e) {
    // handle rate limit exceeded exception
} catch (UnauthorizedRequestException $e) {
    // handle unauthorized request
}
```

The optional parameters for this method are:

* **count** *int* number of tweets to return up to 200
* **since_id** *int* returns results with an ID more recent than the provided ID
* **max_id** *int* returns results with an ID older than the provided ID
* **trim_user** *boolean* when true returns the user object with only an ID
* **contributor_details** *boolean* when true enhances the contributors element of the response
* **include_entities** *boolean* entities node will be excluded when set to false

#### Home Timeline

To retrieve a collection of tweets from all the users you follow:

```php
<?php
use Snaggle\Client\Credentials\AccessCredentials;
use Snaggle\Client\Credentials\ConsumerCredentials;
use Wheedle\TwitterClient;
use Wheedle\Tweet;
use Wheedle\Exceptions\RateLimitExceededException;
use Wheedle\Exceptions\UnauthorizedRequestException;
use \RuntimeException;

$accessToken = new AccessCredentials('YOUR_ACCESS_TOKEN', 'YOUR_ACCESS_SECRET');

$consumerToken = new ConsumerCredentials('YOUR_CONSUMER_KEY', 'YOUR_CONSUMER_SECRET');

$client = new TwitterClient($accessToken, $consumerToken);

$tweet = new Tweet($client);
try {

    $data = $tweet->retrieveHomeTimeline();

} catch (RuntimeException $e) {
    // handle Runtime Exception
} catch (RateLimitExceededException $e) {
    // handle rate limit exceeded exception
} catch (UnauthorizedRequestException $e) {
    // handle unauthorized request
}
```

The optional parameters for this method are:

* **count** *int* number of tweets to return up to 200
* **since_id** *int* returns results with an ID more recent than the provided ID
* **max_id** *int* returns results with an ID older than the provided ID
* **trim_user** *boolean* when true returns the user object with only an ID
* **contributor_details** *boolean* when true enhances the contributors element of the response
* **include_entities** *boolean* entities node will be excluded when set to false

#### User Timeline

To retrieve a timeline of all your tweets:

```php
<?php
use Snaggle\Client\Credentials\AccessCredentials;
use Snaggle\Client\Credentials\ConsumerCredentials;
use Wheedle\TwitterClient;
use Wheedle\Tweet;
use Wheedle\Exceptions\RateLimitExceededException;
use Wheedle\Exceptions\UnauthorizedRequestException;
use \RuntimeException;

$accessToken = new AccessCredentials('YOUR_ACCESS_TOKEN', 'YOUR_ACCESS_SECRET');

$consumerToken = new ConsumerCredentials('YOUR_CONSUMER_KEY', 'YOUR_CONSUMER_SECRET');

$client = new TwitterClient($accessToken, $consumerToken);

$tweet = new Tweet($client);

try {

    $data = $tweet->retrieveUserTimeline();

} catch (RuntimeException $e) {
    // handle Runtime Exception
} catch (RateLimitExceededException $e) {
    // handle rate limit exceeded exception
} catch (UnauthorizedRequestException $e) {
    // handle unauthorized request
}
```

The optional parameters for this method are:

* **count** *int* number of tweets to return up to 200
* **since_id** *int* returns results with an ID more recent than the provided ID
* **max_id** *int* returns results with an ID older than the provided ID
* **trim_user** *boolean* when true returns the user object with only an ID
* **contributor_details** *boolean* when true enhances the contributors element of the response
* **include_entities** *boolean* entities node will be excluded when set to false

#### My Tweets - Retweeted by others

Retrieve a list of tweets that were retweeted by other users

```php
<?php
use Snaggle\Client\Credentials\AccessCredentials;
use Snaggle\Client\Credentials\ConsumerCredentials;
use Wheedle\TwitterClient;
use Wheedle\Tweet;
use Wheedle\Exceptions\RateLimitExceededException;
use Wheedle\Exceptions\UnauthorizedRequestException;
use \RuntimeException;

$accessToken = new AccessCredentials('YOUR_ACCESS_TOKEN', 'YOUR_ACCESS_SECRET');

$consumerToken = new ConsumerCredentials('YOUR_CONSUMER_KEY', 'YOUR_CONSUMER_SECRET');

$client = new TwitterClient($accessToken, $consumerToken);

$tweet = new Tweet($client);

try {

    $data = $tweet->retrieveMyRetweets();

} catch (RuntimeException $e) {
    // handle Runtime Exception
} catch (RateLimitExceededException $e) {
    // handle rate limit exceeded exception
} catch (UnauthorizedRequestException $e) {
    // handle unauthorized request
}
```

The optional parameters for this method are:

* **count** *int* number of tweets to return up to 200
* **since_id** *int* returns results with an ID more recent than the provided ID
* **max_id** *int* returns results with an ID older than the provided ID
* **trim_user** *boolean* when true returns the user object with only an ID
* **include_user_entities** *boolean* user entities node will be excluded when set to false
* **include_entities** *boolean* entities node will be excluded when set to false

#### Retieve Retweets

Retrieve all the retweeting accounts for a single tweet.

```php
<?php
use Snaggle\Client\Credentials\AccessCredentials;
use Snaggle\Client\Credentials\ConsumerCredentials;
use Wheedle\TwitterClient;
use Wheedle\Tweet;
use Wheedle\Exceptions\RateLimitExceededException;
use Wheedle\Exceptions\UnauthorizedRequestException;
use \RuntimeException;

$accessToken = new AccessCredentials('YOUR_ACCESS_TOKEN', 'YOUR_ACCESS_SECRET');

$consumerToken = new ConsumerCredentials('YOUR_CONSUMER_KEY', 'YOUR_CONSUMER_SECRET');

$client = new TwitterClient($accessToken, $consumerToken);

$tweet = new Tweet($client);

try {

    $data = $tweet->retrieveRetweets(12345324);

} catch (RuntimeException $e) {
    // handle Runtime Exception
} catch (RateLimitExceededException $e) {
    // handle rate limit exceeded exception
} catch (UnauthorizedRequestException $e) {
    // handle unauthorized request
}
```

The optional parameters for this method are:

* **count** *int* number of tweets to return up to 200
* **trim_user** *boolean* when true returns the user object with only an ID

#### Send a Tweet

Create a new Tweet and post it to your timeline

```php
<?php
use Snaggle\Client\Credentials\AccessCredentials;
use Snaggle\Client\Credentials\ConsumerCredentials;
use Wheedle\TwitterClient;
use Wheedle\Tweet;
use Wheedle\Exceptions\RateLimitExceededException;
use Wheedle\Exceptions\UnauthorizedRequestException;
use \RuntimeException;

$accessToken = new AccessCredentials('YOUR_ACCESS_TOKEN', 'YOUR_ACCESS_SECRET');

$consumerToken = new ConsumerCredentials('YOUR_CONSUMER_KEY', 'YOUR_CONSUMER_SECRET');

$client = new TwitterClient($accessToken, $consumerToken);

$tweet = new Tweet($client);

try {

    $data = $tweet->create(['status' => 'This is a brand new tweet']);

} catch (RuntimeException $e) {
    // handle Runtime Exception
} catch (RateLimitExceededException $e) {
    // handle rate limit exceeded exception
} catch (UnauthorizedRequestException $e) {
    // handle unauthorized request
}
```

The optional parameters for this method are:

* **in_reply_to_status_id** *int* the ID for the tweet you are replying to, must @ mention original author
* **possibly_sensitive** *boolean* should be set when tweet contains nudity, violence or other gross stuff
* **lat** *float* latitude
* **long** *float* longitude
* **place_id** *string* a place in the world
* **display_coordinates** *boolean* when true will put a pin an exact coordinates tweet was sent from
* **trim_user** *boolean* when true returns the user object with only an ID
* **media_ids** *string* a list of media ids to associate to a tweet

#### Retweet an existing tweet

Retweet a tweet to your timeline

```php
<?php
use Snaggle\Client\Credentials\AccessCredentials;
use Snaggle\Client\Credentials\ConsumerCredentials;
use Wheedle\TwitterClient;
use Wheedle\Tweet;
use Wheedle\Exceptions\RateLimitExceededException;
use Wheedle\Exceptions\UnauthorizedRequestException;
use \RuntimeException;

$accessToken = new AccessCredentials('YOUR_ACCESS_TOKEN', 'YOUR_ACCESS_SECRET');

$consumerToken = new ConsumerCredentials('YOUR_CONSUMER_KEY', 'YOUR_CONSUMER_SECRET');

$client = new TwitterClient($accessToken, $consumerToken);

$tweet = new Tweet($client);

try {

    $data = $tweet->retweet(12312432);

} catch (RuntimeException $e) {
    // handle Runtime Exception
} catch (RateLimitExceededException $e) {
    // handle rate limit exceeded exception
} catch (UnauthorizedRequestException $e) {
    // handle unauthorized request
}
```

The optional parameters for this method are:

* **trim_user** *boolean* when true returns the user object with only an ID

#### Delete an existing tweet

We all make mistakes right? Here's how'd you delete one of those mistakes...

```php
<?php
use Snaggle\Client\Credentials\AccessCredentials;
use Snaggle\Client\Credentials\ConsumerCredentials;
use Wheedle\TwitterClient;
use Wheedle\Tweet;
use Wheedle\Exceptions\RateLimitExceededException;
use Wheedle\Exceptions\UnauthorizedRequestException;
use \RuntimeException;

$accessToken = new AccessCredentials('YOUR_ACCESS_TOKEN', 'YOUR_ACCESS_SECRET');

$consumerToken = new ConsumerCredentials('YOUR_CONSUMER_KEY', 'YOUR_CONSUMER_SECRET');

$client = new TwitterClient($accessToken, $consumerToken);

$tweet = new Tweet($client);

try {

    $data = $tweet->delete(12312432);

} catch (RuntimeException $e) {
    // handle Runtime Exception
} catch (RateLimitExceededException $e) {
    // handle rate limit exceeded exception
} catch (UnauthorizedRequestException $e) {
    // handle unauthorized request
}
```

The optional parameters for this method are:

* **trim_user** *boolean* when true returns the user object with only an ID


