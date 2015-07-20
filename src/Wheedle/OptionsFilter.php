<?php
namespace Wheedle;

/**
 * A trait to handle the filtering of query string params
 *
 * @author Matt Frost <mfrost.design@gmail.com
 * @license MIT http://opensource.org/licenses/MIT
 * @package Wheedle
 */
trait OptionsFilter
{
    /**
     * Method to filter out any unavailable parameters
     *
     * @param Array $availableOptions An array of available parameters
     * @param Array $options An array of the provided parameters
     * @return Arrary
     */
    public function filterOptions($availableOptions, $options)
    {
        array_walk($options, function ($value, $key) use ($availableOptions, &$options) {
            if (!in_array($key, $availableOptions)) {
                unset($options[$key]);
            }
        });
        return $options;
    }
}
