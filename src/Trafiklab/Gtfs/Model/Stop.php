<?php


namespace Trafiklab\Gtfs\Model;


class Stop
{
    private $stop_id;
    private $stop_code;
    private $stop_name;
    private $stop_desc;
    private $stop_lat;
    private $stop_lon;
    private $zone_id;
    private $stop_url;
    private $location_type;
    private $parent_station;
    private $stop_timezone;
    private $wheelchair_boarding;
    private $platform_code;

    private $archive;

    /**
     * Stop constructor.
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
     * @return string
     */
    public function getStopId(): string
    {
        return $this->stop_id;
    }

    /**
     * @return string | null
     */
    public function getStopCode(): ?string
    {
        return $this->stop_code;
    }

    /**
     * @return string | null
     */
    public function getStopName(): ?string
    {
        return $this->stop_name;
    }

    /**
     * @return string | null
     */
    public function getStopDesc(): ?string
    {
        return $this->stop_desc;
    }

    /**
     * @return float | null
     */
    public function getStopLat(): ?float
    {
        return $this->stop_lat;
    }

    /**
     * @return float | null
     */
    public function getStopLon(): ?float
    {
        return $this->stop_lon;
    }

    /**
     * @return string | null
     */
    public function getZoneId(): ?string
    {
        return $this->zone_id;
    }

    /**
     * @return string | null
     */
    public function getStopUrl(): ?string
    {
        return $this->stop_url;
    }

    /**
     * @return string | null
     */
    public function getParentStation(): ?string
    {
        return $this->parent_station;
    }

    /**
     * @return string | null
     */
    public function getStopTimezone(): ?string
    {
        return $this->stop_timezone;
    }

    /**
     * @return string | null
     */
    public function getPlatformCode(): ?string
    {
        return $this->platform_code;
    }

    /**
     * @return int | null
     */
    public function getLocationType(): int
    {
        return $this->location_type;
    }

    /**
     * @return int | null
     */
    public function getWheelchairBoarding(): int
    {
        return $this->wheelchair_boarding;
    }
}