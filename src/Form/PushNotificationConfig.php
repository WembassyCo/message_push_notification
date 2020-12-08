<?php

namespace Drupal\message_push_notification\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\message_push_notification\Entity\PushClient;

/**
 * Configuration settings for push notifications.
 */
class PushNotificationConfig extends ConfigFormBase {

  const SETTINGS = 'message_push_notification.settings';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'push_notification_config';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      static::SETTINGS
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(static::SETTINGS);
    $form['push_reauth_interval'] = [
      '#type' => 'textfield',
      '#title' => t('Push Notification Reauthentication'),
      '#description' => t('Set the number of seconds you would like to pass before trying to re-authorize push notifications. Enter -1 for never.'),
      '#default_value' => $config->get('push_reauth_interval'),
    ];
    $form['public_key'] = [
      '#type' => 'textarea',
      '#title' => t('Public Key'),
      '#description' => t('Textarea file for public key.'),
      '#default_value' => $config->get('public_key'),
      '#required' => TRUE,
    ];
    $form['private_key'] = [
      '#type' => 'textarea',
      '#title' => t('Private Key'),
      '#description' => t('Textarea file for private key.'),
      '#default_value' => $config->get('private_key'),
      '#required' => TRUE,
    ];

    $form['test'] = [
      '#title' => t('Test Notifications'),
      '#type' => 'fieldset',
      '#tree' => TRUE
    ];

    $devices = [];
    foreach(PushClient::loadMultiple() as $id => $entity) {
      $devices[$entity->id()] = $entity->uuid->value . ' | ' . $entity->user_id->entity->label();
    }

    $form['test']['purge_devices'] = [
      '#type' => 'checkbox',
      '#title' => t('Purge Devices')
    ];

    $form['test']['device'] = [
      '#type' => 'select',
      '#title' => t('Device'),
      '#options' => $devices
    ];

    $form['test']['message'] = [
      '#type' => 'textarea',
      '#title' => t('Message')
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->configFactory->getEditable(static::SETTINGS)
      ->set('push_reauth_interval', $form_state->getValue('push_reauth_interval'))
      ->set('public_key', $form_state->getValue('public_key'))
      ->set('private_key', $form_state->getValue('private_key'))
      ->save();
    $values = $form_state->getValues();
    if ($values['test']['message'] !== '') {
      $service = \Drupal::service('message_push_notification.push_service');
      $device = entity_load('push_client', $values['test']['device']);
      if ($device) {
          $service->sendNotification($values['test']['message'], [$device]);
      }
      else {
        \Drupal::logger('message_push_notification')->notice("Could not load device");
      }

    }

    if($values['test']['purge_devices']) {
      $ids = \Drupal::entityQuery('push_client')->execute();
      entity_delete_multiple('push_client', $ids);
    }

    parent::submitForm($form, $form_state);
  }
}
