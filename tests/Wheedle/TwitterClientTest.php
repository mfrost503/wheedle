<?php
namespace Test;

use Wheedle\TwitterClient;
use GuzzleHttp\Client;
use Snaggle\Client\Header\Header;
use Snaggle\Client\Signatures\HmacSha1;

/**
 * @author Matt Frost <mfrost.design@gmail.com>
 * @package Test
 * @subpackage Wheedle
 */
class TwitterClientTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->twitter = new TwitterClient();
        $this->client = new Client();
    }

    public function tearDown()
    {
        unset($this->twitter);
        unset($this->client);
    }

    /**
     * Test to ensure that a new instance of Client is returned when the client property is null
     */
    public function testEnsureClientInstanceReturnedWhenPropertyIsNull()
    {
        $httpClient = $this->twitter->getClient();
        $this->assertInstanceOf('GuzzleHttp\Client', $httpClient);
    }

    /**
     * Test to ensure that the provided instance of Client is returned when client property is set
     */
    public function testEnsureClientPropertyReturnedOnGet()
    {
        $this->twitter->setClient($this->client);
        $client = $this->twitter->getClient();
        $this->assertSame($client, $this->client);
    }
}