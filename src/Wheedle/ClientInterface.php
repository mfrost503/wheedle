<?php
namespace Wheedle;

/**
 * @author Matt Frost<mfrost.design@gmail.com>
 * @package Wheedle
 */
interface ClientInterface
{
    /**
     * Method to make a get request, wrapper around
     * an http client
     *
     * @param string $endpoint
     * @param array $options
     * @return mixed
     */
    public function makeGetRequest($endpoint, $options);

    /**
     * Method to make a post request, wrapper around
     * an http client
     *
     * @param string $endpoint
     * @param array $options
     * @return mixed
     */
    public function makePostRequest($endpoint, $options);
}
