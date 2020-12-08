<?php

namespace Drupal\message_push_notification\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Push client entities.
 *
 * @ingroup message_push_notification
 */
interface PushClientInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Push client name.
   *
   * @return string
   *   Name of the Push client.
   */
  public function getName();

  /**
   * Sets the Push client name.
   *
   * @param string $name
   *   The Push client name.
   *
   * @return \Drupal\message_push_notification\Entity\PushClientInterface
   *   The called Push client entity.
   */
  public function setName($name);

  /**
   * Gets the Push client creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Push client.
   */
  public function getCreatedTime();

  /**
   * Sets the Push client creation timestamp.
   *
   * @param int $timestamp
   *   The Push client creation timestamp.
   *
   * @return \Drupal\message_push_notification\Entity\PushClientInterface
   *   The called Push client entity.
   */
  public function setCreatedTime($timestamp);

}
