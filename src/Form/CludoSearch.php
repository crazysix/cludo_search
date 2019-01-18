<?php

namespace Drupal\cludo_search\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

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
    return 'cludo_search_search_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    // Get settings.
    $settings = _cludo_search_get_settings();

    // Build form.
    $prompt = $this->t('Enter the terms you wish to search for.');
    $query = '';

    // Basic search.
    $form['basic'] = [
      '#type' => 'container',
    ];
    $form['basic']['search_keys'] = [
      '#type' => 'textfield',
      '#default_value' => $query,
      '#attributes' => [
        'title' => $prompt,
        'autocomplete' => 'off',
      ],
      '#title' => $prompt,
      '#title_display' => 'before',
    ];

    // Only prompt if we haven't searched yet.
    if ($query == '') {
      $form['basic']['prompt'] = [
        '#type' => 'item',
        '#markup' => '<p><b>' . $prompt . '</b></p>',
      ];
    }

    $form['basic']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Search'),
    ];

    // Set tab info for the template.
    $tab_info = $settings['tab_info'];
    // Clear incomplete tabs.
    if (!empty($tab_info)) {
      foreach ($tab_info as $key => $tab) {
        if (empty($tab['name']) || empty($tab['category'])) {
          unset($tab_info[$key]);
        }
      }
    }
    $form['#tab_info'] = $tab_info;

    // This may not be needed.
    $form['#action'] = '';

    // Use core search CSS in addition to this module's css
    // (keep it general in case core search is enabled)
    $form['#attributes']['class'][] = 'search-form';
    $form['#attributes']['class'][] = 'search-cludo-search-search-form';

    // Add JS and CSS.
    $form['#attached']['library'][] = 'cludo_search/cludo-customer';

    // Define variables and add to JS.
    $disable_autocomplete = $settings['disable_autocomplete'] ? TRUE : FALSE;
    $hide_results = $settings['hide_results_count'] ? TRUE : FALSE;
    $hide_did_you_mean = $settings['hide_did_you_mean'] ? TRUE : FALSE;
    $hide_search_filters = $settings['hide_search_filters'] ? TRUE : FALSE;
    global $base_url;
    $search_url = $base_url . DIRECTORY_SEPARATOR . $settings['search_page'];
    $global_filter = [];
    if (!empty($settings['global_filter_category'])) {
      $global_filter = preg_split('/[\n\r]+/', $settings['global_filter_category']);
      foreach ($global_filter as $key => $facet) {
        $global_filter[$key] = trim($facet);
        if (empty($global_filter[$key])) {
          unset($global_filter[$key]);
        }
      }
    }
    $whitelist = [];
    if (!empty($settings['whitelist_categories'])) {
      $whitelist = preg_split('/[\n\r]+/', $settings['whitelist_categories']);
      foreach ($whitelist as $key => $facet) {
        $whitelist[$key] = trim($facet);
        if (empty($whitelist[$key])) {
          unset($whitelist[$key]);
        }
      }
    }
    $first_tab = reset($tab_info);
    $init_facet = empty($first_tab['category']) || $first_tab['category'] == 'All' ? NULL : $first_tab['category'];
    $form['#attached']['drupalSettings']['cludo_search']['cludo_searchJS'] = [
      'customerId' => $settings['customerId'],
      'engineId' => $settings['engineId'],
      'searchUrl' => $search_url,
      'disableAutocomplete' => $disable_autocomplete,
      'hideResultsCount' => $hide_results,
      'hideSearchDidYouMean' => $hide_did_you_mean,
      'hideSearchFilters' => $hide_search_filters,
      'filters' => empty($global_filter) ? NULL : (object) ["Category" => $global_filter],
      'whitelistFilters' => $whitelist,
      'initFacets' => empty($init_facet) ? NULL : (object) ["Category" => $init_facet],
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
  }

}
