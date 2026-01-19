<?php

namespace Drupal\custom_text_formatters\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\custom_text_formatters\Service\SlugifyService;

/**
 * Slugify text field formatter.
 *
 * @FieldFormatter(
 *   id = "custom_slugify_text",
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

  protected $slugifyService;

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->slugifyService = $container->get('custom_text_formatters.slugify');
    return $instance;
  }

  public static function defaultSettings() {
    return [
      'separator' => '-',
    ] + parent::defaultSettings();
  }

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

  public function settingsSummary() {
    return [
      $this->t('Separator: @separator', [
        '@separator' => $this->getSetting('separator'),
      ]),
    ];
  }

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
