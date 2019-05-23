<?php


namespace Trafiklab\Gtfs\Util;


class BinarySearchUtil
{

    /**
     *
     * /**
     * Find the index of the first ShapePoint for a given field value, by using binary search.
     *
     * @param $array     array The data to search through
     * @param $getter    string The name of the getter for the field to search
     * @param $value     string The value to search
     *
     * @return int
     */
    public static function findIndexOfFirstFieldOccurrence(array $array, string $getter, string $value): int
    {
        return self::doBinarySearch($array, $getter, $value, 0, count($array));
    }

    private static function doBinarySearch(array $array, string $getter, string $value, int $start, int $stop)
    {
        if ($start == $stop) {
            return $start;
        }
        $middle = $start + (($stop - $start) / 2);

        if ($array[$middle]->$getter() > $value) {
            return self::doBinarySearch($array, $getter, $value, $start, $middle);
        }

        if ($array[$middle]->$getter() < $value) {
            return self::doBinarySearch($array, $getter, $value, $middle, $stop);
        }

        // If the value equals, we just loop back to the first match
        while ($middle > 0 && $array[$middle - 1]->$getter() == $value) {
            $middle--;
        }
        return $middle;
    }
}