<?php
namespace Wheedle;

use \GuzzleHttp\Client;
use \Snaggle\Client\Header\Header;
use \Snaggle\Client\Signatures\HmacSha1;
use \Snaggle\Client\Signatures\SignatureInterface;
use \Snaggle\Client\Credentials\AccessCredentials;
use \Snaggle\Client\Credentials\ConsumerCredentials;

/**
 * @author Matt Frost
 * @license http://opensource.org/licenses/MIT MIT
 * @package Wheedle
 *
 * A Twitter client that extends Guzzle or encapsulates the OAuth madness
 */
class TwitterClient extends Client
{
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
     * @var Snaggle\Client\Credentials\AccessCredentials
     * A Snaggle\AccessCredentials instance with the appropriate key/secret
     */
    private $accessCredentials;

    /**
     * @var Snaggle\Client\Credentials\ConsumerCredentials
     * A Snaggle\ConsumerCredentials instance with the appropriate key/secret
     */
    private $consumerCredentials;

    /**
     * @var string $resourceUrl
     * String representing the location of the resource
     */
    private $resourceUrl;

    /**
     * @var string $httpMethod
     * String representing the HTTP method with which to use the request
     */
    private $httpMethod;

    /**
     * @var int $timestamp
     *
     * A timestamp for the request
     */
    private $timestamp = 0;

    /**
     * @var string $nonce
     *
     * A nonce for the request
     */
    private $nonce = null;

    /**
     * @var string $verifier
     *
     * Verifier that is part of the temporary token exchange
     */
    private $verifier = null;

    /**
     * @var Array $postFields
     *
     * Post requests require any form fields to be included for the signature, you can set them here
     */
    private $postFields = [];

    public function __construct(AccessCredentials $accessCredentials, ConsumerCredentials $consumerCredentials)
    {
        $this->accessCredentials = $accessCredentials;
        $this->consumerCredentials = $consumerCredentials;
        parent::__construct();
    }

    /**
     * @return Snaggle\Client\Header\Header
     *
     * Accessor method to retrieve a set header or create a new instance of header
     */
    public function getHeader()
    {
        if (!$this->header instanceof Header) {
            $this->header = new Header;
        }
        return $this->header;
    }

    /**
     * @param Snaggle\Client\Header\Header $header
     *
     * Access method to set an instance of header
     */
    public function setHeader(Header $header)
    {
        $this->header = $header;
    }

    /**
     * @return Snaggle\Client\Signatures\HmacSha1
     *
     * Accessor method to retrieve a set Signature or create a new instance
     */
    public function getSignature()
    {
        if (!$this->signature instanceof HmacSha1) {
            $this->signature = new HmacSha1($this->consumerCredentials, $this->accessCredentials);
        }
        return $this->signature;
    }

    /**
     * @param Snaggle\Client\Signatures\HmacSha1 $signature
     *
     * Accessor method for setting a preconfigured signature which will set the other
     * properties from the data contained in the signature
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
     * @return string
     *
     * Method to return the Resource URL
     */
    public function getResourceUrl()
    {
        return $this->resourceUrl;
    }

    /**
     * @param string $resourceUrl
     *
     * Method to set the resource url
     */
    public function setResourceUrl($url)
    {
        $this->resourceUrl = $url;
    }

    /**
     * @return string
     *
     * Method to retrieve the HTTP Method
     */
    public function getHttpMethod()
    {
        return strtoupper($this->httpMethod);
    }

    /**
     * @param string $httpMethod
     *
     * Method to set the Http Method
     */
    public function setHttpMethod($httpMethod)
    {
        $this->httpMethod = strtoupper($httpMethod);
    }

    /**
     * @return int
     *
     * Method to get a signed timestamp
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param int $timestamp
     *
     * Method to set a timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @return string
     *
     * Method to retrieve the nonce
     */
    public function getNonce()
    {
        return $this->nonce;
    }

    /**
     * @param string $nonce
     *
     * Method to set a nonce
     */
    public function setNonce($nonce)
    {
        $this->nonce = $nonce;
    }

    /**
     * @return string
     *
     * Method to set the OAuth verifier for token requests
     */
    public function getVerifier()
    {
        return $this->verifier;
    }

    /**
     * @param string $verifier
     *
     * Method to set the verifier for token requests
     */
    public function setVerifier($verifier)
    {
        $this->verifier = $verifier;
    }

    /**
     * @return Array
     *
     * Method for retrieving the set post fields
     */
    public function getPostFields()
    {
        return $this->postFields;
    }

    /**
     * @param Array $postFields
     *
     * Method for setting the post fields
     */
    public function setPostFields(Array $postFields)
    {
        $this->postFields = $postFields;
    }

    /**
     * @return string
     *
     * Method to build the Authorization Header
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
}