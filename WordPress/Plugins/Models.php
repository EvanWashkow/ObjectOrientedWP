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
     * @param string $relativePath Path to plugin file, relative to the plugins directory
     * @param array  $pluginData The mapped array of plugin details
     * @return Models\Plugin
     */
    final public static function Create( string $relativePath, array $pluginData )
    {
        // Optional variables
        $authorURL = '';
        if ( array_key_exists( 'AuthorURI', $pluginData )) {
            $authorURL = $pluginData[ 'AuthorURI' ];
        }
        
        // Create new Plugin instance and return
        return new Models\Plugin(
            $relativePath,
            $pluginData[ 'Network' ],
            $pluginData[ 'Version' ],
            $pluginData[ 'Name' ],
            $pluginData[ 'Description' ],
            $pluginData[ 'Author' ],
            $authorURL
        );
    }
}
