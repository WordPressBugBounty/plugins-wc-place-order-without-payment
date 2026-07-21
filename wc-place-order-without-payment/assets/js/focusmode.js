/*!
 * Bootstrap FocusMode v1.0.0
 * Reusable Bootstrap 5 component for distraction-free UI
 * Author: NeeB Plugins
 */

(function ($) {
  const NAME = 'focusMode';
  const DATA_KEY = 'bs.focusMode';

  class FocusMode {
    constructor(element, options = {}) {
      this._element = element;
      this._isActive = false;

      const defaults = {
        adminSelectors: ['#adminmenumain', '#wpadminbar'],
        resetSelectors: ['#wpcontent'],
        iconEnter: 'dashicons-editor-expand',
        iconExit: 'dashicons-editor-contract',
        animationSpeed: 250
      };

      this._options = { ...defaults, ...options };
    }

    toggle() {
      this._isActive ? this._exit() : this._enter();
    }

    _enter() {
      const speed = this._options.animationSpeed;
      const adminEls = $(this._options.adminSelectors.join(','));
      const resetEls = $(this._options.resetSelectors.join(','));

      // Store initial styles
      resetEls.each(function () {
        const $el = $(this);
        $el.data('original-margin-left', $el.css('margin-left'));
        $el.data('original-padding', $el.css('padding'));
      });

      // Use CSS transitions for opacity instead of fadeOut
      adminEls.each(function () {
        const $el = $(this);
        if ($el.is(':visible')) {
          $el.data('original-display', $el.css('display'));
          $el.css({
            transition: `opacity ${speed}ms ease-in-out, transform ${speed}ms ease-in-out`,
            transform: 'translateX(-10px)',
            opacity: 0
          });
          // hide after transition completes
          setTimeout(() => $el.css({ display: 'none', transform: '', opacity: '', transition: '' }), speed);
        }
      });

      // Expand content smoothly
      resetEls.css({ transition: `margin-left ${speed}ms ease-in-out, padding ${speed}ms ease-in-out` })
        .css({ 'margin-left': 0, padding: 0 });

      this._isActive = true;
      $(this._element)
        .addClass('active btn-primary')
        .attr('title', 'Exit Focus Mode')
        .find('i')
        .removeClass(this._options.iconEnter)
        .addClass(this._options.iconExit);
    }

    _exit() {
      const speed = this._options.animationSpeed;
      const adminEls = $(this._options.adminSelectors.join(','));
      const resetEls = $(this._options.resetSelectors.join(','));

      // Show elements before animating back
      adminEls.each(function () {
        const $el = $(this);
        const display = $el.data('original-display') || 'block';
        $el.css({ display, opacity: 0, transform: 'translateX(-10px)' });
        requestAnimationFrame(() => {
          $el.css({
            transition: `opacity ${speed}ms ease-in-out, transform ${speed}ms ease-in-out`,
            opacity: 1,
            transform: 'translateX(0)'
          });
          setTimeout(() => $el.css({ transition: '', transform: '', opacity: '' }), speed);
        });
      });

      // Restore margin/padding
      resetEls.each(function () {
        const $el = $(this);
        const marginLeft = $el.data('original-margin-left') || '160px';
        const padding = $el.data('original-padding') || '';
        $el.css({
          transition: `margin-left ${speed}ms ease-in-out, padding ${speed}ms ease-in-out`,
          'margin-left': marginLeft,
          padding
        });
        setTimeout(() => $el.css({ transition: '' }), speed);
      });

      this._isActive = false;
      $(this._element)
        .removeClass('active btn-primary')
        .attr('title', 'Enter Focus Mode')
        .find('i')
        .removeClass(this._options.iconExit)
        .addClass(this._options.iconEnter);
    }

    static jQueryInterface(config) {
      return this.each(function () {
        let data = $(this).data(DATA_KEY);
        const _config = typeof config === 'object' ? config : {};

        if (!data) {
          data = new FocusMode(this, _config);
          $(this).data(DATA_KEY, data);
        }
        if (typeof config === 'string' && typeof data[config] === 'function') {
          data[config]();
        }
      });
    }
  }

  $.fn[NAME] = FocusMode.jQueryInterface;
  $.fn[NAME].Constructor = FocusMode;

  $(document).on('click', '[data-bs-toggle="focusmode"]', function (e) {
    e.preventDefault();
    FocusMode.jQueryInterface.call($(this), 'toggle');
  });
})(jQuery);
