<?php
namespace Wheedle;

use \GuzzleHttp\Client;
use \GuzzleHttp\Exception\ClientException;
use \Snaggle\Client\Header\Header;
use \Snaggle\Client\Signatures\HmacSha1;
use \Snaggle\Client\Signatures\SignatureInterface;
use \Snaggle\Client\Credentials\AccessCredentials;
use \Snaggle\Client\Credentials\ConsumerCredentials;

/**
 * A Twitter client that extends Guzzle or encapsulates the OAuth madness
 *
 * @author Matt Frost
 * @license http://opensource.org/licenses/MIT MIT
 * @package Wheedle
 */
class TwitterClient
{
    /**
     * HTTP Client capable of making HTTP Requests
     *
     * @var \GuzzleHttp\Client $client
     */
    private $client;

    /**
     * Header object that is used to generate the OAuth 1.0 header
 	 *
     * @var \Snaggle\Client\Header\Header $header
     */
    private $header;

    /**
     * A signature type used to generate the OAuth 1.0 signature
	 *
     * @var \Snaggle\Client\Signatures\SignatureInterface $signature
     */
    private $signature;

    /**
     * A Snaggle\AccessCredentials instance with the appropriate key/secret
	 *
     * @var \Snaggle\Client\Credentials\AccessCredentials
     */
    private $accessCredentials;

    /**
     * A Snaggle\ConsumerCredentials instance with the appropriate key/secret
	 *
     * @var \Snaggle\Client\Credentials\ConsumerCredentials
     */
    private $consumerCredentials;

    /**
     * String representing the location of the resource
	 *
     * @var string $resourceUrl
     */
    private $resourceUrl;

    /**
     * String representing the HTTP method with which to use the request
	 *
     * @var string $httpMethod
     */
    private $httpMethod;

    /**
     * A timestamp for the request
     *
     * @var int $timestamp
     */
    private $timestamp = 0;

    /**
     * A nonce for the request
     *
     * @var string $nonce
     */
    private $nonce = null;

    /**
     * Verifier that is part of the temporary token exchange
     *
     * @var string $verifier
     */
    private $verifier = null;

    /**
     * Post requests require any form fields to be included for the signature, you can set them here
     *
     * @var Array $postFields
     */
    private $postFields = [];

    /**
     * @param AccessCredentials $accessCredentials
     * @param ConsumerCredentials $consumerCredentials
     */
    public function __construct(AccessCredentials $accessCredentials, ConsumerCredentials $consumerCredentials)
    {
        $this->accessCredentials = $accessCredentials;
        $this->consumerCredentials = $consumerCredentials;
        $this->getClient(); 
    }

    /**
     * Method to retrieve/create a instance of Guzzle Http Client
     *
     * @return Client
     */
    public function getClient()
    {
        if (!$this->client instanceof Client) {
            $this->client = new Client;
        }
        return $this->client;
    }

    /**
     * Method set an instance of Guzzle HTTP client
     *
     * @param Client $client
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Accessor method to retrieve a set header or create a new instance of header
     *
     * @return Header
     */
    public function getHeader()
    {
        if (!$this->header instanceof Header) {
            $this->header = new Header;
        }
        return $this->header;
    }

    /**
     * Access method to set an instance of header
     *
     * @param Header $header
     */
    public function setHeader(Header $header)
    {
        $this->header = $header;
    }

    /**
     * Accessor method to retrieve a set Signature or create a new instance
     *
     */
    public function getSignature()
    {
        if (!$this->signature instanceof HmacSha1) {
            $this->signature = new HmacSha1($this->consumerCredentials, $this->accessCredentials);
        }
        return $this->signature;
    }

    /**
     * Accessor method for setting a preconfigured signature which will set the other
     * properties from the data contained in the signature
     *
     * @param HmacSha1 $signature
     */
    public function setSignature(HmacSha1 $signature)
    {
        $this->signature = $signature;
        $this->resourceUrl = $signature->getResourceURL();
        $this->httpMethod = $signature->getHttpMethod();
        $this->nonce = $signature->getNonce();
        $this->timestamp = $signature->getTimestamp();
        $this->verifier = $signature->getVerifier();
        $this->postFields = $signature->getPostFields();
    }

    /**
     * Method to return the Resource URL
     *
     * @return string
     */
    public function getResourceUrl()
    {
        return $this->resourceUrl;
    }

    /**
     * Method to set the resource url
     *
     * @param string $url
     */
    public function setResourceUrl($url)
    {
        $this->resourceUrl = $url;
    }

    /**
     * Method to retrieve the HTTP Method
     *
     * @return string
     */
    public function getHttpMethod()
    {
        return strtoupper($this->httpMethod);
    }

    /**
     * Method to set the Http Method
     *
     * @param string $httpMethod
     */
    public function setHttpMethod($httpMethod)
    {
        $this->httpMethod = strtoupper($httpMethod);
    }

    /**
     * Method to get a signed timestamp
     *
     * @return int
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Method to set a timestamp
     *
     * @param int $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    /**
     * Method to retrieve the nonce
     *
     * @return string
     */
    public function getNonce()
    {
        return $this->nonce;
    }

    /**
     * Method to set a nonce
     *
     * @param string $nonce
     */
    public function setNonce($nonce)
    {
        $this->nonce = $nonce;
    }

    /**
     * Method to set the OAuth verifier for token requests
     *
     * @return string
     */
    public function getVerifier()
    {
        return $this->verifier;
    }

    /**
     * Method to set the verifier for token requests
     *
     * @param string $verifier
     */
    public function setVerifier($verifier)
    {
        $this->verifier = $verifier;
    }

    /**
     * Method for retrieving the set post fields
     *
     * @return Array
     */
    public function getPostFields()
    {
        return $this->postFields;
    }

    /**
     * Method for setting the post fields
     *
     * @param Array $postFields
     */
    public function setPostFields(Array $postFields)
    {
        $this->postFields = $postFields;
    }

    /**
     * Method to build the Authorization Header
     *
     * @return string
     */
    public function getAuthorizationHeader()
    {
        $signature = $this->getSignature();
        $signature->setResourceURL($this->resourceUrl);
        $signature->setHttpMethod($this->httpMethod);
        
        if ($this->timestamp !== 0) {
            $signature->setTimestamp($this->timestamp);
        }

        if ($this->nonce !== null) {
            $signature->setNonce($this->nonce);
        }

        if ($this->verifier !== null) {
            $signature->setVerifier($this->verifier);
        }

        $header = $this->getHeader();
        $header->setSignature($signature);
        return $header->createAuthorizationHeader();
    }

    /**
     * Method to execute a GET request
     *
     * @param string $endpoint - endpoint to hit
     * @param Array $options parameters for the query string
     * @return string response from the Twitter endpoint
     */
    public function makeGetRequest($endpoint, $options)
    {
        $queryString = (empty($options)) ? '' : '?';
        $queryString .= http_build_query($options);
        $endpoint = $endpoint . $queryString;
        $this->setHttpMethod('GET');
        $this->setResourceUrl($endpoint);
        try {
            $response = $this->client->get($endpoint, [
                'headers' => [
                    'Authorization' => $this->getAuthorizationHeader()
                ]
            ]);
            return $response->getBody();
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return $e->getMessage();
        }
    }

    /**
     * Method to execute a POST request
     *
     * @param string $endpoint - end point to hit
     * @param Array $options - parameters/post body
     * @return string response from Twitter Endpoint
     */
    public function makePostRequest($endpoint, $options)
    {
        $this->setHttpMethod('POST');
        $this->setResourceUrl($endpoint);
        $this->setPostFields($options);
        try {
            $response = $this->client->post($endpoint, [
                'headers' => [
                    'Authorization' => $this->getAuthorizationHeader()
                ],
                'body' => http_build_query($options)
            ]);
            return $response->getBody();
        } catch(\GuzzleHttp\Exception\ClientException $e) {
            return $e->getMessage();
        }
    }
}
