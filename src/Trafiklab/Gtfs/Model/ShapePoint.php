<?php


namespace Trafiklab\Gtfs\Model;


class ShapePoint
{

    private $shape_id;
    private $shape_pt_lat;
    private $shape_pt_lon;
    private $shape_pt_sequence;
    private $shape_dist_traveled;
    private $archive;

    /**
     * ShapePoint constructor.
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
    public function getShapeId() : string
    {
        return $this->shape_id;
    }

    /**
     * @return float
     */
    public function getShapePtLat() : float
    {
        return $this->shape_pt_lat;
    }

    /**
     * @return float
     */
    public function getShapePtLon() : float
    {
        return $this->shape_pt_lon;
    }

    /**
     * @return int
     */
    public function getShapePtSequence() : int
    {
        return $this->shape_pt_sequence;
    }

    /**
     * @return float | null
     */
    public function getShapeDistTraveled(): ?float
    {
        return $this->shape_dist_traveled;
    }
}