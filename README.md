# Trafiklab GTFS(-RT) PHP SDK

[![Build status](https://travis-ci.com/trafiklab/gtfs-php-sdk.svg?branch=master)](https://travis-ci.com/trafiklab/gtfs-php-sdk)
[![Latest Stable Version](https://poser.pugx.org/trafiklab/gtfs-php-sdk/v/stable)](https://packagist.org/packages/trafiklab/gtfs-php-sdk)
[![codecov](https://codecov.io/gh/trafiklab/gtfs-php-sdk/branch/master/graph/badge.svg)](https://codecov.io/gh/trafiklab/gtfs-php-sdk)
[![License: MPL 2.0](https://img.shields.io/badge/License-MPL%202.0-brightgreen.svg)](https://opensource.org/licenses/MPL-2.0)

This SDK makes it easier for developers to use GTFS data in their PHP projects. At this moment, only static files are supported.

## Installation
You can install this package through composer

```composer require trafiklab/gtfs-php-sdk```

## Usage and examples

**Opening a GTFS file**

You can either load a local GTFS zip file, or you can download it over HTTP

```php
$gtfsArchive = GtfsArchive::createFromPath("gtfs.zip");
$gtfsArchive = GtfsArchive::createFromUrl("http://example.com/gtfs.zip");
```

Optionally, you can choose to download a GTFS zip file only if it has changed since the last retrieval. This is useful when trying to automate GTFS retrievals that need to be stored within a database, without constantly rewriting the same data each time.
The following Http Headers are used:

 - https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Last-Modified
 - https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/If-Modified-Since
 - https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/ETag

Most agencies provide a Last-Modified header, but if it's not present, ETag is the best route to go. If for some reason ETag is also not provided, it will just continue normally as if you were to use the original GtfsArchive::createFromUrl() method.

```php
 $gtfsArchive = GtfsArchive::createFromUrlIfModified(
    "http://example.com/gtfs.zip",
    "Wed, 10 Jun 2020 15:56:14 GMT",
    "99fa-5a7bce236c526"
 );
```

If you don't have an ETag or Last-Modified value to begin with, simply leave them out and the method will retrieve them for you.

```php
 if ($gtfsArchive = GtfsArchive::createFromUrlIfModified("http://example.com/gtfs.zip") {
    // Get Methods return null if GTFS Url does not contain the specified Header: ETag, Last-Modified.
    $lastModified = $gtfsArchive->getLastModified(); // Wed, 10 Jun 2020 15:56:14 GMT | null
    $eTag         = $gtfsArchive->getETag(); // "99fa-5a7bce236c526" | null
    
    // You can get the Last-Modified datetime PHP Object (Useful for storing in databases) by doing the following:
    $datetime = $gtfsArchive->getLastModifiedDateTime() // DateTime Object | null
    
    // Or Convert it back to a String using the standard Last-Modified HTTP header format.
    if ($datetime) {
      $lastModified = GtfsArchive::getLastModifiedFromDateTime($datetime); // Wed, 10 Jun 2020 15:56:14 GMT
    }  
 }
 
```


Files are extracted to a temporary directory (/tmp/gtfs/), and cleaned up when the GtfsArchive object is destructed.
You can call `$gtfsArchive->deleteUncompressedFiles()` to manually remove the uncompressed files. 

**Reading a file**
```php
$agencyTxt = $gtfsArchive->getAgencyFile(); // read agency.txt
$calendarTxt = $gtfsArchive->getCalendarFile(); // read calendar.txt
$routesTxt = $gtfsArchive->getRoutesFile(); // read routes.txt
$stopTimesTxt = $gtfsArchive->getStopTimesFile(); // read stop_times.txt
...
```

All files are lazy loaded and cached. This means that data is only loaded after calling a method such as `getStopTimesFile()`. 
Keep in mind that in can take a while to read the data for the first time. It can take up to a minute to read a large `stop_times.txt` file.

**Reading file data**

Every file class contains a method to read all data in that file. Some classes contain additional helper methods for frequently used queries such as filtering by an id or foreign key.

There is one PHP class for every (supported) file, and another class for the data contained in one row of that file. The definition of each field is contained in the PHPDoc for each getter function, allowing you to focus on coding, and less on alt-tabbing between specification and code.
```php
$stopTimesTxt = $gtfsArchive->getStopTimesFile(); // The file is represented by a StopTimesFile object
$allStopTimes = $stopTimesTxt->getStopTimes(); // a method like this is always available
$stopTimesForStopX = $stopTimesTxt->getStopTimesForStop($stopId); // this is a helper method for foreign keys

$stopTime = $allStopTimes[0]; // Get the first row
$headsign = $stopTime->getStopHeadsign(); // One row of data is represented by a StopTime object
```   

## Contributing

We accept pull requests, but please create an issue first in order to discuss the addition or fix.
If you would like to see a new feature added, you can also create a feature request by creating an issue.

## Help

If you're stuck with a question, feel free to ask help through the Issue tracker.
- Need help with API keys? Please read [www.trafiklab.se/api-nycklar](https://www.trafiklab.se/api-nycklar) first.
- Do you want to check the current systems status? Service disruptions
 are published on the [Trafiklab homepage](https://www.trafiklab.se/)
