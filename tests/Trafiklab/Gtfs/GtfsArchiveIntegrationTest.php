<?php

namespace Trafiklab\Gtfs;

use DateTime;
use PHPUnit\Framework\TestCase;
use Trafiklab\Gtfs\Model\GtfsArchive;
use Trafiklab\Gtfs\Util\Internal\BinarySearchUtil;

class GtfsArchiveIntegrationTest extends TestCase
{
    private $gtfsArchive = null;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();
        $this->gtfsArchive = GtfsArchive::createFromPath("./tests/Resources/Gtfs/minified-test-feed.zip");
    }

    function __destruct()
    {
        $this->gtfsArchive->deleteUncompressedFiles();
    }

    public function testGetStops()
    {
        self::assertEquals(9375, count($this->gtfsArchive->getStopsFile()->getStops()));
    }

    public function testGetStop()
    {
        $stop = $this->gtfsArchive->getStopsFile()->getStop('9021008004033000');

        self::assertEquals('Ottenby gård nedre', $stop->getStopName());
        self::assertEquals(56.232823, $stop->getStopLat());
        self::assertEquals(16.417789, $stop->getStopLon());
        self::assertEquals(1, $stop->getLocationType());
    }

    public function testGetRoutes()
    {
        self::assertEquals(2, count($this->gtfsArchive->getRoutesFile()->getRoutes()));
    }

    public function testGetRoute()
    {
        $route = $this->gtfsArchive->getRoutesFile()->getRoute('9011008842200000');

        self::assertEquals('9011008842200000', $route->getRouteId());
        self::assertEquals('88100000000001375', $route->getAgencyId());
        self::assertEquals('8422', $route->getRouteShortName());
        self::assertEquals('Närtrafik 2', $route->getRouteLongName());
        self::assertEquals('0', $route->getRouteType());
    }

    public function testGetTrips()
    {
        self::assertEquals(2, count($this->gtfsArchive->getTripsFile()->getTrips()));
    }

    public function testGetTrip()
    {
        $trip = $this->gtfsArchive->getTripsFile()->getTrip('88100000084251548');

        self::assertEquals('9011008842200000', $trip->getRouteId());
        self::assertEquals('2', $trip->getServiceId());
        self::assertEquals('88100000084251548', $trip->getTripId());
        self::assertEquals('', $trip->getTripHeadsign());
        self::assertEquals('0', $trip->getDirectionId());
        self::assertEquals('2', $trip->getShapeId());
    }

    public function testGetStopTimes()
    {
        self::assertEquals(92, count($this->gtfsArchive->getStopTimesFile()->getStopTimes()));
    }

    public function testGetShapePoints()
    {
        self::assertEquals(2474, count($this->gtfsArchive->getShapesFile()->getShapePoints()));
    }

    public function testGetShape()
    {
        $shape = $this->gtfsArchive->getShapesFile()->getShape(1);

        self::assertEquals(1236, count($shape));
        self::assertEquals(1, $shape[0]->getShapeId());
        self::assertEquals(0, $shape[0]->getShapeDistTraveled());

        self::assertEquals(1, $shape[10]->getShapeId());
        self::assertEquals(292.63, $shape[10]->getShapeDistTraveled());

        self::assertEquals(56.666811, $shape[0]->getShapePtLat());
        self::assertEquals(16.318689, $shape[0]->getShapePtLon());
        for ($i = 0; $i < count($shape); $i++) {
            self::assertEquals(1, $shape[$i]->getShapeId());
            self::assertEquals($i + 1, $shape[$i]->getShapePtSequence());
        }

        $shape = $this->gtfsArchive->getShapesFile()->getShape(2185);
        self::assertEquals(0, count($shape));
    }

    public function testGetShape_shapeIdPresentAtStart()
    {
        $shape = $this->gtfsArchive->getShapesFile()->getShape(1);
        self::assertEquals(1236, count($shape));

        // Additional verification of inner working, this should be in a separate test
        $shapeStartId = BinarySearchUtil::findIndexOfFirstFieldOccurrence(
            $this->gtfsArchive->getShapesFile()->getShapePoints(), 'getShapeId', 1);
        self::assertEquals(0, $shapeStartId);

        $shapeStartId = BinarySearchUtil::findIndexOfFirstFieldOccurrence(
            $this->gtfsArchive->getShapesFile()->getShapePoints(), 'getShapeId', 2);
        self::assertEquals(1236, $shapeStartId);
    }

    public function testGetShape_shapeIdPresentInMiddle()
    {
        $shape = $this->gtfsArchive->getShapesFile()->getShape(4);
        self::assertEquals(1, count($shape));

        // Additional verification of inner working, this should be in a separate test
        $shapeStartId = BinarySearchUtil::findIndexOfFirstFieldOccurrence(
            $this->gtfsArchive->getShapesFile()->getShapePoints(), 'getShapeId', 4);
        self::assertEquals(2472, $shapeStartId);
    }

    public function testGetShape_shapeIdPresentAtEnd()
    {
        // Additional verification of inner working, this should be in a separate test
        $shapeStartId = BinarySearchUtil::findIndexOfFirstFieldOccurrence(
            $this->gtfsArchive->getShapesFile()->getShapePoints(), 'getShapeId', 6);
        self::assertEquals(2473, $shapeStartId);

        $shape = $this->gtfsArchive->getShapesFile()->getShape(6);
        self::assertEquals(1, count($shape));
    }

    public function testGetShape_shapeIdNotPresent()
    {
        $shape = $this->gtfsArchive->getShapesFile()->getShape(3);
        self::assertEquals(0, count($shape));

        $shape = $this->gtfsArchive->getShapesFile()->getShape(5);
        self::assertEquals(0, count($shape));

        // Additional verification of inner working, this should be in a separate test
        $shapeStartId = BinarySearchUtil::findIndexOfFirstFieldOccurrence(
            $this->gtfsArchive->getShapesFile()->getShapePoints(), 'getShapeId', 3);
        self::assertEquals(-1, $shapeStartId);

        $shapeStartId = BinarySearchUtil::findIndexOfFirstFieldOccurrence(
            $this->gtfsArchive->getShapesFile()->getShapePoints(), 'getShapeId', 5);
        self::assertEquals(-1, $shapeStartId);
    }

    public function testGetCalendar()
    {
        $calendar = $this->gtfsArchive->getCalendarFile()->getCalendarEntries();
        self::assertEquals(2, count($calendar));
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
        self::assertEquals(24, count($this->gtfsArchive->getCalendarDatesFile()->getCalendarDates()));
    }

    public function testGetCalendarDatesForService()
    {
        $dates = $this->gtfsArchive->getCalendarDatesFile()->getCalendarDatesForService(2);
        self::assertEquals(5, count($dates));

        self::assertEquals(2, $dates[0]->getServiceId());
        self::assertEquals(DateTime::createFromFormat("Ymd", 20190518), $dates[0]->getDate());
        self::assertEquals(1, $dates[0]->getExceptionType());
    }

    public function testGetTransfers()
    {
        $transfers = $this->gtfsArchive->getTransfersFile()->getTransfers();
        self::assertEquals(9, count($transfers));
        self::assertEquals("9021008001009000", $transfers[0]->getFromStopId());
        self::assertEquals("9021008001009000", $transfers[0]->getToStopId());
        self::assertEquals(2, $transfers[0]->getTransferType());
        self::assertEquals(0, $transfers[0]->getMinTransferTime());
    }

    public function testGetFeedInfo()
    {
        $feedinfo = $this->gtfsArchive->getFeedInfoFile()->getFeedInfo();
        self::assertEquals(1, count($feedinfo));
        self::assertEquals("Samtrafiken i Sverige AB", $feedinfo[0]->getFeedPublisherName());
        self::assertEquals("https://www.samtrafiken.se", $feedinfo[0]->getFeedPublisherUrl());
        self::assertEquals("2019-05-20", $feedinfo[0]->getFeedVersion());
        self::assertEquals("sv", $feedinfo[0]->getFeedLang());
    }

    public function testGetAgency()
    {
        $agencies = $this->gtfsArchive->getAgencyFile()->getAgencies();
        self::assertEquals(2, count($agencies));
        self::assertEquals(88100000000001375, $agencies[0]->getAgencyId());
        self::assertEquals("Kalmar Länstrafik", $agencies[0]->getAgencyName());
        self::assertEquals("https://www.resrobot.se/", $agencies[0]->getAgencyUrl());
        self::assertEquals("Europe/Stockholm", $agencies[0]->getAgencyTimezone());
        self::assertEquals("sv", $agencies[0]->getAgencyLang());
        self::assertNull($agencies[0]->getAgencyPhone(), 'Agency Phone is not null!');
        self::assertNull($agencies[1]->getAgencyEmail(), 'Agency Email is not null!');
        self::assertEquals('000-000-0000', $agencies[1]->getAgencyPhone(), 'Agency Phone is not equal to 000-000-0000');
    }
}
