<?php

namespace Drupal\cludo_search\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class SettingsForm.
 *
 * @package Drupal\cludo_search\Form
 */
class SettingsForm extends ConfigFormBase {

  /**
   * The config factory interface.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs a \Drupal\aggregator\SettingsForm object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    parent::__construct($config_factory);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['cludo_search.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'cludo_search_config_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Grab current settings.
    $settings = _cludo_search_get_settings();

    // Connection information.
    $form['connection_info'] = [
      '#title' => $this->t('Connection Information'),
      '#type' => 'fieldset',
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
    ];

    $form['connection_info']['customerId'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Customer ID'),
      '#description' => $this->t('Cludo Search customerId value.'),
      '#default_value' => $settings['customerId'],
      '#required' => TRUE,
    ];

    $form['connection_info']['engineId'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Engine ID'),
      '#description' => $this->t('Cludo Search engineId value.'),
      '#default_value' => $settings['engineId'],
      '#required' => TRUE,
    ];

    $form['connection_info']['search_page'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Search page path'),
      '#description' => $this->t('Cludo search page.'),
      '#default_value' => $settings['search_page'],
      '#required' => TRUE,
    ];

    $form['additional_customisations'] = [
      '#title' => $this->t('Additional Customisations'),
      '#type' => 'fieldset',
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
    ];

    $form['additional_customisations']['disable_autocomplete'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Disable Autocomplete'),
      '#description' => $this->t('This will disable autocomplete on the search form'),
      '#default_value' => $settings['disable_autocomplete'],
    ];

    $form['additional_customisations']['hide_results_count'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Hide Results Count'),
      '#description' => $this->t('This will hide the number of results from displaying.'),
      '#default_value' => $settings['hide_results_count'],
    ];

    $form['additional_customisations']['hide_did_you_mean'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Hide the "Did you mean..." suggestions'),
      '#description' => $this->t('This will stop the suggestions from showing'),
      '#default_value' => $settings['hide_did_you_mean'],
    ];

    $form['additional_customisations']['hide_search_filters'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Hide search filters (Overlay Implementation only)'),
      '#description' => $this->t('Hides the search filters.'),
      '#default_value' => $settings['hide_search_filters'],
    ];

    $form['additional_customisations']['global_filter_category'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Global Filter Category'),
      '#description' => $this->t('All results on this site will be filtered by this category. This is useful if the engine is used on multiple sites and subdomains. Leave blank to include all results. The end user cannot remove this filter.'),
      '#default_value' => $settings['global_filter_category'],
    ];

    $form['additional_customisations']['whitelist_categories'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Whitelist Filters'),
      '#description' => $this->t('If populated, only whitelisted filters will appear after search results. Leave blank to include all filters. This will not override the ability to hide filters. One filter (or category) per line.'),
      '#default_value' => $settings['whitelist_categories'],
    ];

    $form['cludo_tabs'] = [
      '#title' => $this->t('Tab Settings'),
      '#type' => 'fieldset',
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
      '#description' => $this->t('Leaving the tab fields blank will produce the default behavior, meaning search results will appear without additional filtering. Adding tab information, which is driven by categories, will cause search results to default to the first cateogry. Tabs are provided via block to switch between the tabbed results. The first category can be "All" to get all results.'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->configFactory->getEditable('cludo_search.settings');
    $values = $form_state->getValues();
    $field_keys = _cludo_search_get_field_keys();
    foreach ($field_keys as $field) {
      $config->set($field, trim($values[$field]));
    }
    $config->save();

    // Refresh settings getter.
    _cludo_search_get_settings(TRUE);

    // Make the 'search_title' setting change take effect right away.
    \Drupal::service('router.builder')->rebuild();

    parent::submitForm($form, $form_state);
  }

}
