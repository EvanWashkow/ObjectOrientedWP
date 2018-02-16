<?php
namespace WordPress\Plugins;

/**
 * Creates new plugin models using the factory method
 */
class Models
{
    
    /**
     * Create a new plugin model instance
     *
     * @param string $pluginID The plugin identifier
     * @return Models\Plugin
     */
    final public static function Create( string $pluginID )
    {
        return new Models\Plugin( $pluginID );
    }
}
