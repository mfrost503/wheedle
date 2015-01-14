<?php
namespace Wheedle;

use \GuzzleHttp\Exception\ClientException;

/**
 * @author Matt Frost <mfrost.design@gmail.com>
 * @package Wheedle
 * @license MIT http://opensource.org/licenses/MIT
 */
class Tweet
{
    /**
     * End point constants
     */
    const USER_TIMELINE_ENDPOINT = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
    const HOME_TIMELINE_ENDPOINT = 'https://api.twitter.com/1.1/statuses/home_timeline.json';
    const MENTIONS_ENDPOINT = 'https://api.twitter.com/1.1/statuses/mentions_timeline.json';
    const MY_RETWEETS_ENDPOINT = 'https://api.twitter.com/1.1/statuses/retweets_of_me.json';
    const RETRIEVE_ENDPOINT = 'https://api.twitter.com/1.1/statuses/show/';
    const RETWEETS_ENDPOINT = 'https://api.twitter.com/1.1/statuses/retweets/';
    const UPDATE_ENDPOINT = 'https://api.twitter.com/1.1/statuses/update.json';

    /**
     * Use the options filter trait to eliminate unavailable query string params
     */
    use OptionsFilter;

    /**
     * The Twitter Client for making the requests
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
     * Method to retrieve a tweet by id
     *
     * @param int $id
     * @param Array $options optional parameters to refine a search
     *   - trim_user boolean returns a user object with just numerical ID when true
     *   - include_my_retweet boolean when true any tweets RT'd by authenticated user will have current_user_retweet node
     *   - include_entites boolean entities node will be excluded when set to false
     * @return string
     */
    public function retrieve($id, Array $options = [])
    {
        $availableOptions = [
            'trim_user',
            'include_my_retweet',
            'include_entites'
        ];

        $options = $this->filterOptions($availableOptions, $options);
        return $this->client->makeGetRequest(self::RETRIEVE_ENDPOINT . $id .'.json', $options);
    }

    /**
     * Retrieve a collection of mentions for the authenticated user
     *
     * @param Array $options optional parameters to refine the output
     *   - count int number of results to return, up to 200, if omitted returns 20
     *   - since_id int returns results with an ID more recent than the provided ID
     *   - max_id int returns results with an ID older than the provided ID
     *   - trim_user boolean when true returns the user object with only an ID
     *   - contributor_details boolean when true enhances the contributors element of the response
     *   - include_entities boolean entities node will be excluded when set to false
     * @return string
     */
    public function retrieveMentions(Array $options = [])
    {
        $availableOptions = [
            'contributor_details',
            'count',
            'include_entites',
            'max_id',
            'since_id',
            'trim_user'
        ];

        $options = $this->filterOptions($availableOptions, $options);
        return $this->client->makeGetRequest(self::MENTIONS_ENDPOINT, $options);
    }

    /**
     * A method to return the tweets in a users timeline
     *
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
     */
    public function retrieveUserTimeline(Array $options = [])
    {
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
        return $this->client->makeGetRequest(self::USER_TIMELINE_ENDPOINT, $options);
    }

    /**
     * Method to retrieve the home timeline for the authenticated user
     *
     * @param Array $options
     *   - count int number of results to return, up to 200, if omitted returns 20
     *   - since_id int returns results with an ID more recent than the provided ID
     *   - max_id int returns results with an ID older than the provided ID
     *   - include_entities boolean entities node will be excluded when set to false
     *   - exclude_replies boolean when true, prevents replies from appearing in the returned timeline
     *   - contributor_details boolean when true enhances the contributors element of the response
     * @return string
     */
    public function retrieveHomeTimeline(Array $options = [])
    {
        $availableOptions = [
            'count',
            'since_id',
            'max_id',
            'include_entities',
            'exclude_replies',
            'contributor_details'
        ];

        $options = $this->filterOptions($availableOptions, $options);
        return $this->client->makeGetRequest(self::HOME_TIMELINE_ENDPOINT, $options);
    }

    /**
     * Retrieving a collection of your tweets that were retweeted by others
     *
     * @param Array $options
     *   - count int number of results to return, up to 200, if omitted returns 20
     *   - since_id int returns results with an ID more recent than the provided ID
     *   - max_id int returns results with an ID older than the provided ID
     *   - trim_user boolean when true returns the user object with only an ID
     *   - include_entities boolean tweet entities node will be excluded when set to false
     *   - include_user_entities boolean user entities node will be excluded when set to false
     * @return string
     */
    public function retrieveMyRetweets(Array $options = [])
    {
        $availableOptions = [
            'count',
            'include_entities',
            'include_user_entities',
            'max_id',
            'since_id',
            'trim_user',
        ];

        $options = $this->filterOptions($availableOptions, $options);
        return $this->client->makeGetRequest(self::MY_RETWEETS_ENDPOINT, $options);
    }

    /**
     * Retrieve the retweets for a specific tweet
     *
     * @param int $id ID of the tweet to retrieve the retweeters
     * @param Array $options
     *   - count int number of results to return up to 100, 100 is the max allowed
     *   - trim_user boolean when true returns the user object with only an ID
     * @return string
     */
    public function retrieveRetweets($id, $options)
    {
        $availableOptions = [
            'count',
            'trim_user'
        ];

        $options = $this->filterOptions($availableOptions, $options);
        return $this->client->makeGetRequest(self::RETWEETS_ENDPOINT . $id . '.json', $options);
    }

    /**
     * Method to create a Tweet
     *
     * @param string $status The status or tweet that will be sent
     * @param Array $options Optional parameters for the request
     *   - in_reply_to_status_id int the ID for the tweet you are replying to, must @ mention original author
     *   - possibly_sensitive boolean should be set when tweet contains nudity, violence or other gross stuff
     *   - lat float latitude
     *   - long float longitude
     *   - place_id string a place in the world
     *   - display_coordinates boolean when true will put a pin an exact coordinates tweet was sent from
     *   - trim_user boolean when true returns the user object with only an ID
     *   - media_ids string a list of media ids to associate to a tweet
     */
    public function create($status, $options = [])
    {
        $availableOptions = [
            'in_reply_to_status_id',
            'possibly_sensitive',
            'lat',
            'long',
            'place_id',
            'display_coordinates',
            'trim_user',
            'media_ids'
        ];

        $options = $this->filterOptions($availableOptions, $options);
        $options['status'] = rawurlencode($status);
        return $this->client->makePostRequest(self::UPDATE_ENDPOINT, $options);
    }
}
