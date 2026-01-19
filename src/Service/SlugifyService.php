<?php

namespace Drupal\custom_text_formatters\Service;

use Cocur\Slugify\Slugify;

/**
 * Slugify service that converts text into slugs.
 */
class SlugifyService {

  protected $slugify;

  public function __construct() {
    $this->slugify = new Slugify();
  }

  public function slugify(string $text, string $separator = '-'): string {
    return $this->slugify->slugify($text, $separator);
  }

}
