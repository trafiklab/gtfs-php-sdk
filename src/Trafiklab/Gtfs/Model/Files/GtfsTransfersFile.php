<?php

namespace Trafiklab\Gtfs\Model\Files;


use Trafiklab\Gtfs\Model\Entities\Transfer;
use Trafiklab\Gtfs\Model\GtfsArchive;
use Trafiklab\Gtfs\Util\Internal\GtfsParserUtil;

class GtfsTransfersFile
{
    private $dataRows;

    public function __construct(GtfsArchive $parent, string $filePath)
    {
        $this->dataRows = GtfsParserUtil::deserializeCSV($parent, $filePath,
            Transfer::class);
    }

    /**
     * Get the file data as an array of its rows.
     *
     * @return Transfer[]
     */
    public function getTransfers(): array
    {
        return $this->dataRows;
    }
}