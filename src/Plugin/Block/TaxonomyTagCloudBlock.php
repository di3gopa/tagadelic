<?php

namespace Drupal\tagadelic\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\tagadelic\Lib\TagadelicCloud;
use Drupal\tagadelic\Lib\TagadelicTag;

use Drupal\Core\Config\Config;

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
    // TODO: Make number of tags configurable on admin interface.
    $cloud = $this->getCloud(12);

    return array(
      '#theme' => 'tagadelic_taxonomy_cloud',
      '#tags' => $cloud,
    );
  }

  /**
   * @param $max_amount
   * @return mixed
   */
  private function getCloud($max_amount) {
    $cloud = new TagadelicCloud("tagadalic_taxonomy");

    foreach ($this->getTagsFromDb($max_amount) as $term) {
      $tag = new TagadelicTag($term->tid, $term->name, $term->count);
      $tag->set_link("taxonomy/term/{$term->tid}");

      $cloud->add_tag($tag);
    }

    # Because now here wer're returning an array, not HTML.
    return $cloud->get_tags();
  }

  /**
   * @param $max_amount
   * @return \Drupal\Core\Database\StatementInterface|int|null
   */
  private function getTagsFromDb($max_amount) {
    $tags = array();
    $config = \Drupal::config('tagadelic.settings');

    // TODO: Change db select for a db connection from the container.
    $query = db_select('taxonomy_index', 'i');
    $alias = $query->leftjoin('taxonomy_term_field_data', 't', '%alias.tid = i.tid');
    $query->addExpression('COUNT(i.nid)', 'count');
    $query->addField($alias, 'tid');
    $query->addField($alias, 'name');
    $query->orderBy('count', 'DESC');

    foreach($config->get('tagadelic.vocabularies') as $vid => $state) {
      if ($state != $vid) { //Disabled
        $query->condition('t.vid', $vid, '<>');
      }
    }

    $query->range(0, $max_amount)
      ->groupBy("t.tid, t.name");

    return $query->execute();
  }
}