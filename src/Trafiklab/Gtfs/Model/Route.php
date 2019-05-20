<?php


namespace Trafiklab\Gtfs\Model;


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
    public function getAgencyId()
    {
        return $this->agency_id;
    }

    /**
     * @return mixed
     */
    public function getRouteShortName()
    {
        return $this->route_short_name;
    }

    /**
     * @return mixed
     */
    public function getRouteLongName()
    {
        return $this->route_long_name;
    }

    /**
     * @return mixed
     */
    public function getRouteDesc()
    {
        return $this->route_desc;
    }

    /**
     * @return mixed
     */
    public function getRouteType()
    {
        return $this->route_type;
    }

    /**
     * @return mixed
     */
    public function getRouteUrl()
    {
        return $this->route_url;
    }

    /**
     * @return mixed
     */
    public function getRouteColor()
    {
        return $this->route_color;
    }

    /**
     * @return mixed
     */
    public function getRouteTextColor()
    {
        return $this->route_text_color;
    }

    /**
     * @return mixed
     */
    public function getRouteSortOrder()
    {
        return $this->route_sort_order;
    }
}