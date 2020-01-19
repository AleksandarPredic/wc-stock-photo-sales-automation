<?php

namespace PredicWCPhoto\Controllers;

use PredicWCPhoto\Contracts\ControllerInterface;
use PredicWCPhoto\Traits\SingletonTrait;

/**
 * Class MediaLibraryController
 *
 * @package PredicWCPhoto\Controllers
 */
class MediaLibraryController implements ControllerInterface
{
    use SingletonTrait;

    /**
     * Disable media library filter
     * @var bool
     */
    private $hideProductImages = true;

    /**
     * MediaLibraryController constructor.
     */
    private function __construct()
    {
    }

    /**
     * Add hooks
     */
    public function init()
    {
        if (! $this->hideProductImages) {
            return;
        }
        add_filter('ajax_query_attachments_args', [$this, 'filters']);
    }

    /**
     * Add where clause
     * @param string $where
     * @return string
     */
    public function where($where)
    {
        global $wpdb;
        $where .= $wpdb->prepare(" AND my_post_parent.post_type IS NULL ");

        return $where;
    }

    /**
     * Add join clause
     * @param string $join
     * @return string
     */
    public function join($join)
    {
        global $wpdb;
        $join .= " LEFT JOIN {$wpdb->postmeta} as my_post_parent_meta ON ({$wpdb->posts}.ID = my_post_parent_meta.meta_value AND my_post_parent_meta.meta_key = '_thumbnail_id') ";
        $join .= " LEFT JOIN {$wpdb->posts} as my_post_parent ON (my_post_parent_meta.post_id = my_post_parent.ID) ";

        return $join;
    }

    /**
     * Filter media library and hide product images
     * @param array $query An array of query variables.
     * @return mixed
     */
    public function filters($query)
    {
        add_filter('posts_where', [$this, 'where']);
        add_filter('posts_join', [$this, 'join']);

        return $query;
    }
}
