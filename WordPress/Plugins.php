<?php
namespace WordPress;

/**
 * Manages WordPress plugins
 */
class Plugins
{
    
    /**
     * Constant specifier for all sites
     *
     * @var int
     */
    const ALL_SITES = 0;
    
    /**
     * Cache of all WordPress plugins
     *
     * @var \PHP\Cache
     */
    private static $cache;
    
    
    /**
     * Initializes the plugins manager (automatically-invoked on class load)
     */
    public static function Initialize()
    {
        if ( !isset( self::$cache )) {
            self::$cache = new \PHP\Cache();
        }
    }
    
    
    /***************************************************************************
    *                                   MAIN
    ***************************************************************************/
    
    /**
     * Retrieve all plugin(s)
     *
     * @param mixed $mixed The plugin ID; array of plugin IDs; null to retrieve all
     * @return Plugins\Models\Plugin|null|array
     */
    public static function Get( $mixed = null )
    {
        // Route to the corresponding function
        if ( null === $mixed ) {
            return self::getAll();
        }
        elseif ( is_array( $mixed )) {
            return self::getMultiple( $mixed );
        }
        elseif ( is_string( $mixed )) {
            return self::getSingle( $mixed );
        }
        
        // Parameter was invalid. Returning null.
        return null;
    }
    
    
    /***************************************************************************
    *                               SUB-ROUTINES
    ***************************************************************************/
    
    /**
     * Retrieve all plugin(s)
     *
     * @return array
     */
    private static function getAll()
    {
        // Build cache
        if ( !self::$cache->isComplete() ) {
            
            // Include WordPress plugins function
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            
            // For each WordPress plugin, create and cache our own object
            $plugins = get_plugins();
            foreach ( $plugins as $pluginFile => $pluginData ) {
                $plugin = Plugins\Models::Create( $pluginFile, $pluginData );
                self::$cache->add( $plugin->getID(), $plugin );
            }
            
            // Mark cache complete
            self::$cache->markComplete();
        }
        
        // Return plugins
        return self::$cache->get();
    }
    
    
    /**
     * Get multiple plugins by their IDs
     *
     * @param array $pluginIDs List of plugin ids to return
     * @return array Plugins indexed by their corresponding IDs
     */
    private static function getMultiple( array $pluginIDs )
    {
        $plugins = [];
        if ( 0 < count( $pluginIDs )) {
            $plugins = self::getAll();
            $plugins = array_intersect_key( $plugins, array_flip( $pluginIDs ) );
        }
        return $plugins;
    }
    
    
    /**
     * Retrieve a single plugin by its ID
     *
     * @param string $pluginID The plugin ID
     * @return Plugins\Models\Plugin|null
     */
    private static function getSingle( string $pluginID )
    {
        $plugin  = null;
        $plugins = self::getAll();
        if ( array_key_exists( $pluginID, $plugins )) {
            $plugin = $plugins[ $pluginID ];
        }
        return $plugin;
    }
}
Plugins::Initialize();
