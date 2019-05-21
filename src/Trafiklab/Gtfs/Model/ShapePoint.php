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
     * Identifies a shape.
     *
     * @return string
     */
    public function getShapeId(): string
    {
        return $this->shape_id;
    }

    /**
     * Associates a shape point's latitude with a shape ID. Each row in shapes.txt represents a shape point used to
     * define the shape.
     *
     * @return float
     */
    public function getShapePtLat(): float
    {
        return $this->shape_pt_lat;
    }

    /**
     * Associates a shape point's longitude with a shape ID. Each row in shapes.txt represents a shape point used to
     * define the shape.
     *
     * @return float
     */
    public function getShapePtLon(): float
    {
        return $this->shape_pt_lon;
    }

    /**
     * Associates the latitude and longitude of a shape point with its sequence order along the shape. The values for
     * shape_pt_sequence must increase throughout the trip but don't need to be consecutive.
     *
     * @return int
     */
    public function getShapePtSequence(): int
    {
        return $this->shape_pt_sequence;
    }

    /**
     * Provides the actual distance traveled along the shape from the first shape point to the point specified in this
     * record. This information allows the trip planner to determine how much of the shape to draw when they show part
     * of a trip on the map. The values used for shape_dist_traveled must increase along with shape_pt_sequence: they
     * can't be used to show reverse travel along a route.
     *
     * The units used for shape_dist_traveled in the shapes.txt file must match the units that are used for this field
     * in the stop_times.txt file.
     *
     * @return float | null
     */
    public function getShapeDistTraveled(): ?float
    {
        return $this->shape_dist_traveled;
    }
}