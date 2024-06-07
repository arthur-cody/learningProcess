<?php

if (! function_exists('array_flatten')) {
    /**
     * Flatten an array of arrays containing strings into a single-dimensional array.
     *
     * @param  array  $array
     * @return array
     */
    function array_flatten($array)
    {
        $result = [];

        foreach ($array as $item) {
            if (is_array($item)) {
                foreach ($item as $subItem) {
                    if (is_string($subItem)) {
                        $result[] = $subItem;
                    }
                }
            } else {
                $result[] = $item;
            }
        }

        return $result;
    }
}

if (! function_exists('array_flat')) {

    function array_flat($array)
    {
        $tagList = [];

        $array->each(function ($tag) use (&$tagList) {
            $tagList[] = $tag->name;
        });

        return $tagList;
    }
}
