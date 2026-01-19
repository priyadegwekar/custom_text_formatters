<?php

namespace Drupal\custom_text_formatters\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\custom_text_formatters\Service\SlugifyService;

/**
 * Slugify text field formatter.
 *
 * @FieldFormatter(
 *   id = "custom_slugify_text",
 *   label = @Translation("Slugify"),
 *   field_types = {
 *     "string",
 *     "string_long",
 *     "text",
 *     "text_long",
 *     "text_with_summary"
 *   }
 * )
 */
class SlugifyFormatter extends FormatterBase {

  /**
   * The slugify service.
   *
   * @var \Drupal\custom_text_formatters\Service\SlugifyService
   */
  protected SlugifyService $slugifyService;

  /**
   * Constructs a SlugifyFormatter object.
   *
   * @param string $plugin_id
   *   The plugin ID.
   * @param mixed $plugin_definition
   *   The plugin definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The field definition.
   * @param array $settings
   *   The formatter settings.
   * @param string $label
   *   The field label.
   * @param string $view_mode
   *   The view mode.
   * @param array $third_party_settings
   *   Third-party settings.
   * @param \Drupal\custom_text_formatters\Service\SlugifyService $slugify_service
   *   The slugify service.
   */
  public function __construct(
    string $plugin_id,
    $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    string $label,
    string $view_mode,
    array $third_party_settings,
    SlugifyService $slugify_service
  ) {
    parent::__construct(
      $plugin_id,
      $plugin_definition,
      $field_definition,
      $settings,
      $label,
      $view_mode,
      $third_party_settings
    );

    $this->slugifyService = $slugify_service;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('custom_text_formatters.slugify')
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'separator' => '-',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    return [
      'separator' => [
        '#type' => 'textfield',
        '#title' => $this->t('Separator'),
        '#default_value' => $this->getSetting('separator'),
        '#size' => 5,
        '#description' => $this->t('Character used to separate words in the slug.'),
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    return [
      $this->t('Separator: @separator', [
        '@separator' => $this->getSetting('separator'),
      ]),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    $separator = $this->getSetting('separator');

    foreach ($items as $delta => $item) {
      $elements[$delta] = [
        '#markup' => $this->slugifyService->slugify($item->value ?? '', $separator),
      ];
    }

    return $elements;
  }

}
