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
     * @param Array $options optional parameters to refine a search
     *   - trim_user boolean returns a user object with just numerical ID when true
     *   - include_my_retweet boolean when true any tweets RT'd by authenticated user will have current_user_retweet node
     *   - include_entites boolean entities node will be excluded when set to false
     * @return string
     *
     * Method to retrieve a tweet by id
     */
    public function retrieve($id, Array $options = [])
    {
        try {
            $availableOptions = [
                'trim_user',
                'include_my_retweet',
                'include_entites'
            ];

            $options = $this->filterOptions($availableOptions, $options);
            $queryString = '?';
            $queryString .= http_build_query($options);
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
     * @param Array $options optional parameters to refine the output
     *   - count int number of tweets to return up to 200
     *   - since_id int returns results with an ID more recent than the provided ID
     *   - max_id int returns results with an ID older than the provided ID
     *   - trim_user boolean when true returns the user object with only an ID
     *   - contributor_details boolean when true enhances the contributors element of the response
     *   - include_entities boolean entities node will be excluded when set to false
     * @return string
     *
     * Retrieve a collection of mentions for the authenticated user
     */
    public function retrieveMentions(Array $options = [])
    {
        try {
            $availableOptions = [
                'count',
                'since_id',
                'max_id',
                'trim_user',
                'contributor_details',
                'include_entites'
            ];

            $options = $this->filterOptions($availableOptions, $options);
            $queryString = '?';
            $queryString .= http_build_query($options);
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

    /**
     * @param Array $options optional parameters to refine a search
     *   - user_id int user id for whom to return results for (if blank defaults to authenticated user)
     *   - screen_name string screen name for whom to return results for (if blank, defaults to authenticated user)
     *   - since_id int returns results with an ID more recent than the provided ID
     *   - count int number of results to return, up to 200
     *   - max_id int returns results with an ID older than the provided ID
     *   - trim_user boolean when true returns the user object with only an ID
     *   - exclude_replies boolean when true, prevents replies from appearing in the returned timeline
     *   - contributor_details boolean when true enhances the contributors element of the response
     *   - include_rts boolean when false the timeline will strip any native retweets
     * @return string
     *
     * A method to return the tweets in a users timeline
     */
    public function retrieveUserTimeline(Array $options = [])
    {
        try {
            $availableOptions = [
                'user_id',
                'screen_name',
                'since_id',
                'count',
                'max_id',
                'trim_user',
                'exclude_replies',
                'contributor_details',
                'include_rts'
            ];

            // filter out options that aren't available
            $options = $this->filterOptions($availableOptions, $options);
            $queryString = '?';
            $queryString .= http_build_query($options);
            $endpoint = $this->baseUrl . 'user_timeline.json' . $queryString;
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

    /**
     * @param Array $availableOptions An array of available parameters
     * @param Array $options An array of the provided parameters
     * @return Arrary
     *
     * Method to filter out any unavailable parameters
     */
    public function filterOptions($availableOptions, $options)
    {
        array_walk($options, function($key, $value) use ($availableOptions) {
            if (!array_key_exists($key, $availableOptions)) {
                unset($options[$key]);
            }
        });
        return $options;
    }
}
