<?php
namespace Test;

use Wheedle\TwitterClient;
use GuzzleHttp\Client;
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
    public function setUp()
    {
        $this->header = new Header();
        $this->accessToken = new AccessCredentials('12325234', 'abcdefg');
        $this->consumerToken = new ConsumerCredentials('badbadman', '1234569029zvaed');
        $this->signature = new HmacSha1($this->consumerToken, $this->accessToken);
        $this->twitter = new TwitterClient($this->accessToken, $this->consumerToken);
    }

    public function tearDown()
    {
        unset($this->twitter);
        unset($this->header);
        unset($this->accessToken);
        unset($this->consumerToken);
        unset($this->signature);
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
}
