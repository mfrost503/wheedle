<?php
namespace Wheedle;

/**
 * @author Matt Frost <mfrost.design@gmail.com>
 * @package Wheedle
 * @license MIT http://opensource.org/licenses/MIT
 */
class Tweet
{
    /**
     * @var TwitterClient $client
     *
     * The Twitter Client for making the requests
     */
    private $client;

    /**
     * @var string $baseUrl
     *
     * Base Url for the all the status related endpoints
     */
    private $baseUrl = 'https://api.twitter.com/1.1/statuses/';

    public function __construct(TwitterClient $client)
    {
        $this->client = $client;
    }

    /**
     * @param int $id
     * @param Array $options
     * @return string
     *
     * Method to retrieve a tweet by id
     */
    public function retrieve($id, Array $options = [])
    {
        try {
            $endpoint = $this->baseUrl . 'show/' . $id . '.json';
            $this->client->setHttpMethod('get');
            $this->client->setResourceUrl($endpoint);
            $response = $this->client->get($endpoint, [
                'headers' => [
                    'Authorization' => $this->client->getAuthorizationHeader()
                ]
            ]);
            return $response->getBody();
        } catch(GuzzleHttp\Exception\ClientException $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param Array $options
     * @return string
     *
     * Retrieve a collection of mentions for the authenticated user
     */
    public function retrieveMentions(Array $options = [])
    {
        try {
            $queryString = '?';
            foreach ($options as $key => $value) {
                $queryString .= $key .'=' . $value . '&';
            }
            $endpoint = $this->baseUrl . 'mentions_timeline.json' . substr($queryString, 0, -1);
            $this->client->setHttpMethod('GET');
            $this->client->setResourceUrl($endpoint);
            $response = $this->client->get($endpoint, [
                'headers' => [
                    'Authorization' => $this->client->getAuthorizationHeader()
                ]
            ]);
            return $response->getBody();
        } catch (GuzzleHttp\Exception\ClientException $e) {
            return $e->getMessage();
        }
    }
}
