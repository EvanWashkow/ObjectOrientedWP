<?php
namespace WordPress\Plugins;

use PHP\Collections\Dictionary;
use PHP\Collections\ReadOnlyDictionary;
use WordPress\Plugins\Models\IPlugin;

/**
 * Creates new plugin models using the factory method
 */
class Models
{
    
    /**
     * Create a new plugin model instance
     *
     * @param string $relativePath Path to plugin file, relative to the plugins directory
     * @param array  $pluginData The mapped array of plugin details
     * @return IPlugin
     */
    final public static function Create( string $relativePath, array $pluginData ): IPlugin
    {
        $properties = new Dictionary( 'string' );
        foreach ( $pluginData as $index => $value ) {
            $properties->set( $index, $value );
        }
        $properties = new ReadOnlyDictionary( $properties );
        return new Models\Plugin( $relativePath, $properties );
    }
}
