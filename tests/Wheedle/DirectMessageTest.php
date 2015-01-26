<?php
namespace Test;

use Wheedle\TwitterClient;
use Wheedle\DirectMessage;
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
class DirectMessageTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->access = new AccessCredentials('1234567888', 'AbcDefG');
        $this->consumer = new ConsumerCredentials('987654321', 'GfeDcbA');        
        $this->client = $this->getMock('Wheedle\TwitterClient', ['makeGetRequest', 'makePostRequest'], [$this->access, $this->consumer]);
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
     * Test to ensure the get method for DMs is being called correctly
     */
    public function testEnsureGetMethodIsCalledCorrectlyForDMS()
    {
        $signature = new HmacSha1($this->consumer, $this->access);
        $signature->setHttpMethod('GET');
        $signature->setResourceURL('https://api.twitter.com/1.1/direct_messages.json');
        $signature->setTimestamp(1114234234234);
        $signature->setNonce('testNonce');
        $header = new Header;
        $header->setSignature($signature);
        $expectedHeader = $header->createAuthorizationHeader();
        $this->client->setSignature($signature);
        
        $this->client->expects($this->once())
            ->method('makeGetRequest')
            ->with('direct_messages.json', []);
            
        $tweet = new DirectMessage($this->client);
        $tweet->retrieveLatestMessages();
    }

    /**
     * Test to ensure the get method for sent DMs is being called correctly
     */
    public function testEnsureGetMethodIsCalledCorrectlyForSentDMS()
    {
        $signature = new HmacSha1($this->consumer, $this->access);
        $signature->setHttpMethod('GET');
        $signature->setResourceURL('https://api.twitter.com/1.1/direct_messages/sent.json');
        $signature->setTimestamp(1114234234234);
        $signature->setNonce('testNonce');
        $header = new Header;
        $header->setSignature($signature);
        $expectedHeader = $header->createAuthorizationHeader();
        $this->client->setSignature($signature);
        
        $this->client->expects($this->once())
            ->method('makeGetRequest')
            ->with('direct_messages/sent.json', []);
            
        $tweet = new DirectMessage($this->client);
        $tweet->retrieveLatestSentMessages();
    }
}