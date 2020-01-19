<?php

namespace PredicStorefront\WooCommerce;

/**
 * Class TaxonmyFromPLugin responsible for using the taxonomies registerd in plugin
 *
 * @package PredicStorefront\WooCommerce
 */
abstract class TaxonmyFromPLugin
{
    /**
     * Product ids to display
     * @var array
     */
    protected static $productsIds = [];

    /**
     * Product terms as array of ids
     * @var array
     */
    protected static $productsTerms = [];

    /**
     * Taxonomy id
     * @var string
     */
    protected $taxonomy;

    /**
     * How many posts to show
     * @var int
     */
    protected $postsPerPage;

    /**
     * Path to the template part to use
     * @var string
     */
    protected $templatePart;

    /**
     * If all dependencies are ok as this depend from other plugin that register taxonomies
     * @var bool
     */
    protected $loadedDependecies;

    /**
     * TaxonomyModels constructor.
     */
    public function __construct()
    {
        $this->postsPerPage      = -1;
        $this->loadedDependecies = true;
    }

    /**
     * Display products on single post page related to tags that product have for this taxonomy
     */
    public function render()
    {
        if (! $this->loadedDependecies) {
            return;
        }

        $terms = wp_get_post_terms(get_the_ID(), $this->taxonomy, ['fields' => 'ids']);

        if (empty($terms) || is_wp_error($terms) || ! is_array($terms)) {
            return;
        }

        self::$productsTerms = $terms;

        $args = [
            'post_type'      => 'product',
            'post_status'    => 'publish',
            'nopaging'       => true,
            'fields'         => 'ids',
            'posts_per_page' => $this->postsPerPage,
            'tax_query'      => [
                [
                    'taxonomy' => $this->taxonomy,
                    'terms'    => $terms,
                    'field'    => 'term_id',
                ]
            ],
        ];

        $query = new \WP_Query($args);

        if (! $query->have_posts()) {
            return;
        }

        self::$productsIds = $query->posts;

        get_template_part($this->templatePart);
    }

    /**
     * Return product ids as array to display
     * @return array
     */
    public function getProductsIds()
    {
        return self::$productsIds;
    }

    /**
     * Return product terms as array of ids
     * @return array
     */
    public function getProductTerms()
    {
        return self::$productsTerms;
    }
}
