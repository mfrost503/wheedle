<?php
namespace Wheedle;

use \GuzzleHttp\Exception\ClientException;

/**
 * A collection of convenience methods for the direct message endpoints for
 * the twitter API
 *
 * @author Matt Frost <mfrost.design@gmail.com>
 * @package Wheedle
 * @license MIT http://opensource.org/licenses/MIT
 */
class DirectMessage
{
    /**
     * ENDPOINT CONSTANTS
     */
    const RETRIEVE_MESSAGES_ENDPOINT = 'direct_messages.json';
    const RETRIEVE_SENT_MESSAGES_ENDPOINT = 'direct_messages/sent.json';

    use OptionsFilter;

    /**
     * Twitter Client, needed for making requests
     *
     * @var TwitterClient $client
     */
    private $client;

    /**
     * Constructor
     *
     * @param TwitterClient $client
     */
    public function __construct(TwitterClient $client)
    {
        $this->client = $client;
    }

    /**
     * Method to retrieve the most recent direct messages sent to the
     * authenticated user
     *
     * @param Array $options
     *   - since_id int returns results with an ID more recent than the provided ID
     *   - max_id int returns results with an ID older than the provided ID
     *   - count int number of results to return, up to 200, if omitted returns 20
     *   - include_entities boolean entities node will be excluded when set to false
     *   - skip_status boolean statues will not be returned with the user objects when true
     * @return string
     */
    public function retrieveLatestMessages(Array $options = [])
    {
        $availableOptions = [
            'since_id',
            'max_id',
            'count',
            'include_entities',
            'skip_status'
        ];
        $options = $this->filterOptions($availableOptions, $options);
        return $this->client->makeGetRequest(self::RETRIEVE_MESSAGES_ENDPOINT, $options);
    }

    /**
     * Method to retrieve the most recent direct messages sent from the
     * authenticated user
     *
     * @param Array $options
     *   - since_id int returns results with an ID more recent than the provided ID
     *   - max_id int returns results with an ID older than the provided ID
     *   - count int number of results to return, up to 200, if omitted returns 20
     *   - include_entities boolean entities node will be excluded when set to false
     *   - page int the page of results to retrieve
     * @return string
     */
    public function retrieveLatestSentMessages(Array $options = [])
    {
        $availableOptions = [
            'since_id',
            'max_id',
            'count',
            'include_entities',
            'page'
        ];
        $options = $this->filterOptions($availableOptions, $options);
        return $this->client->makeGetRequest(self::RETRIEVE_SENT_MESSAGES_ENDPOINT, $options);
    }
}
