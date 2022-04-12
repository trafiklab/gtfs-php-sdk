<?php


namespace Trafiklab\Gtfs\Util\Internal;


class BinarySearchUtil
{

    /**
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

    /**
     * @param array  $array  Haystack
     * @param string $getter Getter to get value from array item
     * @param string $needle Needle to find
     * @param int    $start  Inclusive start of range
     * @param int    $stop   Exclusive end of range
     *
     * @return float|int
     */
    private static function doBinarySearch(array $array, string $getter, string $needle, int $start, int $stop)
    {
        if ($start + 1 == $stop) {
            if ($array[$start] != $needle) {
                return -1;
            }
            return $start;
        }

        $middle = $start + (($stop - $start) / 2);
        $middleValue = $array[$middle]->$getter();

        if ($middle != $start && $middle != $stop) {
            if ($middleValue > $needle) {
                return self::doBinarySearch($array, $getter, $needle, $start, $middle);
            }

            if ($middleValue < $needle) {
                return self::doBinarySearch($array, $getter, $needle, $middle, $stop);
            }
        }

        if ($middleValue != $needle) {
            return -1;
        }

        // If the value equals, we just loop back to the first match
        while ($middle > 0 && $array[$middle - 1]->$getter() == $needle) {
            $middle--;
        }
        return $middle;
    }
}