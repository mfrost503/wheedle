<?php
namespace Wheedle;

/**
 * @author Matt Frost <mfrost.design@gmail.com
 * @license MIT http://opensource.org/licenses/MIT
 * @package Wheedle
 *
 * A trait to handle the filtering of query string params
 */
trait OptionsFilter
{
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