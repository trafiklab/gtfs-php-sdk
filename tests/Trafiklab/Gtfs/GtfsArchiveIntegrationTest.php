<?php

namespace Trafiklab\Gtfs;

use DateTime;
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

    function __destruct()
    {
        $this->gtfsArchive->deleteUncompressedFiles();
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
        self::assertEquals('88100000000001375', $route->getAgencyId());
        self::assertEquals('SJ', $route->getRouteShortName());
        self::assertEquals('SJ Regional', $route->getRouteLongName());
        self::assertEquals('2', $route->getRouteType());
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

    public function testGetShape()
    {
        $shape = $this->gtfsArchive->getShape(1);

        self::assertEquals(1236, count($shape));
        self::assertEquals(1, $shape[0]->getShapeId());
        self::assertEquals(0, $shape[0]->getShapeDistTraveled());

        self::assertEquals(1, $shape[10]->getShapeId());
        self::assertEquals(292.63, $shape[10]->getShapeDistTraveled());

        self::assertEquals(56.666811, $shape[0]->getShapePtLat());
        self::assertEquals(16.318689, $shape[0]->getShapePtLon());
        for ($i = 0; $i < count($shape); $i++) {
            self::assertEquals($i + 1, $shape[$i]->getShapePtSequence());
        }
    }

    public function testGetCalendar()
    {
        $calendar = $this->gtfsArchive->getCalendar();
        self::assertEquals(290, count($calendar));
        self::assertEquals(1, $calendar[0]->getServiceId());
        self::assertEquals(DateTime::createFromFormat("Ymd", "20190517"), $calendar[0]->getStartDate());
        self::assertEquals(DateTime::createFromFormat("Ymd", "20190614"), $calendar[0]->getEndDate());
        self::assertEquals(0, $calendar[0]->getMonday());
        self::assertEquals(0, $calendar[0]->getTuesday());
        self::assertEquals(0, $calendar[0]->getWednesday());
        self::assertEquals(0, $calendar[0]->getThursday());
        self::assertEquals(0, $calendar[0]->getFriday());
        self::assertEquals(0, $calendar[0]->getSaturday());
        self::assertEquals(0, $calendar[0]->getSunday());
    }

    public function testGetCalendarDates()
    {
        self::assertEquals(14965, count($this->gtfsArchive->getCalendarDates()));
    }

    public function testGetCalendarDatesForService()
    {
        $dates = $this->gtfsArchive->getCalendarDatesForService(5);
        self::assertEquals(17, count($dates));

        self::assertEquals(5, $dates[0]->getServiceId());
        self::assertEquals(DateTime::createFromFormat("Ymd", 20190517), $dates[0]->getDate());
        self::assertEquals(1, $dates[0]->getExceptionType());
    }
}
