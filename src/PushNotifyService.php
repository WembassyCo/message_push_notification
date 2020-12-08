<?php

namespace Drupal\message_push_notification;

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
use Drupal\message_push_notificaiton\Entity\PushClient;

class PushNotifyService {

  /**
   * Sends a push notification.
   */
  public function sendNotification($message, $devices) {
    $config = \Drupal::config('message_push_notification.settings');
    $auth = [
      'VAPID' => [
        'subject' => $_SERVER['SERVER_ADDR'],
        'publicKey' => $config->get('public_key'),
        'privateKey' => $config->get('private_key')
      ]
    ];

    // Loop through correct subscriptions
    foreach($devices as $id => $entity) {

      if ($entity) {

       try {

          $subscription = new Subscription( $entity->endpoint->value, $entity->publicKey->value, $entity->authToken->value, $entity->contentEncoding->value);
          $webPush = new WebPush($auth);

          $status = $webPush->sendOneNotification($subscription, $message);
          if ($status->isSuccess()) {
              error_log("[v] Message sent successfully for subscription {$entity->endpoint->value}.");
              \Drupal::logger('message_push_notification')->notice("[v] Message sent successfully for subscription {$entity->endpoint->value}.");
          } else {
              error_log("[x] Message failed to sent for subscription {$entity->endpoint->value}: {$report->getReason()}");
              \Drupal::logger('message_push_notification')->error("[x] Message failed to sent for subscription {$entity->endpoint->value}: {$report->getReason()}");
          }

          foreach ($webPush->flush() as $report) {
              $endpoint = $report->getRequest()->getUri()->__toString();

              if ($report->isSuccess()) {
                  error_log("[v] Message sent successfully for subscription {$endpoint}.");
                  \Drupal::logger('message_push_notification')->notice("[v] Message sent successfully for subscription {$endpoint}.");
              } else {
                  error_log("[x] Message failed to sent for subscription {$endpoint}: {$report->getReason()}");
                  \Drupal::logger('message_push_notification')->error("[x] Message failed to sent for subscription {$endpoint}: {$report->getReason()}");
              }
          }
        }
        catch (\Exception $e) {
	        watchdog_exception('message_push_notification', $e);
        }
      }
    }
    return TRUE;
  }
}
