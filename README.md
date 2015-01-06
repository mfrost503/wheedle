## Wheedle
#### A PHP Twitter library

<img src="https://travis-ci.org/mfrost503/wheedle.svg?branch=master"/>

This library is currently a work in progress, more examples to follow as they get built out!

Wheedle provides a common sense way to interacting with the Twitter API. Built using Guzzle and Snaggle, you'll never have to worry about those pesky OAuth 1 signatures. Getting started is as simple as passing in your tokens.

Here's an example of how easy is it to get started!

```php
<?php
use Snaggle\Client\Credentials\AccessCredentials;
use Snaggle\Client\Credentials\ConsumerCredentials;
use Wheedle\TwitterClient;
use Wheedle\Tweet;

$accessToken = new AccessCredentials;
$accessToken->setIdentifier('YOUR_ACCESS_TOKEN');
$accessToken->setSecret('YOUR_ACCESS_SECRET');

$consumerToken = new ConsumerCredentials;
$consumerToken->setIdentifier('YOUR_CONSUMER_KEY');
$consumerToken->setSecret('YOUR_CONSUMER_SECRET');

$client = new TwitterClient($accessToken, $consumerToken);

$tweet = new Tweet($client);

$tweet->retrieveMentions();
```

This example will retrieve the last 20 mentions for the authenticated user, but it's configureable to retrieve up to 200

```php
$tweet->retrieveMentions(['count' => 200]);
```

### Tweet

The Tweet class will allow you to retrieve and post tweets (or statuses) and give you the ability to tailor the results to what you need.

#### Retrieve

To retrieve a tweet, you'll need to know the ID of the tweet and pass it as a parameter to the retrieve method:

```php
<?php
use Snaggle\Client\Credentials\AccessCredentials;
use Snaggle\Client\Credentials\ConsumerCredentials;
use Wheedle\TwitterClient;
use Wheedle\Tweet;

$accessToken = new AccessCredentials;
$accessToken->setIdentifier('YOUR_ACCESS_TOKEN');
$accessToken->setSecret('YOUR_ACCESS_SECRET');

$consumerToken = new ConsumerCredentials;
$consumerToken->setIdentifier('YOUR_CONSUMER_KEY');
$consumerToken->setSecret('YOUR_CONSUMER_SECRET');

$client = new TwitterClient($accessToken, $consumerToken);

$tweet = new Tweet($client);

$data = $tweet->retrieve(123456);
```

Optionally you can pass an array of parameters to give you back what you need, for retrieve the parameters are:

* trim_user boolean returns a user object with just numerical ID when true
* include_my_retweet boolean when true any tweets RT'd by authenticated user will have current_user_retweet node
* include_entites boolean entities node will be excluded when set to false

These parameters are completely optional, so you can include all or none of them

```php
$parameters = ['trim_user' => true, 'include_my_retweet' => true, 'include_entities' => false];
$tweet->retrieve(123456, $parameters);
```

