<?php

namespace Drupal\cludo_search\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class CludoSearch.
 *
 * @package Drupal\cludo_search\Form
 */
class CludoSearch extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'field_group_add_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    // Build form.
    $prompt = ($prompt == '') ? t('Enter the terms you wish to search for.') : $prompt;

    // Basic search.
    $form['basic'] = array(
      '#type' => 'container',
    );
    $form['basic']['search_keys'] = array(
      '#type' => 'textfield',
      '#default_value' => $query,
      '#attributes' => array(
        'title' => $prompt,
        'autocomplete' => 'off',
      ),
      '#title' => $prompt,
      '#title_display' => 'before',
    );

    // Only prompt if we haven't searched yet.
    if ($query == '') {
      $form['basic']['prompt'] = array(
        '#type' => 'item',
        '#markup' => '<p><b>' . $prompt . '</b></p>',
      );
    }

    $form['basic']['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Search'),
    );

    // Submit points to search page without any keys (pre-search state)
    // the redirect happens in _submit handler
    // $form_state['action'] = 'csearch/';
    // This one impacts the form: $form['#action'] = '4';
    // $form['#action'] = 'csearch/';.
    $form['#action'] = '';

    // Use core search CSS in addition to this module's css
    // (keep it general in case core search is enabled)
    $form['#attributes']['class'][] = 'search-form';
    $form['#attributes']['class'][] = 'search-cludo-search-search-form';

    // Add JS and CSS.
    $form['#attached']['library'][] = 'cludo_search/cludo-customer';

    // Define variables and add to JS.
    $settings = _cludo_search_get_settings();
    $disable_autocomplete = $settings['disable_autocomplete'] ? 'true' : 'false';
    $hide_results = $settings['hide_results_count'] ? 'true' : 'false';
    $hide_did_you_mean = $settings['hide_did_you_mean'] ? 'true' : 'false';
    $hide_search_filters = $settings['hide_search_filters'] ? 'true' : 'false';
    global $base_url;
    $search_url = $base_url . DIRECTORY_SEPARATOR . $settings['search_page'];
    $form['#attached']['drupalSettings']['cludo_search']['cludo_searchJS'] = [
      'customerId' => $settings['customerId'],
      'engineId' => $settings['engineId'],
      'searchUrl' => $search_url,
      'disableAutocomplete' => $disable_autocomplete,
      'hideResultsCount' => $hide_results,
      'hideSearchDidYouMean' => $hide_did_you_mean,
      'hideSearchFilters' => $hide_search_filters,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Set the redirect.
    $search_query = urlencode($form_state->getValue('search_keys'));

    // Dirty change to get rid of +.
    $search_query = str_replace('+', '%20', $search_query);

    //$form_state['redirect'] = url($form_state['action'] . $search_query, array('absolute' => TRUE));
    //$data = "submit info: " . $search_query . " \n redirect: " . $form_state['redirect'] . "\n";
  }
}
