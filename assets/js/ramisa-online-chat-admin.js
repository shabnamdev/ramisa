(function ($) {
  'use strict';

  var icons = {
    chat: '💬',
    support: '🎧',
    send: '✉',
    phone: '☎',
    help: '?'
  };

  function updatePreview(target, value) {
    if (!target) {
      return;
    }

    if (target === 'photo') {
      $('[data-preview-output="photo"]').attr('src', value);
      return;
    }

    if (target === 'icon') {
      $('[data-preview-output="icon"]').text(icons[value] || icons.chat);
      return;
    }

    if (target === 'theme') {
      updateTheme(value);
      return;
    }

    $('[data-preview-output="' + target + '"]').text(value);
  }


  function hasValidDestination() {
    var url = ($('#ramisa_online_chat_chat_url').val() || '').trim();
    var phone = ($('#ramisa_online_chat_whatsapp_number').val() || '').replace(/\D+/g, '');

    if (url) {
      try {
        var parsed = new URL(url);
        return parsed.protocol === 'https:' || parsed.protocol === 'http:';
      } catch (error) {
        return false;
      }
    }

    return phone.length >= 8 && phone.length <= 15;
  }

  function updateDestinationStatus() {
    var card = $('[data-ramisa-connection-status]');
    if (!card.length) {
      return;
    }

    if (hasValidDestination()) {
      card.removeClass('is-missing').addClass('is-ready');
      card.find('[data-ramisa-status-label]').text('WhatsApp destination is ready');
      card.find('[data-ramisa-status-message]').text('The widget has a valid chat destination and will open it after a visitor clicks the button.');
      return;
    }

    card.removeClass('is-ready').addClass('is-missing');
    card.find('[data-ramisa-status-label]').text('WhatsApp destination is not configured');
    card.find('[data-ramisa-status-message]').text('Add a valid WhatsApp number or a public chat URL before publishing the widget.');
  }

  function updateTheme(value) {
    var card = $('.ramisa-admin-preview-card');
    card.removeClass('ramisa-admin-preview-theme-green ramisa-admin-preview-theme-blue ramisa-admin-preview-theme-violet ramisa-admin-preview-theme-gold ramisa-admin-preview-theme-dark');
    card.addClass('ramisa-admin-preview-theme-' + value);
  }

  function activateTab(id, updateHash) {
    var target = $('[data-ramisa-panel="' + id + '"]');

    if (!target.length) {
      id = 'ramisa-general';
      target = $('[data-ramisa-panel="' + id + '"]');
    }

    $('[data-ramisa-tab]').removeClass('is-active').attr('aria-selected', 'false');
    $('[data-ramisa-tab="' + id + '"]').addClass('is-active').attr('aria-selected', 'true');
    $('[data-ramisa-panel]').removeClass('is-active').attr('hidden', 'hidden');
    target.addClass('is-active').removeAttr('hidden');

    if (updateHash && window.history && window.history.replaceState) {
      window.history.replaceState(null, document.title, '#' + id);
    }
  }

  function openMediaFrame(target) {
    if (typeof wp === 'undefined' || !wp.media || !target.length) {
      return;
    }

    var frame = wp.media({
      multiple: false,
      library: {
        type: 'image'
      }
    });

    frame.on('select', function () {
      var attachment = frame.state().get('selection').first().toJSON();
      if (attachment && attachment.url) {
        target.val(attachment.url).trigger('change');
      }
    });

    frame.open();
  }

  $(function () {
    var hash = window.location.hash ? window.location.hash.replace('#', '') : 'ramisa-general';
    activateTab(hash, false);

    $(document).on('click', '[data-ramisa-tab]', function (event) {
      event.preventDefault();
      activateTab($(this).attr('data-ramisa-tab'), true);
    });

    $(document).on('click', '[data-ramisa-jump]', function (event) {
      event.preventDefault();
      activateTab($(this).attr('data-ramisa-jump'), true);
    });

    updateDestinationStatus();

    $(document).on('input change', '.ramisa-admin-connection-input', updateDestinationStatus);

    $(document).on('input change', '.ramisa-admin-live-input', function () {
      var input = $(this);
      updatePreview(input.attr('data-preview-target'), input.val());
    });

    $(document).on('change', '.ramisa-admin-live-select', function () {
      var input = $(this);
      updatePreview(input.attr('data-preview-target'), input.val());
    });

    $(document).on('click', '.ramisa-admin-media-button', function (event) {
      event.preventDefault();
      openMediaFrame($('#' + $(this).attr('data-target')));
    });
  });
}(jQuery));
