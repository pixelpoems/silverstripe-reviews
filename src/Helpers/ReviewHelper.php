<?php

namespace ilateral\SilverStripe\Reviews\Helpers;

use SilverStripe\Core\Injector\Injectable;


class ReviewHelper
{
    /**
     * Create a html string from the min and max values, using
     * the provided HTML string
     * 
     * @param int    $min  Initial variable
     * @param int    $max  Final value
     * @param string $html The html to use
     * 
     * @return string
     */
    public static function getStarsFromValues($min, $max, $html = "&#9733;", $divider = " ")
    {
        $return = [];

        for ($i = $min; $i <= $max; $i++) {
            $return[] = $html;
        }

        return implode($divider, $return);
    }
}