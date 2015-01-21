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
}