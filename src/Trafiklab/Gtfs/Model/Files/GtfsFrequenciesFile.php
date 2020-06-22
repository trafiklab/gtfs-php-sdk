<?php

namespace Trafiklab\Gtfs\Model\Files;


use Trafiklab\Gtfs\Model\Entities\FeedInfo;
use Trafiklab\Gtfs\Model\GtfsArchive;
use Trafiklab\Gtfs\Util\Internal\GtfsParserUtil;

class GtfsFrequenciesFile
{
    private $dataRows;

    public function __construct(GtfsArchive $parent, string $filePath)
    {
        $this->dataRows = GtfsParserUtil::deserializeCSV(
            $parent,
            $filePath,
            Frequency::class
        );
    }

    /**
     * Get the file data as an array of its rows.
     *
     * @return Frequency[]
     */
    public function getFrequencies(): array
    {
        return $this->dataRows;
    }
}
