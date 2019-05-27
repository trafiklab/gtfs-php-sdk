<?php

namespace Trafiklab\Gtfs\Model\Files;


use Trafiklab\Gtfs\Model\Entities\CalendarDate;
use Trafiklab\Gtfs\Model\GtfsArchive;
use Trafiklab\Gtfs\Util\Internal\GtfsParserUtil;

class GtfsCalendarDatesFile
{
    private $dataRows;

    public function __construct(GtfsArchive $parent, string $filePath)
    {
        $this->dataRows = GtfsParserUtil::deserializeCSV($parent, $filePath,
            CalendarDate::class);
    }

    /**
     * Get the file data as an array of its rows.
     *
     * @return CalendarDate[]
     */
    public function getCalendarDates(): array
    {
        return $this->dataRows;
    }

    /**
     * Get all the calendar dates where service_id matches the given serviceId parameter.
     *
     * @param string $serviceId
     *
     * @return CalendarDate[]
     */
    public function getCalendarDatesForService(string $serviceId): array
    {
        $calendarDates = $this->getCalendarDates();
        $result = [];
        foreach ($calendarDates as $calendarDate) {
            if ($calendarDate->getServiceId() == $serviceId) {
                $result[] = $calendarDate;
            }
        }
        return $result;
    }

}