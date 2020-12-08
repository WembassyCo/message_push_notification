<?php

namespace Drupal\message_push_notification\Controller;

use Drupal\message_push_notification\Entity\PushClient;

/**
 * Controller for managing push notifications.
 */
class MessagePushNotification {

    /**
     * Simple callback used to register a device for push notifications.
     */
    public function registerDevice() {
      $request = \Drupal::request();
      $data = $request->request->all();

      $ids = \Drupal::entityQuery('push_client')
        ->condition('public_key', $request->request->get('public_key'))
        ->condition('auth_token', $request->request->get('auth_token'))
        ->execute();
      if (count($ids) < 1) {
        $data = [
          'uid' => \Drupal::currentUser()->id(),
          'endpoint' => $request->request->get('endpoint'),
          'public_key' => $request->request->get('public_key'),
          'auth_token' => $request->request->get('auth_token'),
          'contentEncoding' => $request->request->get('encoding')
        ];
        \Drupal::logger('message_push_notification')->notice("New Device Data: " . print_r($data, true));
        $entity = PushClient::create($data);
        $entity->save();
      }
      else {
         $entity = entity_load('push_client', array_shift($ids));
         $entity->set('endpoint', $request->request->get('endpoint'));
	       $entity->set('contentEncoding', $request->request->get('encoding'));
      }
      return [
        '#markup' => 'success'
      ];
    }

}
