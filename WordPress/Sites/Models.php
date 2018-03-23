<?php
namespace WordPress\Sites;

/**
 * Creates new Site instances, following the factory pattern
 */
class Models
{
    /**
     * Instantiate and return new Site
     *
     * @param int $id The site (blog) id
     * @return Models\Site
     */
    final public static function Create( int $id ): Models\Site
    {
        return new Models\Site( $id );
    }
}
