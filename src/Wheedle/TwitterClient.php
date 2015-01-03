<?php
namespace Wheedle;

use GuzzleHttp\Client;
use Snaggle\Client\Header\Header;
use Snaggle\Client\Signatures\SignatureInterface;

/**
 * @author Matt Frost
 * @license http://opensource.org/licenses/MIT MIT
 * @package Wheedle
 *
 * A Twitter client class that is responsible for storing the HttpRequest Object
 */
class TwitterClient
{
    /**
     * @var GuzzleHttp\Client $client
     * Http client used to make the requests
     */
    private $client;

    /**
     * @var Snaggle\Client\Header\Header $header
     * Header object that is used to generate the OAuth 1.0 header
     */
    private $header;

    /**
     * @var Snaggle\Client\Signatures\SignatureInterface $signature
     * A signature type used to generate the OAuth 1.0 signature
     */
    private $signature;

    /**
     * @return GuzzleHttp\Client
     *
     * An accessor method for retrieving a preconfigured HttpClient or a new one
     */
    public function getClient()
    {
        if ($this->client instanceof Client) {
            return $this->client;
        }
        return new Client;
    }

    /**
     * @param \GuzzleHttp\Client $client
     *
     * Set the Http Client
     */
    public function setClient(\GuzzleHttp\Client $client)
    {
        $this->client = $client;
    }
}