<?php
namespace WordPress;

/**
 * Manages WordPress plugins
 */
class Plugins
{
    
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
    
    
    /**
     * Retrieve all plugin(s)
     *
     * @param string $pluginID The plugin ID
     * @return Plugins\Models\Plugin|null|array
     */
    public static function Get( string $pluginID = '' )
    {
        // Route to the corresponding function
        if ( '' == $pluginID ) {
            return self::getAll();
        }
        else {
            return self::getSingle( $pluginID );
        }
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
