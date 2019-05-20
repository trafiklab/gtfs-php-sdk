<?php


namespace Trafiklab\Gtfs\Model;


class Trip
{

    private $route_id;
    private $service_id;
    private $trip_id;
    private $trip_headsign;
    private $trip_short_name;
    private $direction_id;
    private $block_id;
    private $shape_id;
    private $wheelchair_accessible;
    private $bikes_allowed;
    private $archive;

    /**
     * Trip constructor.
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
    public function getRouteId()
    {
        return $this->route_id;
    }

    /**
     * @return mixed
     */
    public function getServiceId()
    {
        return $this->service_id;
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
    public function getTripHeadsign()
    {
        return $this->trip_headsign;
    }

    /**
     * @return mixed
     */
    public function getTripShortName()
    {
        return $this->trip_short_name;
    }

    /**
     * @return mixed
     */
    public function getDirectionId()
    {
        return $this->direction_id;
    }

    /**
     * @return mixed
     */
    public function getBlockId()
    {
        return $this->block_id;
    }

    /**
     * @return mixed
     */
    public function getShapeId()
    {
        return $this->shape_id;
    }

    /**
     * @return mixed
     */
    public function getWheelchairAccessible()
    {
        return $this->wheelchair_accessible;
    }

    /**
     * @return mixed
     */
    public function getBikesAllowed()
    {
        return $this->bikes_allowed;
    }
}