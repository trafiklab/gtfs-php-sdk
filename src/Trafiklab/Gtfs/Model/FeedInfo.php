<?php


namespace Trafiklab\Gtfs\Model;


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
     * @return mixed
     */
    public function getFeedPublisherName()
    {
        return $this->feed_publisher_name;
    }

    /**
     * @return mixed
     */
    public function getFeedPublisherUrl()
    {
        return $this->feed_publisher_url;
    }

    /**
     * @return mixed
     */
    public function getFeedLang()
    {
        return $this->feed_lang;
    }

    /**
     * @return mixed
     */
    public function getFeedStartDate()
    {
        return $this->feed_start_date;
    }

    /**
     * @return mixed
     */
    public function getFeedEndDate()
    {
        return $this->feed_end_date;
    }

    /**
     * @return mixed
     */
    public function getFeedVersion()
    {
        return $this->feed_version;
    }

    /**
     * @return mixed
     */
    public function getFeedContactEmail()
    {
        return $this->feed_contact_email;
    }

    /**
     * @return mixed
     */
    public function getFeedContactUrl()
    {
        return $this->feed_contact_url;
    }
}