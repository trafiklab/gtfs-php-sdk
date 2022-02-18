<?php


namespace Trafiklab\Gtfs\Model;


use Exception;
use Trafiklab\Gtfs\Model\Files\GtfsAgencyFile;
use Trafiklab\Gtfs\Model\Files\GtfsCalendarDatesFile;
use Trafiklab\Gtfs\Model\Files\GtfsCalendarFile;
use Trafiklab\Gtfs\Model\Files\GtfsFeedInfoFile;
use Trafiklab\Gtfs\Model\Files\GtfsFrequenciesFile;
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

    /** @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Last-Modified */
    private const LAST_MODIFIED_FORMAT = "D, d M Y H:i:s e";
    /** @var null|string $archiveLastModified */
    private static $archiveLastModified = null;
    /** @var null|string $archiveETag */
    private static $archiveETag = null;

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
     * Creates the context and returns it for use in file_get_contents
     * Adds the If-Modified-Since header to check if the GTFS has been modified.
     */
    private static function createRequestContext(?\DateTime $lastModified = null)
    {
        /** @var string $lastModifiedString */
        self::$archiveLastModified = $lastModified !== null ? self::getLastModifiedFromDateTime($lastModified) : '';
        return stream_context_create([
            'http' => [
                'header' => "If-Modified-Since: " . self::$archiveLastModified,
                'method'        => 'GET',
                'ignore_errors' => true
            ],
        ]);
    }

    /**
     * Only create a new Archive if it has been modified since the last PULL
     * We delete the Zip archive after each cycle as this is meant to constantly check headers.
     * @param string $url
     * @param \DateTime|null $lastModified
     * @param string|null $eTag
     * @return GtfsArchive|null
     * @throws Exception
     */
    public static function createFromUrlIfModified(
        string $url,
        ?\DateTime $lastModified = null,
        ?string $eTag = null
    ): ?GtfsArchive {
        $temp_file = self::TEMP_ROOT . md5($url) . ".zip";
        if (!file_exists($temp_file)) {
            try {
                /** Create the Request Context (Returns Stream Context) */
                $context = self::createRequestContext($lastModified);

                /** Download the zip file if it's been modified, or exists. */
                file_put_contents($temp_file, file_get_contents($url, false, $context));

                /**
                 * @var array $http_response_header materializes out of thin air unfortunately
                 * Parse the headers so we can retrieve what we need.
                 */
                $responseHeaders = self::parseHeaders($http_response_header);

                /** @var integer $statusCode
                 * Track the Status code to determine if the file has changed, or exists.
                 */
                $statusCode = $responseHeaders['Status'];

                switch ($statusCode) {
                    case 200:
                        /**
                         * If the Status Code is 200, that means the file has been modified or it is a new GTFS Source.
                         * Track the eTag and Last-Modified headers if they exist.
                         */
                        self::$archiveLastModified = $responseHeaders['Last-Modified'] ?? null;
                        self::$archiveETag = $responseHeaders['ETag'] ?? null;

                        /** If no last-modified date is present, and Etag is present and matches, skip as it's not modified */
                        if ($eTag !== null && self::$archiveETag !== null && $eTag == self::$archiveETag) {
                            return null;
                        } else {
                            /**
                             * Last-Modified Date wasn't present, and eTag is not present or didn't match.
                             * Extract files and return the GtfsArchive.
                             */
                            $fileRoot = self::extractFiles($temp_file, true);
                            return new GtfsArchive($fileRoot);
                        }
                    case 304:
                        /**  304 = NOT_MODIFIED (This file hasn't changed since the last pull) */
                        self::$archiveETag = $eTag;
                        break;
                    default:
                        /** Status Code returned a 400 error or similar meaning the URL is invalid or down. */
                        throw new Exception("Could not open the GTFS archive, Status Code: {$statusCode}");
                }
            } catch (Exception $exception) {
                throw new Exception(
                    "There was an issue downloading the GTFS from the requested URL: {$url}, Error: {$exception->getMessage()}"
                );
            } finally {
                /** Clean up - Delete the Downloaded Zip if it exists. */
                self::deleteArchive($temp_file);
            }
        }
        return null;
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

    /**
     * @throws Exception
     */
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
            self::deleteArchive($archiveFilePath);
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
     * @return GtfsFrequenciesFile
     */
    public function getFrequenciesFile(): GtfsFrequenciesFile
    {
        return $this->loadGtfsFileThroughCache(__METHOD__, self::FREQUENCIES_TXT, GtfsFrequenciesFile::class);
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

    private static function deleteArchive(string $archiveFilePath)
    {
        if (file_exists($archiveFilePath)) {
            unlink($archiveFilePath);
        }
    }

    private function loadGtfsFileThroughCache(string $method, string $file, string $class)
    {
        if ($this->getCachedResult($method) == null) {
            $this->setCachedResult($method, new $class($this, $this->fileRoot . $file));
        }
        return $this->getCachedResult($method);
    }

    private static function parseHeaders( array $headers ): array
    {
        $headersArray = [];
        foreach( $headers as $k => $v )
        {
            $t = explode( ':', $v, 2 );
            if( isset( $t[1] ) )
                $headersArray[ trim($t[0]) ] = trim( $t[1] );
            else
            {
                $headersArray[] = $v;
                if( preg_match( "#HTTP/[0-9\.]+\s+([0-9]+)#",$v, $out ) )
                    $headersArray['Status'] = intval($out[1]);
            }
        }
        return $headersArray;
    }

    /**
     * Return the DateTime Object for the Last Modified Date
     * Useful for storing in databases, etc.
     * @return \DateTime|null
     * @throws Exception
     */
    public function getLastModifiedDateTime(): ?\DateTime
    {
        $datetime = (
            new \DateTime(
                self::$archiveLastModified,
                new \DateTimeZone('GMT')
            )
        );
        return self::$archiveLastModified !== null ? $datetime : null;
    }

    /**
     * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Last-Modified
     * @param \DateTime $dateTime
     * @return string
     */
    public static function getLastModifiedFromDateTime(\DateTime $dateTime): string
    {
        return $dateTime
            ->setTimezone(new \DateTimeZone('GMT'))
            ->format(self::LAST_MODIFIED_FORMAT);
    }

    /**
     * @return string|null
     */
    public function getArchiveLastModified(): ?string
    {
        return self::$archiveLastModified ?? null;
    }

    /**
     * @return string|null
     */
    public function getArchiveETag(): ?string
    {
        return self::$archiveETag ?? null;
    }
}
