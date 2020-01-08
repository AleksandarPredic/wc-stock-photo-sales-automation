<?php

namespace PredicWCPhoto\Lib;

/**
 * Include dependencies.
 */
if (! class_exists('WC_Product_Importer', false)) {
    include_once WC_ABSPATH . '/includes/import/abstract-wc-product-importer.php';
}

class WCImporter extends \WC_Product_Importer
{

    /**
     * @var array
     */
    private $data;

    private $name;
    private $sku;
    private $shortDescription;
    private $description;
    private $prices;
    /**
     * @var int
     */
    private $imageId;

    /**
     * @var array
     */
    private $metadata;

    /**
     * @inheritDoc
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

        // Create regular price variation
        // TODO: Set licence values on one place
        $this->data      = $this->setVariationProductData($parentId, $this->prices[0], 'Regular');
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

        // Create extended price variation
        $this->data      = $this->setVariationProductData($parentId, $this->prices[1], 'Extended');
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
         * array(3) {
         * ["id"]=>
         * int(143)
         * ["updated"]=>
         * bool(true)
         * ["children"]=>
         * array(2) {
         * [0]=>
         * int(144)
         * [1]=>
         * int(145)
         * }
         * }
         */
        return $parentImport;
    }

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
     * @param array  $metadata         array of metadata [['key' => post_meta_key, 'value' => some value]]
     */
    public function setData($name, $sku, $shortDescription, $description, $prices, $imageId, $metadata)
    {
        $this->name             = sanitize_text_field($name);
        $this->sku              = sanitize_file_name($sku);
        $this->shortDescription = sanitize_text_field($shortDescription);
        $this->description      = sanitize_text_field($description);
        $this->prices           = array_map('sanitize_text_field', $prices);
        $this->imageId          = intval($imageId);
        $this->metadata         = $metadata;

        $this->data = $this->setVariableProductData();
    }

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

    private function setVariableProductData()
    {
        return array_merge(
            $this->setCommonProductData(),
            [
                'sku'            => sanitize_text_field($this->sku),
                'variation'      => false,
                'name'           => sanitize_text_field($this->name),
                'type'           => 'variable',
                'regular_price'  => '',
                'manage_stock'   => false,
                'stock_quantity' => null,
                'image_id'       => $this->imageId,
                'meta_data'      => $this->metadata,
                // Attributes will be automatically created if doesn't exists
                'raw_attributes' => $this->setVariableProductAttributes(
                    [
                        'Licence' => ['Regular', 'Extended']
                    ]
                ), // XSS WP standards ok
            ]
        );
    }

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
