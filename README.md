# Message Push Notification

This module adds support to send Push notifications to browsers and mobile devices
leveraging the Message API framework.  Currently it leverages the Google API
push notification system, but in the future I am looking to also add support for
other platforms like Twilio.

Installation instructions are simple:
1. Ensure that your website is running HTTPS.
2. Register for Google Developer Console and generate an API key.
3. Install Message module and the Message Push Notification module.
4. Go to the message push notification module settings and enter in your API details

Useage:
You will want to create a new Message API template that leverages the new push
notification method.  Optionally, you can programmatically send push notifications
based on backend events, read the Message Framework api on how to do this.
