<?php


namespace Trafiklab\Gtfs\Model\Files;


use Trafiklab\Gtfs\Model\Entities\StopTime;
use Trafiklab\Gtfs\Model\GtfsArchive;
use Trafiklab\Gtfs\Util\GtfsParserUtil;

class GtfsStopTimesFile
{
    private $dataRows;

    public function __construct(GtfsArchive $parent, string $filePath)
    {
        $this->dataRows = GtfsParserUtil::deserializeCSV($parent, $filePath, StopTime::class);
    }

    /**
     * Get the file data as an array of its rows.
     *
     * @return StopTime[]
     */
    public function getStopTimes(): array
    {
        return $this->dataRows;
    }

    /**
     * @param string $stopId
     *
     * @return StopTime[]
     */
    public function getStopTimesForStop(string $stopId): array
    {
        $stopTimes = $this->getStopTimes();
        $result = [];
        foreach ($stopTimes as $stopTime) {
            if ($stopTime->getStopId() == $stopId) {
                $result[] = $stopTime;
            }
        }
        return $result;
    }

    /**
     * @param string $tripId
     *
     * @return StopTime[]
     */
    public function getStopTimesForTrip(string $tripId): array
    {
        $stopTimes = $this->getStopTimes();
        $result = [];
        foreach ($stopTimes as $stopTime) {
            if ($stopTime->getTripId() == $tripId) {
                $result[] = $stopTime;
            }
        }
        return $result;
    }

}