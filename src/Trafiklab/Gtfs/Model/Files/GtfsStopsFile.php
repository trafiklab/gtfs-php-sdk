<?php


namespace Trafiklab\Gtfs\Model\Files;


use Trafiklab\Gtfs\Model\Entities\Stop;
use Trafiklab\Gtfs\Model\GtfsArchive;
use Trafiklab\Gtfs\Util\GtfsParserUtil;

class GtfsStopsFile
{
    private $dataRows;

    public function __construct(GtfsArchive $parent, string $filePath)
    {
        $this->dataRows = GtfsParserUtil::deserializeCSV($parent, $filePath,
            Stop::class, 'stop_id');
    }

    /**
     * Get the file data as an array of its rows.
     *
     * @return Stop[]
     */
    public function getStops(): array
    {
        return $this->dataRows;
    }

    /**
     * @param string $stopId
     *
     * @return null|Stop
     */
    public function getStop(string $stopId): ?Stop
    {
        $stops = $this->getStops();
        if (!key_exists($stopId, $stops)) {
            return null;
        }
        return $stops[$stopId];
    }

}