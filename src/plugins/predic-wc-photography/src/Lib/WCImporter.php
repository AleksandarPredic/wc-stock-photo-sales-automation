<?php

namespace PredicWCPhoto\Lib;

/**
 * Include dependencies.
 */
if (! class_exists('WC_Product_Importer', false)) {
    include_once WC_ABSPATH . '/includes/import/abstract-wc-product-importer.php';
}

/**
 * Class WCImporter
 *
 * @package PredicWCPhoto\Lib
 */
class WCImporter extends \WC_Product_Importer
{

    /**
     * @var array
     */
    private $data;

    /**
     * Product name
     * @var string
     */
    private $name;

    /**
     * Product sku
     * @var string
     */
    private $sku;

    /**
     * Product short description
     * @var string
     */
    private $shortDescription;

    /**
     * Product description
     * @var string
     */
    private $description;

    /**
     * Array of product prices [regular, extended]
     * @var array
     */
    private $prices;

    /**
     * Product image id
     * @var int
     */
    private $imageId;

    /**
     * Array of metadata [['key' => post_meta_key, 'value' => some value]...]
     * @var array
     */
    private $metadata;

    /**
     * Array of tag ids, non associative array
     * @var array
     */
    private $tagsIds;

    /**
     * Product custom global attribute taxonomy
     * @var string
     */
    private $licenceAttribute = 'Licence';

    /**
     * Product custom global attribute taxonomy term
     * @var string
     */
    private $licenceAttributeRegularTerm = 'Regular';

    /**
     * Product custom global attribute taxonomy term
     * @var string
     */
    private $licenceAttributeExtendedTerm = 'Extended';

    /**
     * Array of custom taxonomies to import [ [taxonomy_id => ['Term name not slug' ...] ] ]
     * @var array
     */
    private $customTaxonomies;

    /**
     * Process set data
     *
     * @throws \Exception
     * @return array Return array of results for each photo [ ['id' => int, 'updated' => bool, 'children' => [int, int]] ... ]
     */
    public function import()
    {
        $parentImport = $this->processData();

        if (is_wp_error($parentImport)) {
            throw new \Exception(
                sprintf(
                    esc_html__('Failed to convert image: %s to product. Error: %s', 'predic-wc-photography'),
                    $this->name,
                    $parentImport->get_error_message()
                ),
                $parentImport->get_error_code()
            );
        }

        $parentId = $parentImport['id'];

        /**
         * Create regular price variation
         */
        $this->data      = $this->setVariationProductData($parentId, $this->prices[0], $this->licenceAttributeRegularTerm);
        $variationResult = $this->processData();

        if (is_wp_error($variationResult)) {
            $code = is_int($variationResult->get_error_code()) ? $variationResult->get_error_code() : null;

            throw new \Exception(
                sprintf(
                    esc_html__('Failed to import variation: %s. Parent Product ID: %s. Error: %s', 'predic-wc-photography'),
                    $this->name,
                    $parentId,
                    $variationResult->get_error_message()
                ),
                $code
            );
        }

        $parentImport['children'][] = $variationResult['id'];

        /**
         * Create extended price variation
         */
        $this->data      = $this->setVariationProductData($parentId, $this->prices[1], $this->licenceAttributeExtendedTerm);
        $variationResult = $this->processData();

        if (is_wp_error($variationResult)) {
            throw new \Exception(
                sprintf(
                    esc_html__('Failed to import variation: %s. Parent Product ID: %d. Error: %s', 'predic-wc-photography'),
                    $this->name,
                    $parentId,
                    $variationResult->get_error_message()
                ),
                $variationResult->get_error_code()
            );
        }

        $parentImport['children'][] = $variationResult['id'];

        /**
         * Add all custom taxonomies here if any
         */
        if (is_array($this->customTaxonomies) && ! empty($this->customTaxonomies)) {
            foreach ($this->customTaxonomies as $taxonomy => $terms) {
                $result = wp_set_object_terms(
                    $parentId,
                    $terms,
                    $taxonomy
                );

                if (is_wp_error($result)) {
                    // TODO: logg this error and maybe add some feedback for the current import
                }
            }
        }

        return $parentImport;
    }

    /**
     * Process data - import data into DB
     * @throws \Exception
     * @return array|\WP_Error
     */
    public function processData()
    {
        return $this->process_item($this->data);
    }

    /**
     * @param string $name             Parsed by filename
     * @param string $sku              Parsed by filename
     * @param string $shortDescription From image metadata
     * @param string $description      From image metadata
     * @param array  $prices           array of prices [regular, extended]
     * @param int    $imageId          Featured image id
     * @param array  $tagsIds          Array of tag ids, non associative array
     * @param array  $metadata         Array of metadata [['key' => post_meta_key, 'value' => some value]...]
     * @param array  $customTaxonomies Array of custom taxonomies to import [ [taxonomy_id => ['Term name not slug' ...] ] ]
     */
    public function setData(string $name, string $sku, string $shortDescription, string $description, array $prices, int $imageId, array $tagsIds, array $metadata, array $customTaxonomies)
    {
        $this->name             = sanitize_text_field($name);
        $this->sku              = sanitize_file_name($sku);
        $this->shortDescription = sanitize_text_field($shortDescription);
        $this->description      = sanitize_text_field($description);
        $this->prices           = array_map('sanitize_text_field', $prices);
        $this->imageId          = intval($imageId);
        $this->tagsIds          = array_map('sanitize_text_field', $tagsIds);
        $this->metadata         = map_deep($metadata, 'sanitize_text_field');
        $this->customTaxonomies = map_deep($customTaxonomies, 'sanitize_text_field');

        $this->data = $this->setVariableProductData();
    }

    /**
     * Set data common for Variation and Variable products
     * @return array
     */
    private function setCommonProductData()
    {
        return [
            'short_description' => sanitize_text_field($this->shortDescription),
            'description'       => sanitize_text_field($this->description),
            'meta_data'         => [
                // TODO: Add trait to get meta data
                [
                    'key'   => 'some_test_metadata',
                    'value' => sanitize_text_field($this->name),
                ],
            ],
        ];
    }

    /**
     * Set data valid only for Variable product
     * @return array
     */
    private function setVariableProductData()
    {
        $data = array_merge(
            $this->setCommonProductData(),
            [
                'sku'                 => sanitize_text_field($this->sku),
                'variation'           => false,
                'name'                => sanitize_text_field($this->name),
                'type'                => 'variable',
                'regular_price'       => '',
                'manage_stock'        => false,
                'stock_quantity'      => null,
                'tag_ids'             => $this->tagsIds,
                'image_id'            => $this->imageId,
                'meta_data'           => $this->metadata,
                // Attributes will be automatically created if it doesn't exists
                'default_attributes' => [$this->licenceAttributeRegularTerm],
                'raw_attributes'     => $this->setVariableProductAttributes(
                    [
                        $this->licenceAttribute => [
                            $this->licenceAttributeRegularTerm,
                            $this->licenceAttributeExtendedTerm
                        ]
                    ]
                ), // XSS WP standards ok
            ]
        );

        /**
         * Filter hook pwcp_wc_importer_variable_data
         */
        return apply_filters('pwcp_wc_importer_variable_data', $data);
    }

    /**
     * Set data valid only for Variation product
     * @return array
     */
    private function setVariationProductData($parentId, $price, $licence)
    {
        return array_merge(
            $this->setCommonProductData(),
            [
                'sku'           => sanitize_file_name(strtolower($this->sku . '-' . $licence)),
                'variation'     => true,
                'name'          => sanitize_text_field($this->name . ' ' . $licence),
                'type'          => 'variation',
                'regular_price' => $price,
                'manage_stock'  => false,
                'downloadable'  => true,
                'parent_id'     => $parentId,
                'image_id'      => '',
                // Attributes will be automatically created if doesn't exists
                'raw_attributes' => $this->setVariableProductAttributes(
                    [
                        'Licence' => [$licence]
                    ]
                ), // XSS WP standards ok
            ]
        );
    }

    /**
     * Set Variable product attributes
     *
     * @param array $data Array [taxonomy => [term, term] ...] or in other words [attribute => [value, value] ...]
     * @return array
     */
    private function setVariableProductAttributes($data = [])
    {
        $raw_attributes = [];

        foreach ($data as $name => $values) {
            $all_values                          = array_unique($values);
            $raw_attributes[ strtolower($name) ] = [
                'name'      => $name,
                'value'     => array_unique($values),
                'default'   => isset($all_values[0]) ? $all_values[0] : null, // Must be set as value
                'visible'   => true,
                'variation' => true,
                'taxonomy'  => $name
            ];
        }

        return $raw_attributes;
    }
}
