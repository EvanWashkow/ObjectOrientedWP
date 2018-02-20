<?php
namespace WordPress\Plugins\Models;

/**
 * Represents a single WordPress plugin
 */
class Plugin extends _Plugin
{
    
    /**
     * This plugin's unique identifier
     *
     * @var string
     */
    private $id;
    
    /**
     * Name of the plugin's author
     *
     * @var string
     */
    private $authorName;
    
    /**
     * Link to the author's website
     *
     * @var string
     */
    private $authorURL;
    
    /**
     * Description of the plugin's purpose
     *
     * @var string
     */
    private $description;
    
    /**
     * User-friendly name for the plugin
     *
     * @var string
     */
    private $name;
    
    /**
    * Path to plugin file, relative to the plugins directory
    *
    * @var string
    */
    private $relativePath;
    
    /**
    * Indicates this is a multi-site plugin: only to be activated on the network
    *
    * @var string
    */
    private $isMultiSite;
    
    /**
     * Plugin version number
     *
     * @var string
     */
    private $version;
    
    
    /**
     * Instantiate a new Plugin instance
     *
     * @param string $relativePath Path to plugin file, relative to the plugins directory
     * @param bool   $isMultiSite  Indicates this is a multi-site plugin: only to be activated on the network
     * @param string $version      Plugin version number
     * @param string $name         User-friendly name for the plugin
     * @param string $description  Description of the plugin's purpose
     * @param string $authorName   Name of the plugin's author
     * @param string $authorURL    Link to the author's website
     */
    final public function __construct( string $relativePath,
                                       bool   $isMultiSite,
                                       string $version,
                                       string $name,
                                       string $description,
                                       string $authorName,
                                       string $authorURL )
    {
        $this->id           = static::ExtractID( $relativePath );
        $this->relativePath = $relativePath;
        $this->isMultiSite  = $isMultiSite;
        $this->version      = $version;
        $this->name         = $name;
        $this->description  = $description;
        $this->authorName   = $authorName;
        $this->authorURL    = $authorURL;
    }
    
    
    final public function getID()
    {
        return $this->id;
    }
    
    final public function getAuthorName()
    {
        return $this->authorName;
    }
    
    final public function getAuthorURL()
    {
        return $this->authorURL;
    }
    
    final public function getDescription()
    {
        return $this->description;
    }
    
    final public function getName()
    {
        return $this->name;
    }
    
    final public function getRelativePath()
    {
        return $this->relativePath;
    }
    
    final public function getVersion()
    {
        return $this->version;
    }
    
    final public function isMultiSite()
    {
        return $this->isMultiSite;
    }
}
