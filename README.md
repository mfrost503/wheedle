## Wheedle
#### A PHP Twitter library

This library is currently a work in progress, more examples to follow as they get built out!

Wheedle provides a common sense way to interacting with the Twitter API. Built using Guzzle and Snaggle, you'll never have to worry about those pesky OAuth 1 signatures. Getting started is as simple as passing in your tokens.

Here's an example of how easy is it to get started!

```
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

```
$tweet->retrieveMentions(['count' => 200]);
```
With many of these calls, there's the ability to tailor the response to what you need!