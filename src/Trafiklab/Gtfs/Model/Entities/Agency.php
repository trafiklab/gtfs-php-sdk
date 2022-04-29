<?php


namespace Trafiklab\Gtfs\Model\Entities;


use Trafiklab\Gtfs\Model\GtfsArchive;

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
            $this->$variable = isset($value) && $value !== '' ? $value : null;
        }
        $this->archive = $archive;
    }

    /**
     * Identifies a transit brand, which is often the same as a transit agency. Note that in some cases, such as when a
     * single agency operates multiple separate services, agencies and brands are distinct. This document uses the term
     * "agency" in place of "brand."
     *
     * A transit feed can represent data from more than one agency. This field is required for transit feeds that
     * contain data for multiple agencies. Otherwise, it's optional.
     *
     * @return string
     */
    public function getAgencyId(): string
    {
        return $this->agency_id;
    }

    /**
     * Contains the full name of the transit agency.
     *
     * @return string
     */
    public function getAgencyName(): string
    {
        return $this->agency_name;
    }

    /**
     * Contains the URL of the transit agency.
     *
     * @return string
     */
    public function getAgencyUrl(): string
    {
        return $this->agency_url;
    }

    /**
     *    Contains the timezone where the transit agency is located. If multiple agencies are specified in the feed,
     *    each must have the same agency_timezone.
     *
     * @return string | null
     */
    public function getAgencyTimezone(): ?string
    {
        return $this->agency_timezone;
    }

    /**
     * Specifies the primary language used by this transit agency. This setting helps GTFS consumers choose
     * capitalization rules and other language-specific settings for the dataset.
     *
     * @return string | null
     */
    public function getAgencyLang(): ?string
    {
        return $this->agency_lang;
    }

    /**
     *    Provides a voice telephone number for the specified agency. This field is a string value that presents the
     *    telephone number in a format typical for the agency's service area. It can and should contain punctuation
     *    marks to group the digits of the number. Dialable text, such as TriMet's 503-238-RIDE, is permitted, but the
     *    field must not contain any other descriptive text.
     *
     * @return string | null
     */
    public function getAgencyPhone(): ?string
    {
        return $this->agency_phone;
    }

    /**
     *    Specifies the URL of a web page where a rider can purchase tickets or other fare instruments for the agency
     *    online.
     *
     * @return string | null
     */
    public function getAgencyFareUrl(): ?string
    {
        return $this->agency_fare_url;
    }

    /**
     * Contains a valid email address that's actively monitored by the agency's customer service department. This email
     * address needs to be a direct contact point where transit riders can reach a customer service representative at
     * the agency.
     *
     * @return string | null
     */
    public function getAgencyEmail(): ?string
    {
        return $this->agency_email;
    }
}
