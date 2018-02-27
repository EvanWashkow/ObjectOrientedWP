<?php
namespace WordPress\Plugins\Models;

use WordPress\Sites;

/**
 * Represents a single WordPress plugin
 */
class Plugin extends _Plugin
{
    
    final public function getAuthorName()
    {
        return $this->get( 'Author', '' );
    }
    
    final public function getAuthorURL()
    {
        return $this->get( 'AuthorURI', '' );
    }
    
    final public function getDescription()
    {
        return $this->get( 'Description', '' );
    }
    
    final public function getName()
    {
        return $this->get( 'Name', '' );
    }
    
    final public function getVersion()
    {
        return $this->get( 'Version', '' );
    }
    
    final public function requiresGlobalActivation()
    {
        return $this->get( 'Network', false );
    }
    
    
    /***************************************************************************
    *                                 ACTIVATING
    ***************************************************************************/
    
    /**
     * Activate the plugin
     *
     * @param int $siteID The site ID or a \WordPress\Sites constant
     * @return bool True if the plugin is active
     */
    final public function activate( int $siteID = Sites::ALL )
    {
        if ( $this->canActivate( $siteID )) {
            
            // Variables
            $siteID = Sites::SanitizeID( $siteID );
            
            // Activate globally, for all sites
            if ( Sites::ALL === $siteID ) {
                $result = activate_plugin( $this->getRelativePath(), null, true );
            }
            
            // Activate on the single site
            else {
                \WordPress\Sites::SwitchTo( $siteID );
                $result = activate_plugin( $this->getRelativePath() );
                \WordPress\Sites::SwitchBack();
            }
        }
        
        return $this->isActive( $siteID );
    }
    
    
    /**
     * Can the plugin be activated?
     *
     * @param int $siteID The site ID or a \WordPress\Sites constant
     * @return bool
     */
    final public function canActivate( int $siteID = Sites::ALL )
    {
        // Variables
        $siteID = Sites::SanitizeID( $siteID );
        
        // Evaluate
        return (
            Sites::INVALID !== $siteID  &&
            !$this->isActive( $siteID ) &&
            (
                !is_multisite()                    ||
                !$this->requiresGlobalActivation() ||
                Sites::ALL === $siteID
            )
        );
    }
    
    /**
     * Deactivate the plugin
     *
     * @param int $siteID The site ID or a \WordPress\Sites constant
     * @return bool True if the plugin is no longer active
     */
    final public function deactivate( int $siteID = Sites::ALL )
    {
        // Variables
        $siteID = Sites::SanitizeID( $siteID );
        
        // Deactivate globally, for all sites
        if ( Sites::ALL === $siteID ) {
            deactivate_plugins( $this->getRelativePath(), false, true );
        }
        
        // Deactivate for a single site
        elseif ( Sites::INVALID !== $siteID ) {
            Sites::SwitchTo( $siteID );
            deactivate_plugins( $this->getRelativePath(), false, false );
            Sites::SwitchBack();
        }
        
        return !$this->isActive( $siteID );
    }
    
    
    /**
     * Is the plugin activated?
     *
     * When checking activated plugins for a single site, also check the
     * globally-activated plugins.
     *
     * @param int $siteID The site ID or a \WordPress\Sites constant
     * @return bool
     */
    final public function isActive( int $siteID = Sites::ALL )
    {
        // Variables
        $isActive = false;
        $siteID   = Sites::SanitizeID( $siteID );
        
        // Determine if plugin is activate globally, for all sites
        if ( Sites::ALL === $siteID ) {
            $isActive = is_plugin_active_for_network( $this->getRelativePath() );
        }
        
        // Determine if plugin is activated for a single site
        elseif ( Sites::INVALID !== $siteID ) {
            
            // Include files
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            
            // Lookup active plugins for the site
            Sites::SwitchTo( $siteID );
            $isActive = is_plugin_active( $this->getRelativePath() );
            Sites::SwitchBack();
        }
        
        // Plugin not active
        return $isActive;
    }
}
