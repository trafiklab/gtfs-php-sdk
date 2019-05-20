<?php


namespace Trafiklab\Gtfs\Model;


class CalendarDate
{

    private $archive;

    private $service_id;
    private $date;
    private $exception_type;

    /**
     * CalendarDate constructor.
     *
     * @param GtfsArchive $archive The archive in which this data originates, used to link between files.
     * @param array       $data    An associative array containing the variable values.
     *
     * @internal Not to be used outside of the Trafiklab\Gtfs\Model package.
     */
    function __construct(GtfsArchive $archive, array $data)
    {
        foreach ($data as $variable => $value) {
            $this->$variable = $value;
        }
        $this->archive = $archive;
    }


    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return mixed
     */
    public function getExceptionType()
    {
        return $this->exception_type;
    }

    /**
     * @return mixed
     */
    public function getServiceId()
    {
        return $this->service_id;
    }
}