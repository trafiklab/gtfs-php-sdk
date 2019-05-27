<?php


namespace Trafiklab\Gtfs\Model;


use Exception;
use Trafiklab\Gtfs\Model\Files\GtfsAgencyFile;
use Trafiklab\Gtfs\Model\Files\GtfsCalendarDatesFile;
use Trafiklab\Gtfs\Model\Files\GtfsCalendarFile;
use Trafiklab\Gtfs\Model\Files\GtfsFeedInfoFile;
use Trafiklab\Gtfs\Model\Files\GtfsRoutesFile;
use Trafiklab\Gtfs\Model\Files\GtfsShapesFile;
use Trafiklab\Gtfs\Model\Files\GtfsStopsFile;
use Trafiklab\Gtfs\Model\Files\GtfsStopTimesFile;
use Trafiklab\Gtfs\Model\Files\GtfsTransfersFile;
use Trafiklab\Gtfs\Model\Files\GtfsTripsFile;
use Trafiklab\Gtfs\Util\Internal\ArrayCache;
use ZipArchive;

class GtfsArchive
{
    use ArrayCache;

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
     * @return GtfsAgencyFile
     */
    public function getAgencyFile(): GtfsAgencyFile
    {
        return $this->loadGtfsFileThroughCache(__METHOD__, self::AGENCY_TXT, GtfsAgencyFile::class);
    }

    /**
     * @return GtfsCalendarDatesFile
     */
    public function getCalendarDatesFile(): GtfsCalendarDatesFile
    {
        return $this->loadGtfsFileThroughCache(__METHOD__, self::CALENDAR_DATES_TXT, GtfsCalendarDatesFile::class);
    }

    /**
     * @return GtfsCalendarFile
     */
    public function getCalendarFile(): GtfsCalendarFile
    {
        return $this->loadGtfsFileThroughCache(__METHOD__, self::CALENDAR_TXT, GtfsCalendarFile::class);
    }

    /**
     * @return GtfsFeedInfoFile
     */
    public function getFeedInfoFile(): GtfsFeedInfoFile
    {
        return $this->loadGtfsFileThroughCache(__METHOD__, self::FEED_INFO_TXT, GtfsFeedInfoFile::class);
    }

    /**
     * @return GtfsRoutesFile
     */
    public function getRoutesFile(): GtfsRoutesFile
    {
        return $this->loadGtfsFileThroughCache(__METHOD__, self::ROUTES_TXT, GtfsRoutesFile::class);
    }

    /**
     * @return GtfsShapesFile
     */
    public function getShapesFile(): GtfsShapesFile
    {
        return $this->loadGtfsFileThroughCache(__METHOD__, self::SHAPES_TXT, GtfsShapesFile::class);
    }

    /**
     * @return GtfsStopsFile
     */
    public function getStopsFile(): GtfsStopsFile
    {
        return $this->loadGtfsFileThroughCache(__METHOD__, self::STOPS_TXT, GtfsStopsFile::class);
    }

    /**
     * @return GtfsStopTimesFile
     */
    public function getStopTimesFile(): GtfsStopTimesFile
    {
        return $this->loadGtfsFileThroughCache(__METHOD__, self::STOP_TIMES_TXT, GtfsStopTimesFile::class);
    }

    /**
     * @return GtfsTransfersFile
     */
    public function getTransfersFile(): GtfsTransfersFile
    {
        return $this->loadGtfsFileThroughCache(__METHOD__, self::TRANSFERS_TXT, GtfsTransfersFile::class);
    }

    /**
     * @return GtfsTripsFile
     */
    public function getTripsFile(): GtfsTripsFile
    {
        return $this->loadGtfsFileThroughCache(__METHOD__, self::TRIPS_TXT, GtfsTripsFile::class);
    }

    /**
     * Delete the uncompressed files. This should be done as a cleanup when you're ready.
     */
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

    private function loadGtfsFileThroughCache(string $method, string $file, string $class)
    {
        if ($this->getCachedResult($method) == null) {
            $this->setCachedResult($method, new $class($this, $this->fileRoot . $file));
        }
        return $this->getCachedResult($method);
    }
}