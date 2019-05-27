<?php

namespace Trafiklab\Gtfs\Model\Files;


use Trafiklab\Gtfs\Model\Entities\FeedInfo;
use Trafiklab\Gtfs\Model\GtfsArchive;
use Trafiklab\Gtfs\Util\Internal\GtfsParserUtil;

class GtfsFeedInfoFile
{
    private $dataRows;

    public function __construct(GtfsArchive $parent, string $filePath)
    {
        $this->dataRows = GtfsParserUtil::deserializeCSV($parent, $filePath,
            FeedInfo::class);
    }

    /**
     * Get the file data as an array of its rows.
     *
     * @return FeedInfo[]
     */
    public function getFeedInfo(): array
    {
        return $this->dataRows;
    }
}