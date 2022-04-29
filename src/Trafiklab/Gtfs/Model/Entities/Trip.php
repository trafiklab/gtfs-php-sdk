<?php


namespace Trafiklab\Gtfs\Model\Entities;


use Trafiklab\Gtfs\Model\GtfsArchive;

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
            $this->$variable = isset($value) && $value !== '' ? $value : null;
        }
        $this->archive = $archive;
    }

    /**
     * Identifies a route.
     *
     * @return string
     */
    public function getRouteId(): string
    {
        return $this->route_id;
    }

    /**
     * Identifies a set of dates when service is available for one or more routes.
     *
     * @return string
     */
    public function getServiceId(): string
    {
        return $this->service_id;
    }

    /**
     * Identifies a trip.
     *
     * @return string
     */
    public function getTripId(): string
    {
        return $this->trip_id;
    }

    /**
     * Contains the text that appears on signage that identifies the trip's destination to riders. Use this field to
     * distinguish between different patterns of service on the same route.
     *
     * If the headsign changes during a trip, you can override the trip_headsign with values from the stop_headsign
     * field in stop_times.txt.
     *
     * @return string | null
     */
    public function getTripHeadsign(): ?string
    {
        return $this->trip_headsign;
    }

    /**
     * Contains the public-facing text that's shown to riders to identify the trip, such as the train numbers for
     * commuter rail trips. If riders don't commonly rely on trip names, leave this field blank.
     *
     * If a trip_short_name is provided, it needs to uniquely identify a trip within a service day. Don't use it for
     * destination names or limited/express designations.
     *
     * @return  string | null
     */
    public function getTripShortName(): ?string
    {
        return $this->trip_short_name;
    }

    /**
     * Indicates the direction of travel for a trip. Use this field to distinguish between bi-directional trips with
     * the same route_id. The following are valid values for this field:
     *
     * 0: Travel in one direction of your choice, such as outbound travel.
     * 1: Travel in the opposite direction, such as inbound travel.
     *
     * This field isn't used in routing, but instead provides a way to separate trips by direction when you publish
     * time tables. You can specify names for each direction with the trip_headsign field.
     *
     * @return  int | null
     */
    public function getDirectionId(): ?int
    {
        return $this->direction_id;
    }

    /**
     * Identifies the block to which the trip belongs. A block consists of a single trip or many sequential trips made
     * with the same vehicle. The trips are grouped into a block by the use of a shared service day and block_id. A
     * block_id can include trips with different service days, which then makes distinct blocks. For more details, see
     * Blocks and service days example.
     *
     * @return  string | null
     */
    public function getBlockId(): ?string
    {
        return $this->block_id;
    }

    /**
     * Defines a geospatial shape that describes the vehicle travel for a trip.
     *
     * @return  string | null
     */
    public function getShapeId(): ?string
    {
        return $this->shape_id;
    }

    /**
     * Identifies whether wheelchair boardings are possible for the specified trip. This field can have the following
     * values:
     *
     * 0 or (empty): There's no accessibility information available for this trip.
     * 1: The vehicle used on this particular trip can accommodate at least one rider in a wheelchair.
     * 2: No riders in wheelchairs can be accommodated on this trip.
     *
     * @return  int | null
     */
    public function getWheelchairAccessible(): ?int
    {
        return $this->wheelchair_accessible;
    }

    /**
     * Identifies whether bicycles are allowed on the specified trip. This field can have the following values:
     *
     * 0 or (empty): There's no bike information available for the trip.
     * 1: The vehicle used on this particular trip can accommodate at least one bicycle.
     * 2: No bicycles are allowed on this trip.
     *
     * @return int | null
     */
    public function getBikesAllowed(): ?int
    {
        return $this->bikes_allowed;
    }
}
