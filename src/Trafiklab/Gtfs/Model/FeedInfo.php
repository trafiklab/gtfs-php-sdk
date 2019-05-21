<?php


namespace Trafiklab\Gtfs\Model;


use DateTime;

class FeedInfo
{

    private $feed_publisher_name;
    private $feed_publisher_url;
    private $feed_lang;
    private $feed_start_date;
    private $feed_end_date;
    private $feed_version;
    private $feed_contact_email;
    private $feed_contact_url;
    private $archive;

    /**
     * FeedInfo constructor.
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
     * Contains the full name of the organization that publishes the dataset. This can be the same as one of the
     * agency_name values in agency.txt.
     *
     * @return mixed
     */
    public function getFeedPublisherName(): string
    {
        return $this->feed_publisher_name;
    }

    /**
     * Contains the URL of the dataset publishing organization's website. This can be the same as one of the agency_url
     * values in agency.txt.
     *
     * @return mixed
     */
    public function getFeedPublisherUrl(): string
    {
        return $this->feed_publisher_url;
    }

    /**
     * Specifies the default language used for the text in this dataset. This setting helps GTFS consumers choose
     * capitalization rules and other language-specific settings for the feed.
     *
     * @return mixed
     */
    public function getFeedLang(): string
    {
        return $this->feed_lang;
    }

    /**
     * The dataset provides complete and reliable schedule information for service in the period from the beginning of
     * the feed_start_date day to the end of the feed_end_date day.
     *
     * If both feed_end_date and feed_start_date are given, the end date must not precede the start date. Dataset
     * providers are encouraged to give schedule data outside this period to advise passengers of likely future
     * service, but dataset consumers should be mindful of its non-authoritative status.
     *
     * If calendar.txt and calendar_dates.txt omit any active calendar dates that are included within the timeframe
     * defined by feed_start_date and feed_end_date, this is an explicit statement that there's no service on those
     * omitted days. That is, calendar.txt and calendar_dates.txt are assumed to be an exhaustive list of the dates
     * when service is provided.
     *
     * @return DateTime | null
     */
    public function getFeedStartDate(): ?DateTime
    {
        if ($this->feed_start_date == null) return null;
        return DateTime::createFromFormat("Ymd", $this->feed_start_date);
    }

    /**
     * For details on this field, see feed_start_date.
     *
     * @return DateTime | null
     */
    public function getFeedEndDate(): ?DateTime
    {
        if ($this->feed_end_date == null) return null;
        return DateTime::createFromFormat("Ymd", $this->feed_end_date);
    }

    /**
     * Specifies a string that indicates the current version of the GTFS dataset. GTFS-consuming applications can
     * display this value to help dataset publishers determine whether the latest version of their dataset has been
     * incorporated.
     *
     * @return string
     */
    public function getFeedVersion(): ?string
    {
        return $this->feed_version;
    }

    /**
     * Provides an email address for communication about the GTFS dataset and data publishing practices. The
     * feed_contact_email field provides a technical contact for GTFS-consuming applications. To provide customer
     * service contact information, use the fields in agency.txt.
     *
     * @return string
     */
    public function getFeedContactEmail(): ?string
    {
        return $this->feed_contact_email;
    }

    /**
     * Provides a URL for contact information, a web form, support desk, or other tool for communication about the GTFS
     * dataset and data publishing practices. The feed_contact_url field provides a technical contact for
     * GTFS-consuming applications. To provide customer service contact information, use the fields in agency.txt.
     *
     * @return string
     */
    public function getFeedContactUrl(): ?string
    {
        return $this->feed_contact_url;
    }
}