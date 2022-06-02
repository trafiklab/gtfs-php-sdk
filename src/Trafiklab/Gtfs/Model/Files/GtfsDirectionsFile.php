<?php

namespace Trafiklab\Gtfs\Model\Files;

use Trafiklab\Gtfs\Model\Entities\Directions;
use Trafiklab\Gtfs\Model\GtfsArchive;
use Trafiklab\Gtfs\Util\Internal\GtfsParserUtil;

class GtfsDirectionsFile
{
    private $dataRows;

    public function __construct(GtfsArchive $parent, string $filePath)
    {
        $this->dataRows = GtfsParserUtil::deserializeCSV(
            $parent,
            $filePath,
            Directions::class
        );
    }

    /**
     * Get the file data as an array of its rows.
     *
     * @return Directions[]
     */
    public function getDirections(): array
    {
        return $this->dataRows;
    }
}
