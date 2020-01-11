<?php

namespace PredicWCPhoto\Lib;

use PredicWCPhoto\Contracts\ImporterTermInterface;
use PredicWCPhoto\Traits\SingletonTrait;

/**
 * Class ImporterTerm
 *
 * @package PredicWCPhoto\Lib
 */
class ImporterTerm implements ImporterTermInterface
{
    use SingletonTrait;

    /**
     * Import single term if does not exists
     * @param string $name
     * @param string $taxonomy
     * @throws \Exception
     * @return false|int
     */
    public function import($name, $taxonomy)
    {
        $name     = sanitize_text_field($name);
        $slug     = strtolower(sanitize_file_name(str_replace('_', '-', $name)));
        $taxonomy = sanitize_text_field($taxonomy);

        $termId = $this->termExists($slug, $taxonomy);

        if ($termId) {
            return $termId;
        }

        $termId = $this->save($name, $slug, $taxonomy);

        return $termId;
    }

    /**
     * Check if term exists
     * @param string $slug
     * @param string $taxonomy
     * @return int|false Returns the term ID or false if term does not exists
     */
    private function termExists($slug, $taxonomy)
    {
        $term = get_term_by('slug', $slug, $taxonomy, 'OBJECT');

        // If exist, continue
        if (false !== $term && isset($term->term_id)) {
            return intval($term->term_id);
        }

        return false;
    }

    /**
     * Return The Term ID and Term Taxonomy ID. (Example: array('term_id'=>12,'term_taxonomy_id'=>34)) or WP_Error
     * @param string $name
     * @param string $slug
     * @param string $taxonomy
     * @param string $parentSlug
     * @throws \Exception
     * @return int
     */
    private function save($name, $slug, $taxonomy, $parentSlug = '')
    {
        $parentId = 0;
        if ($parentSlug) {
            $parentId = $this->get_parent_id();
        }

        $term = wp_insert_term($name, $taxonomy, [
               'parent' => $parentId,
               'slug'   => $slug
           ]
        );

        if (is_wp_error($term)) {
            throw new \Exception(
                sprintf(
                    esc_html__('Error importing term %1$s with slug %2$s. Error message: %3$s.'),
                    $name,
                    $slug,
                    $term->get_error_message()
                )
            );
        }

        return $term['term_id'];
    }

    /**
     * Return parent term id or 0 if does not exists
     * @param string $parentSlug
     * @param string $taxonomy
     * @return int
     */
    private function getParentId($parentSlug, $taxonomy)
    {
        $parentId = 0;

        // Parent categories will have empty parent slug
        if (empty($parentSlug)) {
            return $parentId;
        }

        $termId =  $this->termExists($parentSlug, $taxonomy);

        return $termId ? $termId : 0;
    }
}
