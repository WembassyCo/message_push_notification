<?php

namespace Drupal\message_push_notification\Plugin\Notifier;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\message\MessageInterface;
use Drupal\message_notify\Exception\MessageNotifyException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\message_push_notification\Entity\PushClient;

/**
 * Push notifier.
 *
 * @Notifier(
 *  id = "push",
 *  title = @Translation("Push Notificaiton"),
 *  description = @Translation("Send push notifications."),
 *  viewModes = {
 *     "push_notification"
 *  }
 * )
 */
class Push extends MessageNotifierBase {

  /**
   * the push notifier service.
   */
  protected $pushManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, LoggerChannelINterface $logger, EntityTypeManagerInterface $entity_type_manager, RenderInterface $render, MessageInterface $message, PushNotifyService $push_notify) {
    $this->pushManager =$push_notify;
    parent::__construct($configuration, $plugin_id, $plugin_definition, $logger, $entity_type_manager, $render, $message);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerINterface $container, array $configuration, $plugin_id, $plugin_definition, MessageInterface $message = NULL) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('logger.channel.message_notify'),
      $container->get('entity_type.manager'),
      $container->get('renderer'),
      $message,
      $container->get('message_push_notification.push_service')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function deliver(array $output = []) {
    $account = $this->message->getOwner();
    // Load up all of the devices.
    $devices = PushClient::loadMultiple();
    // Send the notification to the devices.
    return $this->pushManager->sendNotification($output['push_notification'], $devices);
  }
}
