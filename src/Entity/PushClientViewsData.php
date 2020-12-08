<?php

namespace Drupal\message_push_notification\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Push client entities.
 */
class PushClientViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.
    return $data;
  }

}
