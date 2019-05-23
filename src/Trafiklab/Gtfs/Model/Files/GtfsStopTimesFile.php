<?php


namespace Trafiklab\Gtfs\Model\Files;


use Trafiklab\Gtfs\Model\Entities\StopTime;
use Trafiklab\Gtfs\Model\GtfsArchive;
use Trafiklab\Gtfs\Util\GtfsParserUtil;

class GtfsStopTimesFile
{
    private $dataRows;

    private $stopTimesByStop = [];

    public function __construct(GtfsArchive $parent, string $filePath)
    {
        $this->dataRows = GtfsParserUtil::deserializeCSV($parent, $filePath, StopTime::class);

        // Create an array which gives all stop_times for each stop.
        // This way we only need to loop once over all stop times, but memory usage will be a bit higher.
        foreach ($this->dataRows as $stopTime) {
            if (!key_exists($stopTime->getStopId(), $this->stopTimesByStop)) {
                $this->stopTimesByStop[$stopTime->getStopId()] = [];
            }
            $this->stopTimesByStop[$stopTime->getStopId()][] = $stopTime;
        }
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
        // Before we run a computationally intensive loop, check the table with included stops first.
        if (!key_exists($stopId, $this->stopTimesByStop)) {
            return [];
        }

        return $this->stopTimesByStop[$stopId];
    }

    /**
     * @param string $tripId
     *
     * @return StopTime[]
     */
    public function getStopTimesForTrip(string $tripId): array
    {
        $result = [];
        $stopTimes = $this->getStopTimes();
        foreach ($stopTimes as $stopTime) {
            if ($stopTime->getTripId() == $tripId) {
                $result[] = $stopTime;
            }
        }
        return $result;
    }

}