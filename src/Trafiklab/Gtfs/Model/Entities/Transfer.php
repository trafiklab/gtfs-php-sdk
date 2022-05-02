<?php


namespace Trafiklab\Gtfs\Model\Entities;


use Trafiklab\Gtfs\Model\GtfsArchive;

class Transfer
{

    private $from_stop_id;
    private $to_stop_id;
    private $transfer_type;
    private $min_transfer_time;
    private $archive;

    /**
     * Transfer constructor.
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
     * Identifies a stop or station where a connection between routes begins. If this field refers to a station, the
     * transfer rule applies to all of its child stops.
     *
     * @return string
     */
    public function getFromStopId(): string
    {
        return $this->from_stop_id;
    }

    /**
     * Identifies a stop or station where a connection between routes ends. If this field refers to a station, the
     * transfer rule applies to all of its child stops.
     *
     * @return string
     */
    public function getToStopId(): string
    {
        return $this->to_stop_id;
    }

    /**
     * Indicates the type of connection for the specified pair (from_stop_id, to_stop_id). The following are valid
     * values for this field:
     *
     * 0 or (empty): This is a recommended transfer point between routes.
     * 1: This is a timed transfer point between two routes. The departing vehicle is expected to wait for the arriving
     * one, with sufficient time for a rider to transfer between routes.
     * 2: This transfer requires a minimum amount of time between arrival and departure to ensure a connection. The
     * time required to transfer is specified by min_transfer_time.
     * 3: Transfers aren't possible between routes at this location.
     *
     * @return int
     */
    public function getTransferType(): int
    {
        return $this->transfer_type;
    }

    /**
     * Defines the amount of time, in seconds, that must be available in an itinerary to permit a transfer between
     * routes at the specified stops. The min_transfer_time must be sufficient to permit a typical rider to move
     * between the two stops, as well as some buffer time to allow for schedule variance on each route.
     *
     * @return int|null
     */
    public function getMinTransferTime(): ?int
    {
        return $this->min_transfer_time;
    }
}
