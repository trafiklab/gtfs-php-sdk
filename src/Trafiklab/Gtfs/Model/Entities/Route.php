<?php


namespace Trafiklab\Gtfs\Model\Entities;


use Trafiklab\Gtfs\Model\GtfsArchive;

class Route
{

    private $route_id;
    private $agency_id;
    private $route_short_name;
    private $route_long_name;
    private $route_desc;
    private $route_type;
    private $route_url;
    private $route_color;
    private $route_text_color;
    private $route_sort_order;
    private $continuous_pickup;
    private $continuous_drop_off;
    private $archive;

    /**
     * Route constructor.
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
     * Defines an agency for the specified route. This field is required when the dataset provides data for routes from
     * more than one agency in agency.txt. Otherwise, it's optional.
     *
     * @return string
     */
    public function getAgencyId(): ?string
    {
        return $this->agency_id;
    }

    /**
     * Contains the short name of a route. This is a short, abstract identifier like 32, 100X, or Green that riders use
     * to identify a route, but which doesn't give any indication of what places the route serves.
     *
     * At least one of route_short_name or route_long_name must be specified, or both if appropriate. If the route has
     * no short name, specify a route_long_name and use an empty string as the value for this field.
     *
     * @return string
     */
    public function getRouteShortName(): ?string
    {
        return $this->route_short_name;
    }

    /**
     * Contains the full name of a route. This name is generally more descriptive than the name from route_short_name
     * and often includes the route's destination or stop.
     *
     * At least one of route_short_name or route_long_name must be specified, or both if appropriate. If the route has
     * no long name, specify a route_short_name and use an empty string as the value for this field.
     *
     * @return string
     */
    public function getRouteLongName(): ?string
    {
        return $this->route_long_name;
    }

    /**
     * Describes a route. Be sure to provide useful, quality information for this field. Don't simply duplicate the
     * name of the route.
     *
     * @return string
     */
    public function getRouteDesc(): ?string
    {
        return $this->route_desc;
    }

    /**
     * Describes the type of transportation used on a route. The following are valid values for this field:
     *
     * 0: Tram, streetcar, or light rail. Used for any light rail or street-level system within a metropolitan area.
     * 1: Subway or metro. Used for any underground rail system within a metropolitan area.
     * 2: Rail. Used for intercity or long-distance travel.
     * 3: Bus. Used for short- and long-distance bus routes.
     * 4: Ferry. Used for short- and long-distance boat service.
     * 5: Cable car. Used for street-level cable cars where the cable runs beneath the car.
     * 6: Gondola or suspended cable car. Typically used for aerial cable cars where the car is suspended from the
     * cable.
     * 7: Funicular. Used for any rail system that moves on steep inclines with a cable traction system.
     *
     * @return int
     */
    public function getRouteType(): int
    {
        return $this->route_type;
    }

    /**
     * Contains the URL of a web page for a particular route. This URL needs to be different from the agency_url value.
     *
     * @return string| null
     */
    public function getRouteUrl(): ?string
    {
        return $this->route_url;
    }

    /**
     *
     *
     * In systems that assigns colors to routes, the route_color field defines a color that corresponds to a route. If
     * no color is specified, the default route color is white, FFFFFF.
     *
     * The color difference between route_color and route_text_color needs to provide sufficient contrast when viewed
     * on a black and white screen. The W3C techniques for accessibility evaluation and repair tools document offers a
     * useful algorithm to evaluate color contrast. There are also helpful online tools to help you choose contrasting
     * colors, such as the snook.ca Color Contrast Check application.
     *
     * @return string| null
     */
    public function getRouteColor(): ?string
    {
        return $this->route_color;
    }

    /**
     * Specifies a legible color for text that's drawn against the background color of route_color. If no color is
     * specified, the default text color is black, 000000.
     *
     * The color difference between route_color and route_text_color needs to provide sufficient contrast when viewed
     * on a black and white screen. The W3C techniques for accessibility evaluation and repair tools document offers a
     * useful algorithm to evaluate color contrast. There are also helpful online tools to help you choose contrasting
     * colors, such as the snook.ca Color Contrast Check application.
     *
     * @return string| null
     */
    public function getRouteTextColor(): ?string
    {
        return $this->route_text_color;
    }

    /**
     * Specifies the order in which to present the routes to customers. Routes with smaller route_sort_order values
     * need to be displayed before routes with larger route_sort_order values.
     *
     * @return int | null
     */
    public function getRouteSortOrder(): ?int
    {
        return $this->route_sort_order;
    }

    /**
     * Indicates whether a rider can board the transit vehicle anywhere along the vehicle’s travel path. The path is described by shapes.txt on every trip of the route. Valid options are:
     *
     * 0 - Continuous stopping pickup.
     * 1 or empty - No continuous stopping pickup.
     * 2 - Must phone an agency to arrange continuous stopping pickup.
     * 3 - Must coordinate with a driver to arrange continuous stopping pickup.
     *
     * The default continuous pickup behavior defined in routes.txt can be overridden in stop_times.txt.
     *
     * @return int | null
     */
    public function getContinuousPickup(): ?int
    {
        return $this->continuous_pickup;
    }

    /**
     * Indicates whether a rider can alight from the transit vehicle at any point along the vehicle’s travel path. The path is described by shapes.txt on every trip of the route. Valid options are:
     *
     * 0- Continuous stopping drop-off.
     * 1 or empty - No continuous stopping drop-off.
     * 2 - Must phone an agency to arrange continuous stopping drop-off.
     * 3 - Must coordinate with a driver to arrange continuous stopping drop-off.
     *
     * The default continuous drop-off behavior defined in routes.txt can be overridden in stop_times.txt.
     *
     * @return int | null
     */
    public function getContinuousDropOff(): ?int
    {
        return $this->continuous_drop_off;
    }
}
