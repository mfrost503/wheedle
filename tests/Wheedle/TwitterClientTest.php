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
        $this->accessToken = new AccessCredentials;
        $this->accessToken->setIdentifier('12325234');
        $this->accessToken->setSecret('abcdefg');
        $this->consumerToken = new ConsumerCredentials;
        $this->consumerToken->setIdentifier('badbadman');
        $this->consumerToken->setSecret('1234569029zvaed');
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
     * test to ensure http method accessors are working
     */
    public function testHttpMethodAccessors()
    {
        $method = 'Post';
        $this->twitter->setHttpMethod($method);
        $this->assertEquals(strtoupper($method), $this->twitter->getHttpMethod());
    }

    /**
     * test to ensure resourceUrl accessors are working
     */
    public function testResourceUrlAccessors()
    {
        $resourceUrl = 'http://example.com';
        $this->twitter->setResourceUrl($resourceUrl);
        $this->assertEquals($resourceUrl, $this->twitter->getResourceUrl());
    }

    /**
     * test to ensure the set properties are in the header
     */
    public function testEnsureHeaderIsBeingCreated()
    {
        $timestamp = time();
        $nonce = 'testingNonce';
        $signature = new HmacSha1($this->consumerToken, $this->accessToken);
        $signature->setResourceURL('http://example.com');
        $signature->setHttpMethod('POST');
        $signature->setNonce($nonce);
        $signature->setTimestamp($timestamp);
        $matchSignature = rawurlencode($signature->sign());
        $this->twitter->setResourceUrl('http://example.com');
        $this->twitter->setHttpMethod('POST');
        $this->twitter->setNonce($nonce);
        $this->twitter->setTimestamp($timestamp);
        $header = $this->twitter->getAuthorizationHeader();
        $this->assertGreaterThan(0, strpos($header, $matchSignature), $matchSignature . ' is not in ' . $header);
        $this->assertGreaterThan(0, strpos($header, $this->accessToken->getIdentifier()));
    }
}