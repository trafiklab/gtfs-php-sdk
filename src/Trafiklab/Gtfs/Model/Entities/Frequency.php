<?php


namespace Trafiklab\Gtfs\Model\Entities;

use DateTime;
use Trafiklab\Gtfs\Model\GtfsArchive;

class Frequency
{
    private $trip_id;
    private $start_time;
    private $end_time;
    private $headway_secs;
    private $exact_times;

    private $archive;

    /**
     * Frequency constructor.
     *
     * @param GtfsArchive $archive The archive in which this data originates, used to link between files.
     * @param array $data An associative array containing the variable values.
     *
     * @internal Not to be used outside of the Trafiklab\Gtfs\Model package.
     */
    function __construct(GtfsArchive $archive, array $data)
    {
        foreach ($data as $variable => $value) {
            $this->$variable = $value;
        }
        $this->archive = $archive;
    }

    /**
     * Identifies a trip to which the specified headway of service applies.
     *
     * @return string
     */
    public function getTripId(): string
    {
        return $this->trip_id;
    }

    /**
     * Time at which the first vehicle departs from the first stop of the trip with the specified headway.
     *
     * @return DateTime
     */
    public function getStartTime(): DateTime
    {
        return DateTime::createFromFormat("H:i:s", $this->start_time);
    }

    /**
     * Time at which service changes to a different headway (or ceases) at the first stop in the trip.
     *
     * @return DateTime
     */
    public function getEndTime(): DateTime
    {
        return DateTime::createFromFormat("H:i:s", $this->end_time);
    }

    /**
     * Time, in seconds, between departures from the same stop (headway) for the trip, during the time 
     * interval specified by start_time and end_time. Multiple headways for the same trip are allowed, 
     * but may not overlap. New headways may start at the exact time the previous headway ends.
     *
     * @return int
     */
    public function getHeadwaySecs(): int
    {
        return $this->headway_secs;
    }

    /**
     * Indicates the type of service for a trip. See the file description for more information. Valid options are:
     * 
     * 0 or empty - Frequency-based trips.
     * 1 - Schedule-based trips with the exact same headway throughout the day. In this case the end_time value 
     *     must be greater than the last desired trip start_time but less than the last desired trip 
     *     start_time + headway_secs.
     *
     * @return int
     */
    public function getExactTimes(): int
    {
        return $this->exact_times;
    }
}
