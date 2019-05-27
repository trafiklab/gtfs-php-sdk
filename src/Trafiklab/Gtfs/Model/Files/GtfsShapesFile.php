<?php


namespace Trafiklab\Gtfs\Model\Files;


use Trafiklab\Gtfs\Model\Entities\ShapePoint;
use Trafiklab\Gtfs\Model\GtfsArchive;
use Trafiklab\Gtfs\Util\Internal\ArrayCache;
use Trafiklab\Gtfs\Util\Internal\BinarySearchUtil;
use Trafiklab\Gtfs\Util\Internal\GtfsParserUtil;

class GtfsShapesFile
{

    use ArrayCache;

    private $dataRows;

    public function __construct(GtfsArchive $parent, string $filePath)
    {
        $this->dataRows = GtfsParserUtil::deserializeCSVWithCompositeIndex($parent, $filePath,
            ShapePoint::class, 'shape_id', 'shape_pt_sequence');
        $this->dataRows = array_values($this->dataRows);
    }

    /**
     * Get the file data as an array of its rows.
     *
     * @return ShapePoint[]
     */
    public function getShapePoints(): array
    {
        return $this->dataRows;
    }


    /**
     * @param string $shapeId
     *
     * @return ShapePoint[]
     */
    public function getShape(string $shapeId): array
    {
        if ($this->getCachedResult(__METHOD__ . "::" . $shapeId) != null) {
            return $this->getCachedResult(__METHOD__ . "::" . $shapeId);
        }

        $shapePoints = $this->getShapePoints();
        $i = BinarySearchUtil::findIndexOfFirstFieldOccurrence($shapePoints, 'getShapeId', $shapeId);
        $result = [];
        while ($shapePoints[$i]->getShapeId() == $shapeId) {
            $result[] = $shapePoints[$i];
            $i++;
        }

        $this->setCachedResult(__METHOD__ . "::" . $shapeId, $result);
        return $result;
    }
}