<?php

namespace PredicWCPhoto\Lib;

use PredicWCPhoto\Traits\SingletonTrait;

/**
 * Class WCTaxonomies
 *
 * @package PredicWCPhoto\Lib
 */
class WCTaxonomies implements WCTaxonomiesInterface
{
    use SingletonTrait;

    /**
     * WCTaxonomies constructor.
     */
    private function __construct()
    {
    }

    /**
     * Add hooks
     */
    public function init()
    {
        add_action('init', [$this, 'register']);
    }

    /**
     * Register taxonomies
     */
    public function register()
    {
        $this->registerShootouts();
        $this->registerModels();
    }

    /**
     * Register models taxonomy
     */
    private function registerModels()
    {
        // Add new taxonomy, NOT hierarchical (like tags)
        $labels = [
            'name'                       => esc_html_x('Models', 'taxonomy general name', 'predic-wc-photography'),
            'singular_name'              => esc_html_x('Model', 'taxonomy singular name', 'predic-wc-photography'),
            'search_items'               => esc_html__('Search Models', 'predic-wc-photography'),
            'popular_items'              => esc_html__('Popular Models', 'predic-wc-photography'),
            'all_items'                  => esc_html__('All Models', 'predic-wc-photography'),
            'parent_item'                => null,
            'parent_item_colon'          => null,
            'edit_item'                  => esc_html__('Edit Model', 'predic-wc-photography'),
            'update_item'                => esc_html__('Update Model', 'predic-wc-photography'),
            'add_new_item'               => esc_html__('Add New Model', 'predic-wc-photography'),
            'new_item_name'              => esc_html__('New Model Name', 'predic-wc-photography'),
            'separate_items_with_commas' => esc_html__('Separate Models with commas', 'predic-wc-photography'),
            'add_or_remove_items'        => esc_html__('Add or remove Models', 'predic-wc-photography'),
            'choose_from_most_used'      => esc_html__('Choose from the most used Models', 'predic-wc-photography'),
            'not_found'                  => esc_html__('No Models found.', 'predic-wc-photography'),
            'menu_name'                  => esc_html__('Models', 'predic-wc-photography'),
        ];

        $args = [
            'hierarchical'          => false,
            'labels'                => $labels,
            'show_ui'               => true,
            'show_admin_column'     => true,
            'update_count_callback' => '_update_post_term_count',
            'query_var'             => true,
            'rewrite'               => [ 'slug' => 'photo-model' ],
        ];

        register_taxonomy(self::MODELS_ID, 'product', $args);
    }

    /**
     * Register Shootout taxonomy
     */
    private function registerShootouts()
    {
        // Add new taxonomy, NOT hierarchical (like tags)
        $labels = [
            'name'                       => esc_html_x('Shootouts', 'taxonomy general name', 'predic-wc-photography'),
            'singular_name'              => esc_html_x('Shootout', 'taxonomy singular name', 'predic-wc-photography'),
            'search_items'               => esc_html__('Search Shootouts', 'predic-wc-photography'),
            'popular_items'              => esc_html__('Popular Shootouts', 'predic-wc-photography'),
            'all_items'                  => esc_html__('All Shootouts', 'predic-wc-photography'),
            'parent_item'                => null,
            'parent_item_colon'          => null,
            'edit_item'                  => esc_html__('Edit Shootout', 'predic-wc-photography'),
            'update_item'                => esc_html__('Update Shootout', 'predic-wc-photography'),
            'add_new_item'               => esc_html__('Add New Shootout', 'predic-wc-photography'),
            'new_item_name'              => esc_html__('New Shootout Name', 'predic-wc-photography'),
            'separate_items_with_commas' => esc_html__('Separate Shootouts with commas', 'predic-wc-photography'),
            'add_or_remove_items'        => esc_html__('Add or remove Shootouts', 'predic-wc-photography'),
            'choose_from_most_used'      => esc_html__('Choose from the most used Shootouts', 'predic-wc-photography'),
            'not_found'                  => esc_html__('No Shootouts found.', 'predic-wc-photography'),
            'menu_name'                  => esc_html__('Shootouts', 'predic-wc-photography'),
        ];

        $args = [
            'hierarchical'          => false,
            'labels'                => $labels,
            'show_ui'               => true,
            'show_admin_column'     => true,
            'update_count_callback' => '_update_post_term_count',
            'query_var'             => true,
            'rewrite'               => [ 'slug' => 'photo-shootout' ],
        ];

        register_taxonomy(self::SHOOTOUTS_ID, 'product', $args);
    }
}
