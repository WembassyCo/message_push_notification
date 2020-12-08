(function ($, Drupal) {

  Drupal.behaviors.message_push_notification = {
    checkNotificationPermission: function() {
      return new Promise( (resolve, reject) => {
        if (Notification.permission === 'denied') {
          return reject(new Error('Push messages are blocked.'));
        }

        if (Notification.permission === 'granted') {
          return resolve();
        }

        if (Notification.permission === 'default') {
          return Notification.requestPermission().then(result => {
            if (result !== 'granted') {
              reject(new Error('Bad permission result'));
            }
            resolve();
          });
        }

      });  // end promise
    },
    urlBase64ToUint8Array: function(base64String) {
      padding = '='.repeat((4 - (base64String.length % 4)) % 4);
      base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');

      rawData = window.atob(base64);
      outputArray = new Uint8Array(rawData.length);

      for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
      }
      return outputArray;
    },
    sendSubscriptionToServer: function (subscription) {
      key = subscription.getKey('p256dh');
      token = subscription.getKey('auth');
      contentEncoding = (PushManager.supportedContentEncodings || ['aesgcm'])[0];
      data = {
        endpoint: subscription.endpoint,
        publicKey: key ? btoa(String.fromCharCode.apply(null, new Uint8Array(key))) : null,
        authToken: token ? btoa(String.fromCharCode.apply(null, new Uint8Array(token))) : null,
        encoding: contentEncoding,
      };
      localStorage.setItem('push_notifications', data);
      return $.post( "/push_notification_register", data).then(() => subscription);
    },
    initialized_push: false,
    init: function(context, settings) {
      navigator.serviceWorker.register(drupalSettings.message_push_notification.service_worker).then(
        (registration) => {
          Drupal.behaviors.message_push_notification.serviceWorkerRegistration = registration;
          Drupal.behaviors.message_push_notification.serviceWorkerReady(registration);
        },
        e => {
          console.warn('[SW] Service worker registration failed', e);
        }
      );
    },
    serviceWorkerReady: function (serviceWorkerRegistration) {

      serviceWorkerRegistration.pushManager.getSubscription().then((subscription) => {
          if (subscription == null) {
            serviceWorkerRegistration.pushManager.subscribe({
              userVisibleOnly:true,
              applicationServerKey: Drupal.behaviors.message_push_notification.urlBase64ToUint8Array(drupalSettings.message_push_notification.appServerKey),
            });
            return;
          }
          return Drupal.behaviors.message_push_notification.sendSubscriptionToServer(subscription);
      });

      return Drupal.behaviors.message_push_notification.checkNotificationPermission()
        .then(() => { return navigator.serviceWorker.ready; } )
        .then(serviceWorkerRegistration =>
            serviceWorkerRegistration.pushManager.subscribe({
              userVisibleOnly:true,
              applicationServerKey: Drupal.behaviors.message_push_notification.urlBase64ToUint8Array(drupalSettings.message_push_notification.appServerKey),
            })
        );

    },
    attach: function(context, settings) {
      if (!('serviceWorker' in navigator)) {
        console.warn('Service workers are not supported by this browser');
        return;
      }

      if (!('PushManager' in window)) {
        console.warn('Push notifications are not supported by this browser');
        return;
      }

      if (!('showNotification' in ServiceWorkerRegistration.prototype)) {
        console.warn('Notifications are not supported by this browser');
        return;
      }

      // Check the current Notification permission.
      // If its denied, the button should appears as such, until the user changes the permission manually
      if (Notification.permission === 'denied') {
        console.warn('Notifications are denied by the user');
        return;
      }

      if (Notification.permission !== 'granted' || localStorage.getItem('push_notifications') == null) {
        if (!Drupal.behaviors.message_push_notification.initialized_push) {
          Drupal.behaviors.message_push_notification.initialized_push = true;
          Drupal.behaviors.message_push_notification.init(context, settings);
        }
      }
       else {
        Drupal.behaviors.message_push_notification.initialized_push = true;
      }
    }
  }

})(jQuery, Drupal);
