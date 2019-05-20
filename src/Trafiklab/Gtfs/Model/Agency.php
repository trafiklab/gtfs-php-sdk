<?php


namespace Trafiklab\Gtfs\Model;


class Agency
{
    private $agency_id;
    private $agency_name;
    private $agency_url;
    private $agency_timezone;
    private $agency_lang;
    private $agency_phone;
    private $agency_fare_url;
    private $agency_email;


    private $archive;

    /**
     * Agency constructor.
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

    public function getAgencyId(): string
    {
        return $this->agency_id;
    }

    public function getAgencyName(): string
    {
        return $this->agency_name;
    }


    public function getAgencyUrl(): string
    {
        return $this->agency_url;
    }

    public function getAgencyTimezone(): ?string
    {
        return $this->agency_timezone;
    }


    public function getAgencyLang(): ?string
    {
        return $this->agency_lang;
    }


    public function getAgencyPhone(): ?string
    {
        return $this->agency_phone;
    }


    public function getAgencyFareUrl(): ?string
    {
        return $this->agency_fare_url;
    }


    public function getAgencyEmail(): ?string
    {
        return $this->agency_email;
    }
}