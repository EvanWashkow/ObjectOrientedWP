<?php
namespace WordPress\Sites\Models;

use WordPress\Shared\ModelSpec;

/**
 * Defines the basic structure for a Site model
 */
interface SiteSpec extends ModelSpec
{
    
    /***************************************************************************
    *                             GENERAL INFORMATION
    ***************************************************************************/
    
    /**
     * Get the unique identifier for this site (blog)
     *
     * @return int
     */
    public function getID();
    
    
    /**
     * Get the site title
     *
     * @return string
     */
    public function getTitle();
    
    
    /**
     * Set the site title
     *
     * @param string $title The new site title
     * @return bool Whether or not the title change was successful
     */
    public function setTitle( string $title );
    
    
    /**
     * Get the site description
     *
     * @return string
     */
    public function getDescription();
    
    
    /**
     * Set the site description
     *
     * @param string $description The new site description
     * @return bool Whether or not the description change was successful
     */
    public function setDescription( string $description );
    
    
    /***************************************************************************
    *                                    URLS
    ***************************************************************************/
    
    /**
     * Retrieve the primary site URL
     *
     * If you want to retrieve the front-facing home URL, see getHomePageURL()
     *
     * @return string
     */
    public function getURL();
    
    /**
     * Set the primary site URL
     *
     * If you want to set the front-facing home URL, see setHomePageURL()
     *
     * @param string $url The new URL
     * @return bool Whether or not the URL change was successful
     */
    public function setURL( string $url );
    
    
    /**
     * Retrieve the home page URL for this site
     *
     * @return array
     */
    public function getHomePageURL();
    
    
    /**
     * Set the home page URL for this site
     *
     * @param string $url The new URL
     * @return bool Whether or not the URL change was successful
     */
    public function setHomePageURL( string $url );
    
    
    /**
     * Returns "http" or "https" for the primary URL
     *
     * @return string
     */
    public function getProtocol();
    
    
    /***************************************************************************
    *                               ADMINISTRATION
    ***************************************************************************/
    
    /**
     * Retrieve the administator's email address
     *
     * @return string
     */
    public function getAdministratorEmail();
    
    
    /**
     * Change the administator's email address
     *
     * @param string $email The new administrator email address
     * @return bool Whether or not the change was successful
     */
    public function setAdministratorEmail( string $email );
    
    
    /**
     * Get time zone for this site
     *
     * @return \WordPress\Sites\TimeZone
     */
    public function getTimeZone();
    
    
    /**
     * Set time zone for this site
     *
     * @param \WordPress\Sites\TimeZone $timeZone
     * @return bool Whether or not the TimeZone change was successful
     */
    public function setTimeZone( \WordPress\Sites\TimeZone $timeZone );
}
