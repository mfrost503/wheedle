<?php
namespace Test;

use Wheedle\TwitterClient;
use Wheedle\Tweet;
use Snaggle\Client\Credentials\AccessCredentials;
use Snaggle\Client\Credentials\ConsumerCredentials;
use Snaggle\Client\Signatures\HmacSha1;
use Snaggle\Client\Header\Header;

/**
 * @author Matt Frost <mfrost.design@gmail.com>
 * @license MIT http://opensource.org/licenses MIT
 * @package Tests
 * @subpackage Wheedle
 */
class TweetTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->access = new AccessCredentials('1234567888', 'AbcDefG');
        $this->consumer = new ConsumerCredentials('987654321', 'GfeDcbA');
        $this->client = $this->getMock(
            'Wheedle\TwitterClient',
            ['makeGetRequest', 'makePostRequest'],
            [$this->access, $this->consumer]
        );
        $this->response = $this->getMockBuilder('GuzzleHttp\Message\Response')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function tearDown()
    {
        unset($this->client);
        unset($this->access);
        unset($this->consumer);
        unset($this->response);
    }

    /**
     * Test to ensure the get method in retrieve is being called correctly
     */
    public function testEnsureGetMethodIsCalledCorrectlyForRetrieve()
    {
        $signature = new HmacSha1($this->consumer, $this->access);
        $signature->setHttpMethod('GET');
        $signature->setResourceURL('https://api.twitter.com/1.1/statuses/show/1.json');
        $signature->setTimestamp(1114234234234);
        $signature->setNonce('testNonce');
        $header = new Header;
        $header->setSignature($signature);
        $expectedHeader = $header->createAuthorizationHeader();
        $this->client->setSignature($signature);
        
        $this->client->expects($this->once())
            ->method('makeGetRequest')
            ->with('statuses/show/1.json', []);
            
        $tweet = new Tweet($this->client);
        $tweet->retrieve(1);
    }

    /**
     * Test to ensure the retrieveMentions is being called correctly
     */
    public function testEnsureRetrieveMentionsIsHitCorrectly()
    {
        $signature = new HmacSha1($this->consumer, $this->access);
        $signature->setHttpMethod('GET');
        $signature->setResourceURL('https://api.twitter.com/1.1/statuses/mentions_timeline.json');
        $signature->setTimestamp(1114234234234);
        $signature->setNonce('testNonce');
        $header = new Header;
        $header->setSignature($signature);
        $expectedHeader = $header->createAuthorizationHeader();
        $this->client->setSignature($signature);
        
        $this->client->expects($this->once())
            ->method('makeGetRequest')
            ->with('statuses/mentions_timeline.json', []);
                
        $tweet = new Tweet($this->client);
        $tweet->retrieveMentions();
    }

    /**
     * Test to ensure the userTimeline is being called correctly
     */
    public function testEnsureRetrieveUserTimelineIsHitCorrectly()
    {
        $signature = new HmacSha1($this->consumer, $this->access);
        $signature->setHttpMethod('GET');
        $signature->setResourceURL('https://api.twitter.com/1.1/statuses/user_timeline.json?count=20');
        $signature->setTimestamp(1114234234234);
        $signature->setNonce('testNonce');
        $header = new Header;
        $header->setSignature($signature);
        $expectedHeader = $header->createAuthorizationHeader();
        $this->client->setSignature($signature);
        
        $this->client->expects($this->once())
            ->method('makeGetRequest')
            ->with('statuses/user_timeline.json', ['count' => 20]);
            
        $tweet = new Tweet($this->client);
        $tweet->retrieveUserTimeline(['count' => '20']);
    }

    /**
     * Test to ensure the userTimeline is being called correctly
     */
    public function testEnsureRetrieveHomeTimelineIsHitCorrectly()
    {
        $signature = new HmacSha1($this->consumer, $this->access);
        $signature->setHttpMethod('GET');
        $signature->setResourceURL('https://api.twitter.com/1.1/statuses/home_timeline.json?count=20');
        $signature->setTimestamp(1114234234234);
        $signature->setNonce('testNonce');
        $header = new Header;
        $header->setSignature($signature);
        $expectedHeader = $header->createAuthorizationHeader();
        $this->client->setSignature($signature);
        
        $this->client->expects($this->once())
            ->method('makeGetRequest')
            ->with('statuses/home_timeline.json', ['count' => 20]);

        $tweet = new Tweet($this->client);
        $tweet->retrieveHomeTimeline(['count' => 20]);
    }

    /**
     * Test to ensure the retrieve my retweets is being called correctly
     */
    public function testEnsureRetrieveMyRetweetsIsHitCorrectly()
    {
        $signature = new HmacSha1($this->consumer, $this->access);
        $signature->setHttpMethod('GET');
        $signature->setResourceURL('https://api.twitter.com/1.1/statuses/retweets_of_me.json?count=20');
        $signature->setTimestamp(1114234234234);
        $signature->setNonce('testNonce');
        $header = new Header;
        $header->setSignature($signature);
        $expectedHeader = $header->createAuthorizationHeader();
        $this->client->setSignature($signature);
        
        $this->client->expects($this->once())
            ->method('makeGetRequest')
            ->with('statuses/retweets_of_me.json', ['count' => 20]);
            
        $tweet = new Tweet($this->client);
        $tweet->retrieveMyRetweets(['count' => 20]);
    }

    /**
     * Test to ensure the retrieve retweets is being called correctly
     */
    public function testEnsureRetrieveRetweetsIsHitCorrectly()
    {
        $signature = new HmacSha1($this->consumer, $this->access);
        $signature->setHttpMethod('GET');
        $signature->setResourceURL('https://api.twitter.com/1.1/statuses/retweets/1.json?count=20');
        $signature->setTimestamp(1114234234234);
        $signature->setNonce('testNonce');
        $header = new Header;
        $header->setSignature($signature);
        $expectedHeader = $header->createAuthorizationHeader();
        $this->client->setSignature($signature);
        
        $this->client->expects($this->once())
            ->method('makeGetRequest')
            ->with('statuses/retweets/1.json', ['count' => 20]);

        $tweet = new Tweet($this->client);
        $tweet->retrieveRetweets(1, ['count' => 20]);
    }


    /**
     * Test to ensure create is being called correctly
     */
    public function testEnsureCreateTweetIsHitCorrectly()
    {
        $signature = new HmacSha1($this->consumer, $this->access);
        $signature->setHttpMethod('POST');
        $signature->setResourceURL('https://api.twitter.com/1.1/statuses/update.json');
        $signature->setTimestamp(1114234234234);
        $signature->setNonce('testNonce');
        $header = new Header;
        $header->setSignature($signature);
        $expectedHeader = $header->createAuthorizationHeader();
        $this->client->setSignature($signature);
        
        $this->client->expects($this->once())
            ->method('makePOSTRequest')
            ->with('statuses/update.json', ['status' => 'This is a test tweet', 'trim_user' => true]);

        $tweet = new Tweet($this->client);
        $tweet->create('This is a test tweet', ['trim_user' => true]);
    }

    /**
     * Test to ensure retweet is being called correctly
     */
    public function testEnsureRetweetIsHitCorrectly()
    {
        $signature = new HmacSha1($this->consumer, $this->access);
        $signature->setHttpMethod('POST');
        $signature->setResourceURL('https://api.twitter.com/1.1/statuses/retweet/1.json');
        $signature->setTimestamp(1114234234234);
        $signature->setNonce('testNonce');
        $header = new Header;
        $header->setSignature($signature);
        $expectedHeader = $header->createAuthorizationHeader();
        $this->client->setSignature($signature);
        
        $this->client->expects($this->once())
            ->method('makePostRequest')
            ->with('statuses/retweet/1.json', ['trim_user' => true]);

        $tweet = new Tweet($this->client);
        $tweet->retweet(1, ['trim_user' => true]);
    }

    /**
     * Test to ensure delete is being called correctly
     */
    public function testEnsureDeleteIsHitCorrectly()
    {
        $signature = new HmacSha1($this->consumer, $this->access);
        $signature->setHttpMethod('POST');
        $signature->setResourceURL('https://api.twitter.com/1.1/statuses/destroy/1.json');
        $signature->setTimestamp(1114234234234);
        $signature->setNonce('testNonce');
        $header = new Header;
        $header->setSignature($signature);
        $expectedHeader = $header->createAuthorizationHeader();
        $this->client->setSignature($signature);
        
        $this->client->expects($this->once())
            ->method('makePostRequest')
            ->with('statuses/destroy/1.json');

        $tweet = new Tweet($this->client);
        $tweet->delete(1);
    }
    /**
     * Test to ensure the filter options method is working correctly
     */
    public function testEnsureFilterOptionsMethodIsRemovingBadOptions()
    {
        $availableOptions = [
            'id',
            'count',
            'since_id',
            'max_id'
        ];

        $options = ['id' => 1, 'count' => 20, 'include_entities' => false];
        $expectedResult = ['id','count'];
        $tweet = new Tweet($this->client);
        $filteredOptions = $tweet->filterOptions($availableOptions, $options);
        $this->assertTrue(array_key_exists('id', $filteredOptions));
        $this->assertTrue(array_key_exists('count', $filteredOptions));
        $this->assertFalse(array_key_exists('include_entities', $filteredOptions));
    }
}
