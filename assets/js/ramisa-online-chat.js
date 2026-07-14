(function () {
  'use strict';

  function ready(callback) {
    if (document.readyState !== 'loading') {
      callback();
      return;
    }
    document.addEventListener('DOMContentLoaded', callback);
  }

  function setOpen(widget, toggle, isOpen) {
    widget.classList.toggle('ramisa-online-chat-open', isOpen);
    if (toggle) {
      toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    }
  }

  function updateTime(element) {
    if (!element) {
      return;
    }

    var now = new Date();
    element.textContent = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  }

  function setupAgentSearch(widget) {
    var input = widget.querySelector('[data-ramisa-agent-search]');
    var actions = widget.querySelectorAll('[data-ramisa-agent-action]');

    if (!input || !actions.length) {
      return;
    }

    input.addEventListener('input', function () {
      var value = input.value.toLowerCase().trim();

      actions.forEach(function (action) {
        var haystack = (action.getAttribute('data-search-text') || '').toLowerCase();
        action.classList.toggle('is-hidden', value && haystack.indexOf(value) === -1);
      });
    });
  }

  ready(function () {
    var widgets = document.querySelectorAll('[data-ramisa-online-chat]');

    widgets.forEach(function (widget) {
      var toggle = widget.querySelector('.ramisa-online-chat-toggle');
      var time = widget.querySelector('[data-ramisa-online-chat-time]');
      var autoshow = widget.getAttribute('data-autoshow') === '1';

      window.requestAnimationFrame(function () {
        widget.classList.add('ramisa-online-chat-ready');
      });

      if (toggle) {
        toggle.addEventListener('click', function () {
          setOpen(widget, toggle, !widget.classList.contains('ramisa-online-chat-open'));
        });
      }

      document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && widget.classList.contains('ramisa-online-chat-open')) {
          setOpen(widget, toggle, false);
        }
      });

      document.addEventListener('click', function (event) {
        if (!widget.contains(event.target) && widget.classList.contains('ramisa-online-chat-open')) {
          setOpen(widget, toggle, false);
        }
      });

      updateTime(time);
      if (time) {
        window.setInterval(function () {
          updateTime(time);
        }, 60000);
      }

      setupAgentSearch(widget);

      if (autoshow) {
        window.setTimeout(function () {
          setOpen(widget, toggle, true);
        }, 750);
      }
    });
  });
}());
