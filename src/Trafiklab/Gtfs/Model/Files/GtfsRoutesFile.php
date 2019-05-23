<?php

namespace Trafiklab\Gtfs\Model\Files;


use Trafiklab\Gtfs\Model\Entities\Route;
use Trafiklab\Gtfs\Model\GtfsArchive;
use Trafiklab\Gtfs\Util\GtfsParserUtil;

class GtfsRoutesFile
{
    private $dataRows;

    public function __construct(GtfsArchive $parent, string $filePath)
    {
        $this->dataRows = GtfsParserUtil::deserializeCSV($parent, $filePath,
            Route::class, 'route_id');
    }

    /**
     * Get the file data as an array of its rows.
     *
     * @return Route[]
     */
    public function getRoutes(): array
    {
        return $this->dataRows;
    }

    /**
     * @param string $routeId
     *
     * @return Route | null
     */
    public function getRoute(string $routeId): ?Route
    {
        $routes = $this->getRoutes();
        if (!key_exists($routeId, $routes)) {
            return null;
        }
        return $routes[$routeId];
    }

}