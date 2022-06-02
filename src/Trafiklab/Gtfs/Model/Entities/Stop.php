<?php


namespace Trafiklab\Gtfs\Model\Entities;


use Trafiklab\Gtfs\Model\GtfsArchive;

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
    private $level_id;
    private $platform_code;

    private $archive;

    /**
     * Stop constructor.
     *
     * @param GtfsArchive $archive The archive in which this data originates, used to link between files.
     * @param array $data An associative array containing the variable values.
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
     * Identifies a stop, station, or station entrance. The term "station entrance" refers to both station entrances
     * and station exits. Stops, stations, and station entrances are collectively referred to as locations. Multiple
     * routes can use the same stop.
     *
     * @return string
     */
    public function getStopId(): string
    {
        return $this->stop_id;
    }

    /**
     * Contains some short text or a number that uniquely identifies the stop for riders. Stop codes are often used in
     * phone-based transit information systems or printed on stop signage to make it easier for riders to get
     * information for a particular stop.
     *
     * The stop_code can be the same as stop_id if the ID is public facing. Leave this field blank for stops without a
     * code presented to riders.
     *
     * @return string | null
     */
    public function getStopCode(): ?string
    {
        return $this->stop_code;
    }

    /**
     * Contains the name of a location. Use a name that people understand in the local and tourist vernacular.
     *
     * When the location is a boarding area, with location_type=4, include the name of the boarding area as displayed
     * by the agency in the stop_name. It can be just one letter or text like "Wheelchair boarding area" or "Head of
     * short trains."
     *
     * This field is required for locations that are stops, stations, or entrances/exits, which have location_type
     * fields of 0, 1, and 2 respectively.
     *
     * This field is optional for locations that are generic nodes or boarding areas, which have location_type fields
     * of 3 and 4 respectively.
     *
     * @return string | null
     */
    public function getStopName(): ?string
    {
        return $this->stop_name;
    }

    /**
     * Describes a location. Provide useful, quality information. Don't simply duplicate the name of the location.
     *
     * @return string | null
     */
    public function getStopDesc(): ?string
    {
        return $this->stop_desc;
    }

    /**
     * Contains the latitude of a stop, station, or station entrance.
     *
     * This field is required for locations that are stops, stations, or entrances/exits, which have location_type
     * fields of 0, 1, and 2 respectively.
     *
     * This field is optional for locations that are generic nodes or boarding areas, which have location_type fields
     * of 3 and 4 respectively.
     *
     * @return float | null
     */
    public function getStopLat(): ?float
    {
        return floatval($this->stop_lat);
    }

    /**
     * Contains the longitude of a stop, station, or station entrance.
     *
     * This field is required for locations that are stops, stations, or entrances/exits, which have location_type
     * fields of 0, 1, and 2 respectively.
     *
     * This field is optional for locations that are generic nodes or boarding areas, which have location_type fields
     * of 3 and 4 respectively.
     *
     * @return float | null
     */
    public function getStopLon(): ?float
    {
        return floatval($this->stop_lon);
    }

    /**
     * Defines the fare zone for a stop. This field is required if you want to provide fare information with
     * fare_rules.txt. If this record represents a station or station entrance, the zone_id is ignored.
     *
     * @return string | null
     */
    public function getZoneId(): ?string
    {
        return $this->zone_id;
    }

    /**
     * Contains the URL of a web page about a particular stop. Make this different from the agency_url and route_url
     * fields.
     *
     * @return string | null
     */
    public function getStopUrl(): ?string
    {
        return $this->stop_url;
    }

    /**
     * For stops that are physically located inside stations, the parent_station field identifies the station
     * associated with the stop. Based on a combination of values for the parent_station and location_type fields, we
     * define three types of stops:
     *
     * A parent stop is an (often large) station or terminal building that can contain child stops.
     * This entry's location type is 1.
     * The parent_station field contains a blank value, because parent stops can't contain other parent stops.
     * A child stop is located inside of a parent stop. It can be an entrance, platform, node, or other pathway, as
     * defined in pathways.txt. This entry's location_type is 0 or (empty). The parent_station field contains the stop
     * ID of the station where this stop is located. The stop referenced in parent_station must have location_type=1. A
     * standalone stop is located outside of a parent stop. This entry's location type is 0 or (empty). The
     * parent_station field contains a blank value, because the parent_station field doesn't apply to this stop.
     *
     * @return string | null
     */
    public function getParentStation(): ?string
    {
        return $this->parent_station;
    }

    /**
     * Contains the timezone of this location. If omitted, it's assumed that the stop is located in the timezone
     * specified by the agency_timezone in agency.txt.
     *
     * When a stop has a parent station, the stop has the timezone specified by the parent station's stop_timezone
     * value. If the parent has no stop_timezone value, the stops that belong to that station are assumed to be in the
     * timezone specified by agency_timezone, even if the stops have their own stop_timezone values.
     *
     * In other words, if a given stop has a parent_station value, any stop_timezone value specified for that stop must
     * be ignored. Even if stop_timezone values are provided in stops.txt, continue to specify the times in
     * stop_times.txt relative to the timezone specified by the agency_timezone field in agency.txt. This ensures that
     * the time values in a trip always increase over the course of a trip, regardless of which timezones the trip
     * crosses.
     *
     * @return string | null
     */
    public function getStopTimezone(): ?string
    {
        return $this->stop_timezone;
    }

    /**
     * Provides the platform identifier for a platform stop, which is a stop that belongs to a station. Just include
     * the platform identifier, such as G or 3. Don't include words like "platform" or "track" or the feed's
     * language-specific equivalent. This allows feed consumers to more easily internationalize and localize the
     * platform identifier into other languages.
     *
     * @return string | null
     */
    public function getPlatformCode(): ?string
    {
        return $this->platform_code;
    }

    /**
     * Defines the type of the location. The location_type field can have the following values:
     *
     * 0 or (empty): Stop (or "Platform"). A location where passengers board or disembark from a transit vehicle. Stops
     * are called a "platform" when they're defined within a parent_station.
     * 1: Station. A physical structure or area that contains one or more platforms.
     * 2: Station entrance or exit. A location where passengers can enter or exit a station from the street. The stop
     * entry must also specify a parent_station value that references the stop_id of the parent station for the
     * entrance. If an entrance/exit belongs to multiple stations, it's linked by pathways to both, and the data
     * provider can either pick one station as parent, or put no parent station at all.
     * 3: Generic node. A location within a station that doesn't match any other location_type. Generic nodes are used
     * to link together the pathways defined in pathways.txt.
     * 4: Boarding area. A specific location on a platform where passengers can board or exit vehicles.
     *
     * @return int | null
     */
    public function getLocationType(): ?int
    {
        if ($this->location_type == null) {
            return null;
        }
        return intval($this->location_type);
    }

    /**
     * Identifies whether wheelchair boardings are possible from the specified stop, station, or station entrance. This
     * field can have the following values:
     *
     * 0 or (empty): Indicates that there's no accessibility information available for this stop.
     * 1: Indicates that at least some vehicles at this stop can be boarded by a rider in a wheelchair.
     * 2: Indicates that wheelchair boarding isn't possible at this stop.
     *
     * When a stop is part of a larger station complex, as indicated by the presence of a parent_station value, the
     * stop's wheelchair_boarding field has the following additional semantics:
     *
     * 0 or (empty): The stop inherits its wheelchair_boarding value from the parent station if it exists.
     * 1: Some accessible path exists from outside the station to the specific stop or platform.
     * 2: There are no accessible paths from outside the station to the specific stop or platform.
     *
     * For station entrances/exits, the wheelchair_boarding field has the following additional semantics:
     *
     * 0 or (empty): The station entrance inherits its wheelchair_boarding value from the parent station if it exists.
     * 1: The station entrance is wheelchair accessible, such as when an elevator is available to reach platforms that
     * aren't at-grade.
     * 2: There are no accessible paths from the entrance to the station platforms.
     *
     * @return int | null
     */
    public function getWheelchairBoarding(): ?int
    {
        return $this->wheelchair_boarding;
    }

    /**
     * Level of the location. The same level can be used by multiple unlinked stations.
     *
     * @return string | null
     */
    public function getLevelId(): ?string
    {
        return $this->level_id;
    }
}
