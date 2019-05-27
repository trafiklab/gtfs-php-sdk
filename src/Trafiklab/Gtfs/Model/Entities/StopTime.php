<?php


namespace Trafiklab\Gtfs\Model\Entities;


use Trafiklab\Gtfs\Model\GtfsArchive;

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
     * Identifies a trip.
     *
     * @return string
     */
    public function getTripId(): string
    {
        return $this->trip_id;
    }

    /**
     * Specifies the arrival time at a specific stop for a specific trip on a route. An arrival time must be specified
     * for the first and the last stop in a trip.
     *
     * If you don't have separate times for arrival and departure at a stop, enter the same value for arrival_time and
     * departure_time.
     *
     * For information on how to enter arrival times for stops where the vehicle strictly adheres to a schedule, see
     * Timepoints.
     *
     * @return string|null
     */
    public function getArrivalTime(): ?string
    {
        return $this->arrival_time;
    }

    /**
     * Specifies the departure time from a specific stop for a specific trip on a route. A departure time must be
     * specified for the first and the last stop in a trip, even if the vehicle does not allow boarding at the last
     * stop.
     *
     * If you don't have separate times for arrival and departure at a stop, enter the same value for arrival_time and
     * departure_time.
     *
     * For information on how to enter departure times for stops where the vehicle strictly adheres to a schedule, see
     * Timepoints.
     *
     * @return string|null
     */
    public function getDepartureTime(): ?string
    {
        return $this->departure_time;
    }

    /**
     * Identifies the serviced stop. Multiple routes can use the same stop.
     *
     * If location_type is used in stops.txt, all stops referenced in stop_times.txt must have location_type=0. Where
     * possible, stop_id values should remain consistent between feed updates. In other words, stop A with stop_id=1
     * should have stop_id=1 in all subsequent data updates. If a stop isn't a timepoint, enter blank values for
     * arrival_time and departure_time. For more details, see Timepoints.
     *
     * @return string
     */
    public function getStopId(): string
    {
        return $this->stop_id;
    }

    /**
     * Identifies the order of the stops for a particular trip. The values for stop_sequence must increase throughout
     * the trip but do not need to be consecutive.
     *
     * For example, the first stop on the trip could have a stop_sequence of 1, the second stop on the trip could have
     * a stop_sequence of 23, the third stop could have a stop_sequence of 40, and so on.
     *
     * @return int|null
     */
    public function getStopSequence(): ?int
    {
        return $this->stop_sequence;
    }

    /**
     * Contains the text, as shown on signage, that identifies the trip's destination to riders. Use this field to
     * override the default trip_headsign when the headsign changes between stops. If this headsign is associated with
     * an entire trip, use trip_headsign instead.
     *
     * @return string|null
     */
    public function getStopHeadsign(): ?string
    {
        return $this->stop_headsign;
    }

    /**
     * Indicates whether riders are picked up at a stop as part of the normal schedule or whether a pickup at the stop
     * isn't available. This field also allows the transit agency to indicate that riders must call the agency or
     * notify the driver to arrange a pickup at a particular stop. The following are valid values for this field:
     *
     * 0 or (empty): Regularly scheduled pickup
     * 1: No pickup available
     * 2: Must phone agency to arrange pickup
     * 3: Must coordinate with driver to arrange pickup
     *
     * @return int|null
     */
    public function getPickupType(): ?int
    {
        return $this->pickup_type;
    }

    /**
     * Indicates whether riders are dropped off at a stop as part of the normal schedule or whether a dropoff at the
     * stop is unavailable. This field also allows the transit agency to indicate that riders must call the agency or
     * notify the driver to arrange a dropoff at a particular stop. The following are valid values for this field:
     *
     * 0 or (empty): Regularly scheduled drop off
     * 1: No dropoff available
     * 2: Must phone agency to arrange dropoff
     * 3: Must coordinate with driver to arrange dropoff
     *
     * @return int|null
     */
    public function getDropOffType(): ?int
    {
        return $this->drop_off_type;
    }

    /**
     * When used in the stop_times.txt file, the shape_dist_traveled field positions a stop as a distance from the
     * first shape point. The shape_dist_traveled field represents a real distance traveled along the route in units
     * such as feet or kilometers.
     *
     * For example, if a bus travels a distance of 5.25 kilometers from the start of the shape to the stop, the
     * shape_dist_traveled for the stop ID would be entered as 5.25. This information allows the trip planner to
     * determine how much of the shape to draw when they show part of a trip on the map.
     *
     * The values used for shape_dist_traveled must increase along with stop_sequence: they can't be used to show
     * reverse travel along a route. The units used for shape_dist_traveled in the stop_times.txt file must match the
     * units that are used for this field in the shapes.txt file.
     *
     * @return float|null
     */
    public function getShapeDistTraveled(): ?float
    {
        return $this->shape_dist_traveled;
    }

    /**
     * Indicates if the specified arrival and departure times for a stop are strictly adhered to by the transit
     * vehicle, or if they're instead approximate or interpolated times. This field allows a GTFS producer to provide
     * interpolated stop times that potentially incorporate local knowledge, but still indicate if the times are
     * approximate.
     *
     * For stop-time entries with specified arrival and departure times, the following are valid values for this field:
     *
     * 0: Times are considered approximate.
     * 1 or (empty): Times are considered exact.
     *
     * For stop-time entries without specified arrival and departure times, feed consumers must interpolate arrival and
     * departure times. Feed producers can optionally indicate that such an entry is not a timepoint (with
     * timepoint=0), but it's an error to mark an entry as a timepoint (with timepoint=1) without specifying arrival
     * and departure times.
     *
     * @return int|null
     */
    public function getTimepoint(): ?int
    {
        return $this->timepoint;
    }
}