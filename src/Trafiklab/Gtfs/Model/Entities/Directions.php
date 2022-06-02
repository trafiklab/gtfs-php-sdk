<?php

namespace Trafiklab\Gtfs\Model\Entities;

use Trafiklab\Gtfs\Model\GtfsArchive;

/**
 * Directions.txt is a GTFS+ Experimental dataset.
 * GTFS+ is an extension to GTFS in use by the San Francisco (MTC) 511.org, Trillium, etc.
 * Many transit agencies in the SF Bay Area publish GTFS+.
 * @link https://www.transitwiki.org/TransitWiki/index.php/GTFS+
 *
 * There are many Agencies that use Trillium to generate GTFS feeds, and directions can be useful when
 * relating back to GTFS-RT datasets.
 * @link https://trilliumtransit.com/gtfs/reference/#directions
 */
class Directions
{
    /**
     * Per the GTFS+ Specification, all fields are required.
     */
    /** @var string $route_id */
    private $route_id;
    /** @var int $direction_id */
    private $direction_id;
    /**
     *
     * @var string $direction
     * Possible Values:
     * North, South, East, West, Northeast, Northwest, Southeast, Southwest
     * Clockwise, Counterclockwise
     * Inbound, Outbound
     * Loop, A Loop, B Loop
     */
    private $direction;
    private $archive;

    /**
     * Directions constructor.
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
     * @return string
     */
    public function getRouteId(): string
    {
        return $this->route_id;
    }

    /**
     * @param string $route_id
     */
    public function setRouteId(string $route_id): void
    {
        $this->route_id = $route_id;
    }

    /**
     * @return int
     */
    public function getDirectionId(): int
    {
        return $this->direction_id;
    }

    /**
     * @param int $direction_id
     */
    public function setDirectionId(int $direction_id): void
    {
        $this->direction_id = $direction_id;
    }

    /**
     * @return string
     */
    public function getDirection(): string
    {
        return $this->direction;
    }

    /**
     * @param string $direction
     */
    public function setDirection(string $direction): void
    {
        $this->direction = $direction;
    }

    /**
     * @return GtfsArchive
     */
    public function getArchive(): GtfsArchive
    {
        return $this->archive;
    }

    /**
     * @param GtfsArchive $archive
     */
    public function setArchive(GtfsArchive $archive): void
    {
        $this->archive = $archive;
    }

    /**
     * Get the Abbreviation of the Cardinal Direction.
     *  - Ex: North = N, West = W, South = S, Southeast = SE.
     * @param bool $adjective - Whether or not to use NorthBound (NB)
     *  - Ex: North = NB, NorthEast = NB, South = SB, SouthWest = SB.
     * @return string
     * * Possible Values:
     *  - North, South, East, West, Northeast, Northwest, Southeast, Southwest
     */
    public function getCardinalDirectionAbrv(bool $adjective = false): string
    {
        switch (strtolower($this->getDirection())) {
            case 'north':
            case 'northeast':
            case 'northwest':
                return $adjective ? 'NB' : 'N';
            case 'south':
            case 'southeast':
            case 'southwest':
                return $adjective ? 'SB' : 'S';
            case 'west':
                return $adjective ? 'WB' : 'W';
            case 'east':
                return $adjective ? 'EB' : 'E';
            default:
                return '';
        }
    }

    /**
     * Return the value of Inbound(1) or Outbound(0) depending on the value of direction_id.
     * Inbound assumes that the vehicle is arriving at said stop or location.
     * Outbound assumes that the vehicle is leaving said stop or location.
     * @return string
     */
    public function getBoundDirection(): string
    {
        return $this->getDirectionId() === 0 ? 'Outbound' : 'Inbound';
    }
}
