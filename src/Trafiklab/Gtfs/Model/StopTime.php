<?php


namespace Trafiklab\Gtfs\Model;


class StopTime
{

    private $trip_id;
    private $arrival_time;
    private $departure_time;
    private $stop_id;
    private $stop_sequence;
    private $stop_headsign;
    private $pickup_type;
    private $drop_off_type;
    private $shape_dist_traveled;
    private $timepoint;
    private $archive;

    /**
     * StopTime constructor.
     *
     * @param GtfsArchive $archive The archive in which this data originates, used to link between files.
     * @param array       $data    An associative array containing the variable values.
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
     * @return mixed
     */
    public function getTripId()
    {
        return $this->trip_id;
    }

    /**
     * @return mixed
     */
    public function getArrivalTime()
    {
        return $this->arrival_time;
    }

    /**
     * @return mixed
     */
    public function getDepartureTime()
    {
        return $this->departure_time;
    }

    /**
     * @return mixed
     */
    public function getStopId()
    {
        return $this->stop_id;
    }

    /**
     * @return mixed
     */
    public function getStopSequence()
    {
        return $this->stop_sequence;
    }

    /**
     * @return mixed
     */
    public function getStopHeadsign()
    {
        return $this->stop_headsign;
    }

    /**
     * @return mixed
     */
    public function getPickupType()
    {
        return $this->pickup_type;
    }

    /**
     * @return mixed
     */
    public function getDropOffType()
    {
        return $this->drop_off_type;
    }

    /**
     * @return mixed
     */
    public function getShapeDistTraveled()
    {
        return $this->shape_dist_traveled;
    }

    /**
     * @return mixed
     */
    public function getTimepoint()
    {
        return $this->timepoint;
    }
}