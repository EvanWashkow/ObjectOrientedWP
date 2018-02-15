<?php
namespace WordPress\Sites\Models;

use \WordPress\Sites;

/**
 * Defines the basic structure for a Site model
 */
abstract class _Site
{
    
    /***************************************************************************
    *                             GENERAL INFORMATION
    ***************************************************************************/
    
    /**
     * Get the unique identifier for this site (blog)
     *
     * @return int
     */
    abstract public function getID();
    
    
    /**
     * Get the site title
     *
     * @return string
     */
    abstract public function getTitle();
    
    
    /**
     * Set the site title
     *
     * @param string $title The new site title
     */
    abstract public function setTitle( string $title );
    
    
    /**
     * Get the site description
     *
     * @return string
     */
    abstract public function getDescription();
    
    
    /**
     * Set the site description
     *
     * @param string $description The new site description
     */
    abstract public function setDescription( string $description );
    
    
    /***************************************************************************
    *                                    URLS
    ***************************************************************************/
    
    /**
     * Retrieve the primary site URL
     *
     * If you want the front-facing home URL, see getURLs()
     *
     * @return string
     */
    abstract public function getURL();
    
    
    /**
     * Retrieve the home page URL for this site
     *
     * @return array
     */
    abstract public function getHomePageURL();
    
    
    /**
     * Returns "http" or "https" for the primary URL
     *
     * @return string
     */
    abstract public function getProtocol();
    
    
    /***************************************************************************
    *                           PLUGINS AND THEMES
    ***************************************************************************/
    
    /**
     * Retrieve the currently-active theme ID
     *
     * Use \WordPress\Themes for related theme management
     *
     * @return string
     */
    abstract public function getActiveThemeID();
    
    
    /**
     * Retrieve the active plugin IDs for this site (does not include network)
     *
     * Use \WordPress\Plugins for related plugin management
     *
     * @return array
     */
    abstract public function getActivePluginIDs();
    
    
    /***************************************************************************
    *                               ADMINISTRATION
    ***************************************************************************/
    
    /**
     * Retrieve the administator's email address
     *
     * @return string
     */
    abstract public function getAdministratorEmail();
    
    
    /**
     * Change the administator's email address
     *
     * @param string $email The new administrator email address
     */
    abstract public function setAdministratorEmail( string $email );
    
    
    /**
     * Get the default user role identifier
     *
     * Use \WordPress\Users\Roles for related user role management
     *
     * @return string
     */
    abstract public function getDefaultUserRoleID();
    
    
    /**
     * Get time zone for this site
     *
     * @return \WordPress\TimeZone
     */
    abstract public function getTimeZone();
    
    
    /**
     * Set time zone for this site
     *
     * @param \WordPress\TimeZone $timeZone
     */
    abstract public function setTimeZone( \WordPress\TimeZone $timeZone );
    
    
    /***************************************************************************
    *                               UTILITIES
    ***************************************************************************/
    
    /**
     * Retrieve a property for this site
     *
     * @param string $key          The property key
     * @param mixed  $defaultValue The property's default value
     * @return mixed The property value
     */
    final public function get( string $key, $defaultValue = NULL )
    {
        // Variables
        $key   = self::sanitizeKey( $key );
        $value = $defaultValue;
        
        // Retrieve value
        if ( '' != $key ) {
            Sites::SwitchTo( $this->getID() );
            $value = get_option( $key, $defaultValue );
            Sites::SwitchBack();
        }
        return $value;
    }
    
    /**
     * Set a property on this site
     *
     * @param string $key   The property key
     * @param mixed  $value The new value for the property
     */
    final public function set( string $key, $value )
    {
        // Variables
        $key = self::sanitizeKey( $key );
        
        // Set value
        if ( '' != $key ) {
            Sites::SwitchTo( $this->getID() );
            update_option( $key, $value );
            Sites::SwitchBack();
        }
    }
    
    
    /**
     * Sanitize the site property key
     *
     * @param string $key The property key
     * @return string
     */
    final protected static function sanitizeKey( string $key )
    {
        return trim( $key );
    }
}
