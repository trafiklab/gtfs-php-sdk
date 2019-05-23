<?php


namespace Trafiklab\Gtfs\Util;


use RuntimeException;
use Trafiklab\Gtfs\Model\GtfsArchive;

class GtfsParserUtil
{
    /**
     * Load a CSV file and store it in an associative array with the first CSV column value as key.
     * Each line is stored as an associative array using column headers as key and the fields as value.
     *
     * @param         $csvPath         string File path leading to the CSV file.
     *
     * @param         $dataModelClass  string  The name of the class name in which the data should be stored.
     *
     * @param         $indexField      null|String The field, if any, which uniquely identifies a row and can be used as
     *                                 index.
     *
     * @return array the deserialized data
     */
    public static function deserializeCSV(GtfsArchive $gtfsArchive, string $csvPath,
                                          string $dataModelClass, $indexField = null): array
    {
        // Open the CSV file and read it into an associative array
        $resultingObjects = $fieldNames = [];;

        $handle = @fopen($csvPath, "r");
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
                    $resultingObjects[] = new $dataModelClass($gtfsArchive, $rowData);
                } else {
                    $resultingObjects[$rowData[$indexField]] = new $dataModelClass($gtfsArchive, $rowData);
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
    public static function deserializeCSVWithCompositeIndex(GtfsArchive $gtfsArchive, string $csvPath,
                                                           string $dataModelClass, $firstIndexField, $secondIndexField): array
    {
        // Open the CSV file and read it into an associative array
        $resultingObjects = $fieldNames = [];;

        $handle = @fopen($csvPath, "r");
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
                $index = $rowData[$firstIndexField] . '-' . $rowData[$secondIndexField];
                $resultingObjects[$index] = new $dataModelClass($gtfsArchive, $rowData);
            }
            if (!feof($handle)) {
                throw new RuntimeException("Failed to read data from file");
            }
            fclose($handle);
        }
        return $resultingObjects;
    }

}