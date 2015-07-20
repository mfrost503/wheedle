<?php
namespace Test;

use Wheedle\TwitterClient;
use GuzzleHttp\Client;
use GuzzleHttp\Response;
use GuzzleHttp\Request;
use GuzzleHttp\Exception\ClientException;
use Snaggle\Client\Header\Header;
use Snaggle\Client\Signatures\HmacSha1;
use Snaggle\Client\Credentials\AccessCredentials;
use Snaggle\Client\Credentials\ConsumerCredentials;

/**
 * @author Matt Frost <mfrost.design@gmail.com>
 * @package Test
 * @subpackage Wheedle
 */
class TwitterClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Setup
     */
    public function setUp()
    {
        $this->header = new Header();
        $this->accessToken = new AccessCredentials('12325234', 'abcdefg');
        $this->consumerToken = new ConsumerCredentials('badbadman', '1234569029zvaed');
        $this->signature = new HmacSha1($this->consumerToken, $this->accessToken);
        $this->twitter = new TwitterClient($this->accessToken, $this->consumerToken);
    }

    /**
     * Tear Down
     */
    public function tearDown()
    {
        unset($this->twitter);
        unset($this->header);
        unset($this->accessToken);
        unset($this->consumerToken);
        unset($this->signature);
    }

    /**
     * Test to ensure client can be set
     */
    public function testClientCanBeSet()
    {
        $client = new Client();
        $this->twitter->setClient($client);
        $this->assertInstanceOf('\GuzzleHttp\Client', $client);
    }

    /**
     * Test for returning a header if none is explicitly set
     */
    public function testHeaderInstanceReturnedWhenNotSet()
    {
        $header = $this->twitter->getHeader();
        $this->assertInstanceOf('Snaggle\Client\Header\Header', $header);
    }

    /**
     * Test for returning a previous set instance of Header
     */
    public function testGetHeaderReturnsSetInstance()
    {
        $this->twitter->setHeader($this->header);
        $header = $this->twitter->getHeader();
        $this->assertSame($header, $this->header);
    }

    /**
     * test for returning a signature if none is explicitly set
     */
    public function testSignatureInstaceReturnedWhenNotSet()
    {
        $signature = $this->twitter->getSignature();
        $this->assertInstanceOf('Snaggle\Client\Signatures\HmacSha1', $signature);
    }

    /**
     * test for retrieving a previously set signature
     */
    public function testGetSignatureReturnsSetInstance()
    {
        $this->twitter->setSignature($this->signature);
        $signature = $this->twitter->getSignature();
        $this->assertSame($signature, $this->signature);
    }

    /**
     * test to ensure the set properties are in the header
     */
    public function testEnsureHeaderIsBeingCreated()
    {
        $timestamp = 1420302568;
        $nonce = '3e448845e49f46fc47335a8537333ada';
        $signature = new HmacSha1($this->consumerToken, $this->accessToken);
        $signature->setResourceURL('http://api.twitter.com/1.1/statuses/show/460095281871073282.json');
        $signature->setHttpMethod('GET');
        $signature->setNonce($nonce);
        $signature->setTimestamp($timestamp);
        $matchSignature = rawurlencode($signature->sign());
        $this->twitter->setResourceUrl('http://api.twitter.com/1.1/statuses/show/460095281871073282.json');
        $this->twitter->setHttpMethod('GET');
        $this->twitter->setNonce($nonce);
        $this->twitter->setTimestamp($timestamp);
        $header = $this->twitter->getAuthorizationHeader();
        $this->assertGreaterThan(0, strpos($header, $matchSignature), $matchSignature . ' is not in ' . $header);
        $this->assertGreaterThan(0, strpos($header, $this->accessToken->getIdentifier()));
    }

    /**
     * Test to ensure the verifier is being set in the header
     */
    public function testEnsureVerifierIsSet()
    {
        $verifier = '1234abc';
        $this->twitter->setVerifier($verifier);
        $this->twitter->setHttpMethod('get');
        $this->twitter->setResourceURL('http://api.twitter.com/1.1/statues/show/1234567.json');
        $header = $this->twitter->getAuthorizationHeader();
        $this->assertContains('oauth_verifier="1234abc"', $header);
    }

    /**
     * Test to ensure the Post Fields can be set to an array
     */
    public function testEnsurePostFieldsCanBeSet()
    {
        $postFields = ['name' => 'Twitter Client'];
        $expected = ['name' => rawurlencode('Twitter Client')];
        $this->twitter->setPostFields($postFields);
        $this->assertAttributeEquals($expected, 'postFields', $this->twitter);
    }

    /**
     * Test to ensure that makeGetRequest operates correctly
     */
    public function testEnsureMakeGetRequestOperatesCorrectly()
    {
        $url = 'statuses/show/460095281871073282.json';
        $expectedURL = 'https://api.twitter.com/1.1/statuses/show/460095281871073282.json?trim_user=1';
        $options = ['trim_user' => true];
        $response = $this->getMock('\GuzzleHttp\Response', ['getBody']);
        $response->expects($this->once())
            ->method('getBody')
            ->will($this->returnValue(json_encode(['test' => '123abc', 'name' => 'test-data'])));
        $this->twitter->setTimestamp(time());
        $this->twitter->setNonce('1234abc');
        $this->twitter->setHttpMethod('get');
        $this->twitter->setResourceURL($expectedURL);
        $authorizationHeader = $this->twitter->getAuthorizationHeader();
        $client_options = [
            'headers' => [
                'Authorization' => $authorizationHeader
            ]
        ];
        $client = $this->getMock('\GuzzleHttp\Client', ['get']);
        $client->expects($this->once())
            ->method('get')
            ->with($expectedURL, $client_options)
            ->will($this->returnValue($response));
        $this->twitter->setClient($client);
        $get = $this->twitter->get($url, $options);
        $data = json_decode($get, true);
        $this->assertEquals($data['test'], '123abc');
    }

    /**
     * Test to ensure that makeGetRequest throws an appropriate error
     * @expectedException \Wheedle\Exceptions\UnauthorizedRequestException
     */
    public function testEnsureMakeGetRequestThrowsExceptionCorrectly()
    {
        $url = 'statuses/show/460095281871073282.json';
        $expectedURL = 'https://api.twitter.com/1.1/statuses/show/460095281871073282.json?trim_user=1';
        $options = ['trim_user' => true];
        $request = $this->getMockBuilder('\GuzzleHttp\Message\Request')
            ->disableOriginalConstructor()
            ->getMock();
        $response = $this->getMockBuilder('\GuzzleHttp\Message\Response')
            ->setMethods(['getStatusCode'])
            ->disableOriginalConstructor()
            ->getMock();
        $response->expects($this->once())
            ->method('getStatusCode')
            ->will($this->returnValue(401));
        $exception = new \GuzzleHttp\Exception\ClientException("Unauthorized", $request, $response);
        $this->twitter->setTimestamp(time());
        $this->twitter->setNonce('1234abc');
        $this->twitter->setHttpMethod('get');
        $this->twitter->setResourceURL($expectedURL);
        $authorizationHeader = $this->twitter->getAuthorizationHeader();
        $client_options = [
            'headers' => [
                'Authorization' => $authorizationHeader
            ]
        ];
        $client = $this->getMock('\GuzzleHttp\Client', ['get']);
        $client->expects($this->once())
            ->method('get')
            ->with($expectedURL, $client_options)
            ->will($this->throwException($exception));
        $this->twitter->setClient($client);
        $get = $this->twitter->get($url, $options);
    }
}
