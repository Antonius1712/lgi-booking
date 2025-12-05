/**
 * Global Notyf Configuration
 * Place this in your main app.js or create a separate notifications.js
 */

'use strict';

// Custom Notyf class to allow HTML content in messages
class CustomNotyf extends Notyf {
  _renderNotification(options) {
    const notification = super._renderNotification(options);

    // Replace textContent with innerHTML to render HTML content
    if (options.message) {
      notification.message.innerHTML = options.message;
    }

    return notification;
  }
}

// Initialize global CustomNotyf instance
window.notyf = new CustomNotyf({
  duration: 4000,
  ripple: true,
  dismissible: true,
  position: { x: 'right', y: 'top' },
  types: [
    {
      type: 'info',
      background: config.colors.info,
      className: 'notyf__info',
      icon: {
        className: 'icon-base bx bxs-info-circle icon-md text-white',
        tagName: 'i'
      }
    },
    {
      type: 'warning',
      background: config.colors.warning,
      className: 'notyf__warning',
      icon: {
        className: 'icon-base bx bxs-error icon-md text-white',
        tagName: 'i'
      }
    },
    {
      type: 'success',
      background: config.colors.success,
      className: 'notyf__success',
      icon: {
        className: 'icon-base bx bxs-check-circle icon-md text-white',
        tagName: 'i'
      }
    },
    {
      type: 'error',
      background: config.colors.danger,
      className: 'notyf__error',
      icon: {
        className: 'icon-base bx bxs-x-circle icon-md text-white',
        tagName: 'i'
      }
    }
  ]
});