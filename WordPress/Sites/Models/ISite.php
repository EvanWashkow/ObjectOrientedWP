<?php
namespace WordPress\Sites\Models;

use WordPress\Shared\ModelSpec;

/**
 * Defines the basic structure for a Site model
 */
interface ISite extends ModelSpec
{
    
    /***************************************************************************
    *                             GENERAL INFORMATION
    ***************************************************************************/
    
    /**
     * Get the unique identifier for this site (blog)
     *
     * @return int
     */
    public function getID(): int;
    
    
    /**
     * Get the site title
     *
     * @return string
     */
    public function getTitle(): string;
    
    
    /**
     * Set the site title
     *
     * @param string $title The new site title
     * @return bool Whether or not the title change was successful
     */
    public function setTitle( string $title ): bool;
    
    
    /**
     * Get the site description
     *
     * @return string
     */
    public function getDescription(): string;
    
    
    /**
     * Set the site description
     *
     * @param string $description The new site description
     * @return bool Whether or not the description change was successful
     */
    public function setDescription( string $description ): bool;
    
    
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
    public function getURL(): string;
    
    /**
     * Set the primary site URL
     *
     * If you want to set the front-facing home URL, see setHomePageURL()
     *
     * @param string $url The new URL
     * @return bool Whether or not the URL change was successful
     */
    public function setURL( string $url ): bool;
    
    
    /**
     * Retrieve the home page URL for this site
     *
     * @return array
     */
    public function getHomePageURL(): string;
    
    
    /**
     * Set the home page URL for this site
     *
     * @param string $url The new URL
     * @return bool Whether or not the URL change was successful
     */
    public function setHomePageURL( string $url ): bool;
    
    
    /**
     * Returns "http" or "https" for the primary URL
     *
     * @return string
     */
    public function getProtocol(): string;
    
    
    /***************************************************************************
    *                               ADMINISTRATION
    ***************************************************************************/
    
    /**
     * Retrieve the administator's email address
     *
     * @return string
     */
    public function getAdministratorEmail(): string;
    
    
    /**
     * Change the administator's email address
     *
     * @param string $email The new administrator email address
     * @return bool Whether or not the change was successful
     */
    public function setAdministratorEmail( string $email ): bool;
    
    
    /**
     * Get time zone for this site
     *
     * @return \WordPress\Sites\TimeZone
     */
    public function getTimeZone(): \WordPress\Sites\TimeZone;
    
    
    /**
     * Set time zone for this site
     *
     * @param \WordPress\Sites\TimeZone $timeZone
     * @return bool Whether or not the TimeZone change was successful
     */
    public function setTimeZone( \WordPress\Sites\TimeZone $timeZone ): bool;
    
    
    /***************************************************************************
    *                               MODEL EXTENSIONS
    ***************************************************************************/
    
    /**
     * Retrieve a property
     *
     * @param string $key          The property key
     * @param mixed $defaultValue The property's default value
     * @return mixed The property value
     */
    public function get( string $key, $defaultValue = NULL );
}
