<?php

namespace Drupal\message_push_notification;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Push client entity.
 *
 * @see \Drupal\message_push_notification\Entity\PushClient.
 */
class PushClientAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\message_push_notification\Entity\PushClientInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished push client entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published push client entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit push client entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete push client entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add push client entities');
  }

}
