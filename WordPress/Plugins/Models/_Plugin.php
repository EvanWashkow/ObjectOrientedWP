<?php
namespace WordPress\Plugins\Models;

/**
 * Defines the structure for a single plugin
 */
abstract class _Plugin extends \WordPress\Shared\_Model
{
    
    /**
     * The unique identifier for this plugin
     *
     * @var string
     */
    private $id;
    
    /**
     * Mapped array of arbitrary properties
     *
     * @var array
     */
    private $properties;
    
    /**
     * Path to plugin file, relative to the plugins directory
     *
     * @var string
     */
    private $relativePath;
    
    
    /**
     * Create a new plugin instance
     *
     * @param array $properties Mapped array of this plugin's properties
     */
    final public function __construct( string $relativePath, array $properties )
    {
        $this->id           = \WordPress\Plugins::ExtractID( $relativePath );
        $this->relativePath = $relativePath;
        $this->properties   = $properties;
    }
    
    
    /***************************************************************************
    *                                  PROPERTIES
    ***************************************************************************/
    
    /**
     * Retrieve this plugin's ID
     *
     * @return string
     */
    final public function getID()
    {
        return $this->id;
    }
    
    
    /**
    * Retrieves the path to this plugin's file, relative to the plugins directory
    *
    * @return string
    */
    final public function getRelativePath()
    {
        return $this->relativePath;
    }
    
    
    /**
     * Retrieve a property for this plugin
     *
     * @param string $key          The property key
     * @param mixed  $defaultValue The property's default value
     * @return mixed The property value
     */
    final public function get( string $key, $defaultValue = '' )
    {
        $value = $defaultValue;
        if ( array_key_exists( $key, $this->properties )) {
            $value = $this->properties[ $key ];
        }
        return $value;
    }
    
    
    /***************************************************************************
    *                                  ABSTRACT
    ***************************************************************************/
    
    /**
     * Retrieves this plugin's author name
     *
     * @return string
     */
    abstract public function getAuthorName();
    
    /**
     * Retrieves this plugin author's website
     *
     * @return string
     */
    abstract public function getAuthorURL();
    
    /**
     * Retrieves the description of this plugin's purpose
     *
     * @return string
     */
    abstract public function getDescription();
    
    /**
     * Retrieves the user-friendly name for this plugin's
     *
     * @return string
     */
    abstract public function getName();
    
    /**
     * Retrieves this plugin's version number
     *
     * @return string
     */
    abstract public function getVersion();
    
    /**
     * Indicates this plugin requires global activation on all sites
     *
     * @return bool
     */
    abstract public function requiresGlobalActivation();
    
    
    /***************************************************************************
    *                                 ACTIVATING
    ***************************************************************************/
    
    /**
     * Activate the plugin
     *
     * @return bool True if the plugin is active
     */
    abstract public function activate();
    
    /**
     * Can the plugin be activated?
     *
     * @return bool
     */
    abstract public function canActivate();
    
    /**
     * Deactivate the plugin
     *
     * @return bool True if the plugin is no longer active
     */
    abstract public function deactivate();
    
    /**
     * Is the plugin activated?
     *
     * @return bool
     */
    abstract public function isActive();
}
