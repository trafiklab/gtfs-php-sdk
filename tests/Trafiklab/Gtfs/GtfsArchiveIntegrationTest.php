<?php

namespace Trafiklab\Gtfs;

use PHPUnit_Framework_TestCase;
use Trafiklab\Gtfs\Model\GtfsArchive;

class GtfsArchiveIntegrationTest extends PHPUnit_Framework_TestCase
{
    private $gtfsArchive = null;

    public function __construct()
    {
        parent::__construct();
        $this->gtfsArchive = GtfsArchive::createFromPath("./tests/Resources/Gtfs/klt.zip");
    }

    public function testGetStops()
    {
        self::assertEquals(9375, count($this->gtfsArchive->getStops()));
    }

    public function testGetStop()
    {
        $stop = $this->gtfsArchive->getStop('9021008004033000');

        self::assertEquals('Ottenby gård nedre', $stop->getStopName());
        self::assertEquals(56.232823, $stop->getStopLat());
        self::assertEquals(16.417789, $stop->getStopLon());
        self::assertEquals(1, $stop->getLocationType());
    }

    public function testGetRoutes()
    {
        self::assertEquals(337, count($this->gtfsArchive->getRoutes()));
    }

    public function testGetRoute()
    {
        $route = $this->gtfsArchive->getRoute('9011008095600000');

        self::assertEquals('9011008095600000', $route->getRouteId());
        self::assertEquals('88100000000001375',$route->getAgencyId());
        self::assertEquals('SJ',$route->getRouteShortName());
        self::assertEquals('SJ Regional',$route->getRouteLongName());
        self::assertEquals('2',$route->getRouteType());
    }

    public function testGetTrips()
    {
        self::assertEquals(11967, count($this->gtfsArchive->getTrips()));
    }

    public function testGetTrip()
    {
        $trip = $this->gtfsArchive->getTrip('88100000070093268');

        self::assertEquals('9011008003900000', $trip->getRouteId());
        self::assertEquals('13', $trip->getServiceId());
        self::assertEquals('88100000070093268', $trip->getTripId());
        self::assertEquals('', $trip->getTripHeadsign());
        self::assertEquals('0', $trip->getDirectionId());
        self::assertEquals('10', $trip->getShapeId());
    }

    public function testGetStopTimes()
    {
        self::assertEquals(248296, count($this->gtfsArchive->getStopTimes()));
    }

    public function testGetShapePoints()
    {
        self::assertEquals(2172367, count($this->gtfsArchive->getShapePoints()));
    }
}
