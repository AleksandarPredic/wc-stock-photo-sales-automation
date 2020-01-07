<?php

namespace PredicWCPhoto\Lib;

/**
 * Include dependencies.
 */
if ( ! class_exists( 'WC_Product_Importer', false ) ) {
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
     * @inheritDoc
     */
    public function import()
    {
		$parentImport = $this->processData();

		if ( is_wp_error( $parentImport ) ) {
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
		$this->data = $this->setVariationProductData($parentId, $this->prices[0], 'Regular');
		$variationResult = $this->processData();

		if ( is_wp_error( $variationResult ) ) {
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


		// Create extended price variation
		$this->data = $this->setVariationProductData($parentId, $this->prices[0], 'Extended');
		$variationResult = $this->processData();

		if ( is_wp_error( $variationResult ) ) {
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

		return $parentImport;
    }

    public function processData()
	{
		return $this->process_item( $this->data );
	}

	/**
	 * @param       $name
	 * @param       $sku
	 * @param       $shortDescription
	 * @param       $description
	 * @param array $prices
	 * @param       $imgaeId
	 */
	public function setData($name, $sku, $shortDescription, $description, $prices, $imgaeId )
	{
		$this->name = sanitize_text_field($name);
		$this->sku = sanitize_file_name($sku);
		$this->shortDescription = sanitize_text_field($shortDescription);
		$this->description = sanitize_text_field($description);
		$this->prices = array_map('sanitize_text_field', $prices);
		$this->imageId = intval($imgaeId);

		$this->data = $this->setVariableProductData();

	}

	private function setCommonProductData()
	{
		return array(
			'short_description' => sanitize_text_field( $this->shortDescription ),
			'description' => sanitize_text_field( $this->description ),
			'meta_data' => array(
				// TODO: Add trait to get meta data
				array(
					'key' => 'some_test_metadata',
					'value' => sanitize_text_field( $this->name ),
				),
			),
		);
	}

	private function setVariableProductData()
	{
		return array_merge(
			$this->setCommonProductData(),
			array(
				'sku' => sanitize_text_field( $this->sku ),
				'variation' => false,
				'name' => sanitize_text_field( $this->name ),
				'type' => 'variable',
				'regular_price' => '',
				'manage_stock' => false,
				'stock_quantity' => null,
				'image_id' => $this->imageId,
				// Attributes will be automatically created if doesn't exists
				'raw_attributes' => $this->setVariableProductAttributes(
					[
						'Licence' => ['Extended', 'Regular']
					]
				), /* XSS WP standards ok */
			)
		);
	}

	private function setVariationProductData($parentId, $price, $licence)
	{
		return array_merge(
			$this->setCommonProductData(),
			array(
				'sku' => sanitize_file_name( strtolower($this->sku . '-' . $licence) ) ,
				'variation' => true,
				'name' => sanitize_text_field( $this->name . ' ' . $licence ),
				'type' => 'variation',
				'regular_price' => $price,
				'manage_stock' => false,
				'downloadable' => true,
				'parent_id' => $parentId,
				'image_id' => '',
				// Attributes will be automatically created if doesn't exists
				'raw_attributes' => $this->setVariableProductAttributes(
					[
						'Licence' => [$licence]
					]
				), /* XSS WP standards ok */
			)
		);
	}

	private function setVariableProductAttributes( $data = array() ) {

		$raw_attributes = array();

		foreach ( $data as $name => $values ) {

			$all_values = array_unique( $values );
			$raw_attributes[ strtolower( $name ) ] = array(
				'name' => $name,
				'value' => array_unique( $values ),
				'default' => isset( $all_values[0] ) ? $all_values[0] : null, // Must be set as value
				'visible' => true,
				'variation' => true,
				'taxonomy' => $name
			);
		}

		return $raw_attributes;

	}

}
