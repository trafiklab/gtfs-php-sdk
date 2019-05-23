<?php

namespace Trafiklab\Gtfs\Model\Files;


use Trafiklab\Gtfs\Model\Entities\Trip;
use Trafiklab\Gtfs\Model\GtfsArchive;
use Trafiklab\Gtfs\Util\GtfsParserUtil;

class GtfsTripsFile
{
    private $dataRows;

    public function __construct(GtfsArchive $parent, string $filePath)
    {
        $this->dataRows = GtfsParserUtil::deserializeCSV($parent, $filePath,
            Trip::class, 'trip_id');
    }

    /**
     * Get the file data as an array of its rows.
     *
     * @return Trip[]
     */
    public function getTrips(): array
    {
        return $this->dataRows;
    }

    /**
     * @param string $tripId
     *
     * @return null|Trip
     */
    public function getTrip(string $tripId): ?Trip
    {
        $trips = $this->getTrips();
        if (!key_exists($tripId, $trips)) {
            return null;
        }
        return $trips[$tripId];
    }

}