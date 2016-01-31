<?php

namespace Drupal\tagadelic\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a Tag Cloud block based on module's settings.
 *
 * @Block(
 *   id = "tagadelic_taxonomy_tagcloud",
 *   admin_label = @Translation("Tagadelic: Taxonomy Tag Cloud")
 * )
 */
class TaxonomyTagCloudBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return 'Tag cloud';
  }
}