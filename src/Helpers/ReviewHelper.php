<?php

declare(strict_types=1);

namespace ilateral\SilverStripe\Reviews\Helpers;

class ReviewHelper
{
    /**
     * Create a html string from the min and max values, using
     * the provided HTML string
     *
     * @param int    $min  Initial variable
     * @param int    $max  Final value
     * @param string $html The html to use
     */
    public static function getStarsFromValues($min, $max, $html = "&#9733;", $divider = " "): string
    {
        $return = [];

        for ($i = $min; $i <= $max; ++$i) {
            $return[] = $html;
        }

        return implode($divider, $return);
    }
}
