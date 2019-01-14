<?php

/**
 * @file
 * Contains \Drupal\cludo_search\Plugin\Block\CludoSearchBlock.
 */

namespace Drupal\cludo_search\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormInterface;

/**
 * Provides a cludo_search block for the search form.
 *
 * @Block(
 *   id = "cludo_search",
 *   admin_label = @Translation("Cludo Search block"),
 *   category = @Translation("Cludo Search")
 * )
 */
class CludoSearchBlock extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
    $form = \Drupal::formBuilder()->getForm('Drupal\cludo_search\Form\CludoSearchBlockForm');
    return $form;
  }
}
