<?php

namespace Trafiklab\Gtfs\Model\Files;


use Trafiklab\Gtfs\Model\Entities\CalendarEntry;
use Trafiklab\Gtfs\Model\GtfsArchive;
use Trafiklab\Gtfs\Util\Internal\GtfsParserUtil;

class GtfsCalendarFile
{
    private $dataRows;

    public function __construct(GtfsArchive $parent, string $filePath)
    {
        $this->dataRows = GtfsParserUtil::deserializeCSV($parent, $filePath,
            CalendarEntry::class);
    }

    /**
     * Get the file data as an array of its rows.
     *
     * @return CalendarEntry[]
     */
    public function getCalendarEntries(): array
    {
        return $this->dataRows;
    }
}