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
        $this->access = new AccessCredentials();
        $this->access->setIdentifier('1234567888');
        $this->access->setSecret('AbcDefG');
        $this->consumer = new ConsumerCredentials;
        $this->consumer->setIdentifier('987654321');
        $this->consumer->setSecret('GfeDcbA');
        $this->client = $this->getMock('Wheedle\TwitterClient', ['get', 'post'], [$this->access, $this->consumer]);
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
     * Test to ensure the get method in retrieve is being called correct
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
            ->method('get')
            ->with(
                'https://api.twitter.com/1.1/statuses/show/1.json',
                [
                    'headers' => [
                        'Authorization' => $this->client->getAuthorizationHeader()
                    ]
                ]
            )
            ->will($this->returnValue($this->response));
        $tweet = new Tweet($this->client);
        $tweet->retrieve(1);
    }
}