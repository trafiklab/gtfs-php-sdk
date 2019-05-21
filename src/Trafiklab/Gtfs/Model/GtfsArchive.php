<?php


namespace Trafiklab\Gtfs\Model;


use Exception;
use RuntimeException;
use ZipArchive;

class GtfsArchive
{


    private const AGENCY_TXT = "agency.txt";
    private const STOPS_TXT = "stops.txt";
    private const ROUTES_TXT = "routes.txt";
    private const TRIPS_TXT = "trips.txt";
    private const STOP_TIMES_TXT = "stop_times.txt";
    private const CALENDAR_TXT = "calendar.txt";
    private const CALENDAR_DATES_TXT = "calendar_dates.txt";
    private const FARE_ATTRIBUTES_TXT = "fare_attributes.txt"; // Unsupported at this moment
    private const FARE_RULES_TXT = "fare_rules.txt"; // Unsupported at this moment
    private const SHAPES_TXT = "shapes.txt";
    private const FREQUENCIES_TXT = "frequencies.txt"; // Unsupported at this moment
    private const TRANSFERS_TXT = "transfers.txt";
    private const PATHWAYS_TXT = "pathways.txt"; // Unsupported at this moment
    private const LEVELS_TXT = "levels.txt"; // Unsupported at this moment
    private const FEED_INFO_TXT = "feed_info.txt";

    private const TEMP_ROOT = "/tmp/gtfs/";

    private $fileRoot;
    private $_cache = [];
    private $deleteUncompressedFilesWhenDone;

    private function __construct(string $fileRoot)
    {
        $this->fileRoot = $fileRoot;
    }

    /**
     * Download a GTFS zipfile.
     *
     * @param string $url The URL that points to the archive.
     *
     * @return GtfsArchive The downloaded archive.
     * @throws Exception
     */
    public static function createFromUrl(string $url): GtfsArchive
    {
        $downloadedArchive = self::downloadFile($url);
        $fileRoot = self::extractFiles($downloadedArchive, true);
        return new GtfsArchive($fileRoot);
    }

    /**
     * Open a local GTFS zipfile.
     *
     * @param string $path The path that points to the archive.
     *
     * @return GtfsArchive The downloaded archive.
     * @throws Exception
     */
    public static function createFromPath(string $path): GtfsArchive
    {
        $fileRoot = self::extractFiles($path);
        return new GtfsArchive($fileRoot);
    }

    private static function extractFiles(string $archiveFilePath, bool $deleteArchive = false)
    {
        // Load the zip file.
        $zip = new ZipArchive();
        if ($zip->open($archiveFilePath) != 'true') {
            throw new Exception('Could not open the GTFS archive');
        }
        // Extract the zip file and remove it.
        $extractionPath = substr($archiveFilePath, 0, strlen($archiveFilePath) - 4) . '/';
        $zip->extractTo($extractionPath);
        $zip->close();

        if ($deleteArchive) {
            unlink($archiveFilePath);
        }

        return $extractionPath;
    }

    /**
     * Download and extract the latest GTFS data set
     *
     * @param string $url
     *
     * @return string
     */
    private static function downloadFile(string $url): string
    {
        $temp_file = self::TEMP_ROOT . md5($url) . ".zip";

        if (!file_exists($temp_file)) {
            // Download zip file with GTFS data.
            file_put_contents($temp_file, file_get_contents($url));
        }

        return $temp_file;
    }

    /**
     * @return Agency[]
     */
    public function getAgencies(): array
    {
        return $this->loadCsvArrayToModelArrayThroughCache(__METHOD__, self::AGENCY_TXT, Agency::class, 'agency_id');
    }

    /**
     * @return Stop[]
     */
    public function getStops(): array
    {
        return $this->loadCsvArrayToModelArrayThroughCache(__METHOD__, self::STOPS_TXT, Stop::class, 'stop_id');
    }

    public function getStop(string $stopId)
    {
        $stops = $this->getStops();
        if (!key_exists($stopId, $stops)) {
            return null;
        }
        return $stops[$stopId];
    }

    /**
     * @return Trip[]
     */
    public function getTrips(): array
    {
        return $this->loadCsvArrayToModelArrayThroughCache(__METHOD__, self::TRIPS_TXT, Trip::class, 'trip_id');
    }

    public function getTrip(string $tripId)
    {
        $trips = $this->getTrips();
        if (!key_exists($tripId, $trips)) {
            return null;
        }
        return $trips[$tripId];
    }

    /**
     * @return Route[]
     */
    public function getRoutes(): array
    {
        return $this->loadCsvArrayToModelArrayThroughCache(__METHOD__, self::ROUTES_TXT, Route::class, 'route_id');
    }

    public function getRoute(string $routeId)
    {
        $routes = $this->getRoutes();
        if (!key_exists($routeId, $routes)) {
            return null;
        }
        return $routes[$routeId];
    }

    /**
     * @return StopTime[]
     */
    public function getStopTimes(): array
    {
        return $this->loadCsvArrayToModelArrayThroughCache(__METHOD__, self::STOP_TIMES_TXT, StopTime::class);
    }

    public function getStopTimesForTrip(string $tripId)
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

    public function getStopTimesForStop(string $stopId)
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
     * @return ShapePoint[]
     */
    public function getShapePoints(): array
    {
        if ($this->getCachedResult(__METHOD__) == null) {
            $this->setCachedResult(__METHOD__, $this->deserializeShapesCSV(self::SHAPES_TXT));
        }
        return $this->getCachedResult(__METHOD__);
    }

    /**
     * @param string $shapeId
     *
     * @return ShapePoint[]
     */
    public function getShape(string $shapeId): array
    {
        if ($this->getCachedResult(__METHOD__ . "::" . $shapeId) != null) {
            return $this->getCachedResult(__METHOD__ . "::" . $shapeId);
        }

        $shapePoints = $this->getShapePoints();
        $i = $this->findIndexOfFirstFieldOccurrence($shapePoints, 'getShapeId', $shapeId);
        $result = [];
        while ($shapePoints[$i]->getShapeId() == $shapeId) {
            $result[] = $shapePoints[$i];
            $i++;
        }

        $this->setCachedResult(__METHOD__ . "::" . $shapeId, $result);
        return $result;
    }

    /**
     * @return CalendarEntry[]
     */
    public function getCalendar(): array
    {
        return $this->loadCsvArrayToModelArrayThroughCache(__METHOD__, self::CALENDAR_TXT, CalendarEntry::class);
    }

    /**
     * @return CalendarDate[]
     */
    public function getCalendarDates(): array
    {
        return $this->loadCsvArrayToModelArrayThroughCache(__METHOD__, self::CALENDAR_DATES_TXT, CalendarDate::class);
    }

    /**
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

    /**
     * @return Transfer[]
     */
    public function getTransfers(): array
    {
        return $this->loadCsvArrayToModelArrayThroughCache(__METHOD__, self::TRANSFERS_TXT, Transfer::class);
    }


    /**
     * @return FeedInfo[]
     */
    public function getFeedInfo(): array
    {
        return $this->loadCsvArrayToModelArrayThroughCache(__METHOD__, self::FEED_INFO_TXT, FeedInfo::class);
    }

    public function deleteUncompressedFiles()
    {
        // Remove temporary data.
        if (!file_exists($this->fileRoot)) {
            return;
        }
        $files = scandir($this->fileRoot);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                // Remove all extracted files from the zip file.
                unlink($this->fileRoot . '/' . $file);
            }
        }
        reset($files);
        // Remove the empty folder.
        rmdir($this->fileRoot);
    }

    /**
     * Load a CSV file and store it in an associative array with the first CSV column value as key.
     * Each line is stored as an associative array using column headers as key and the fields as value.
     *
     * @param         $csvPath    string File path leading to the CSV file.
     *
     * @param         $dataModel  string  The name of the class name in which the data should be stored.
     *
     * @param         $indexField null|String The field, if any, which uniquely identifies a row and can be used as
     *                            index.
     *
     * @return array the deserialized data
     */
    private function deserializeCSV($csvPath, $dataModel, $indexField = null): array
    {
        // Open the CSV file and read it into an associative array
        $resultingObjects = $fieldNames = [];;

        $handle = @fopen($this->fileRoot . $csvPath, "r");
        if ($handle) {
            while (($row = fgetcsv($handle)) !== false) {
                // Read the header row
                if (empty($fieldNames)) {
                    $fieldNames = $row;
                    continue;
                }
                // Read a data row
                $rowData = [];
                foreach ($row as $k => $value) {
                    $rowData[$fieldNames[$k]] = $value;
                }
                if ($indexField == null) {
                    $resultingObjects[] = new $dataModel($this, $rowData);
                } else {
                    $resultingObjects[$rowData[$indexField]] = new $dataModel($this, $rowData);
                }
            }
            if (!feof($handle)) {
                throw new RuntimeException("Failed to read data from file");
            }
            fclose($handle);
        }
        return $resultingObjects;
    }

    /**
     * This is a modified version of deserializeCSV, in order to optimize the speed when handling shapes
     *
     * @param         $csvPath    string File path leading to the CSV file.
     *
     * @return array the deserialized data, sorted by shape_id.
     */
    private function deserializeShapesCSV($csvPath): array
    {
        // Open the CSV file and read it into an associative array
        $resultingObjects = $fieldNames = [];;

        $handle = @fopen($this->fileRoot . $csvPath, "r");
        if ($handle) {
            while (($row = fgetcsv($handle)) !== false) {
                // Read the header row
                if (empty($fieldNames)) {
                    $fieldNames = $row;
                    continue;
                }
                // Read a data row
                $rowData = [];
                foreach ($row as $k => $value) {
                    $rowData[$fieldNames[$k]] = $value;
                }
                $index = $rowData['shape_id'] . '-' . $rowData['shape_pt_sequence'];
                $resultingObjects[$index] = new ShapePoint($this, $rowData);
            }
            if (!feof($handle)) {
                throw new RuntimeException("Failed to read data from file");
            }
            fclose($handle);
        }
        return array_values($resultingObjects);
    }

    /**
     * Find the index of the first ShapePoint for a given field value, by using binary search.
     *
     * @param $array     array The data to search through
     * @param $getter    string The name of the getter for the field to search
     * @param $value     string The value to search
     *
     * @return int
     */
    private function findIndexOfFirstFieldOccurrence(array $array, string $getter, string $value): int
    {
        return $this->doBinarySearch($array, $getter, $value, 0, count($array));
    }

    private function doBinarySearch(array $array, string $getter, string $value, int $start, int $stop)
    {
        if ($start == $stop) {
            return $start;
        }
        $middle = $start + (($stop - $start) / 2);

        if ($array[$middle]->$getter() > $value) {
            return $this->doBinarySearch($array, $getter, $value, $start, $middle);
        }

        if ($array[$middle]->$getter() < $value) {
            return $this->doBinarySearch($array, $getter, $value, $middle, $stop);
        }

        // If the value equals, we just loop back to the first match
        while ($middle > 0 && $array[$middle - 1]->$getter() == $value) {
            $middle--;
        }
        return $middle;
    }

    /**
     * Get a cached method result.
     *
     * @param string $method The method for which the cached result should be obtained.
     *
     * @return null | mixed Null if no data is available, the cached data if available.
     */
    private function getCachedResult(string $method)
    {
        if (!key_exists($method, $this->_cache)) {
            return null;
        }
        return $this->_cache[$method];
    }

    /**
     * Cache data to ensure a method call doesn't do work twice.
     *
     * @param string $method The method which generated the data.
     * @param array  $value  The value to cache.
     */
    private function setCachedResult(string $method, array $value)
    {
        $this->_cache[$method] = $value;
    }

    /**
     * @param $method
     * @param $file
     * @param $class
     * @param $index
     *
     * @return mixed|null
     */
    private function loadCsvArrayToModelArrayThroughCache($method, $file, $class, $index = null)
    {
        if ($this->getCachedResult($method) == null) {
            $this->setCachedResult($method, $this->deserializeCSV($file, $class, $index));
        }
        return $this->getCachedResult($method);
    }
}