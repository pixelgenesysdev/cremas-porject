/*
 * jQuery Superfish Menu Plugin - v1.7.4
 * Copyright (c) 2013 Joel Birch
 *
 * Dual licensed under the MIT and GPL licenses:
 *	http://www.opensource.org/licenses/mit-license.php
 *	http://www.gnu.org/licenses/gpl.html
 */

;(function ($) {
	"use strict";


	$(window).on('resize',function(){
		$('.sf-menu ul.tracked-pos').removeClass('tracked-pos');
	});

	var methods = (function () {
		// private properties and methods go here
		var c = {
				bcClass: 'sf-breadcrumb',
				menuClass: 'sf-js-enabled',
				anchorClass: 'sf-with-ul',
				menuArrowClass: 'sf-arrows'
			},
			ios = (function () {
				var ios = /iPhone|iPad|iPod/i.test(navigator.userAgent);
				if (ios) {
					// tap anywhere on iOS to unfocus a submenu
					$('html').css('cursor', 'pointer').on('click', $.noop);
				}
				return ios;
			})(),
			wp7 = (function () {
				var style = document.documentElement.style;
				return ('behavior' in style && 'fill' in style && /iemobile/i.test(navigator.userAgent));
			})(),
			toggleMenuClasses = function ($menu, o) {
				var classes = c.menuClass;
				if (o.cssArrows) {
					classes += ' ' + c.menuArrowClass;
				}
				$menu.toggleClass(classes);
			},
			setPathToCurrent = function ($menu, o) {
				return $menu.find('li.' + o.pathClass).slice(0, o.pathLevels)
					.addClass(o.hoverClass + ' ' + c.bcClass)
						.filter(function () {
							return ($(this).children(o.popUpSelector).hide().show().length);
						}).removeClass(o.pathClass);
			},
			toggleAnchorClass = function ($li) {
				$li.children('a').toggleClass(c.anchorClass);
			},
			toggleTouchAction = function ($menu) {
				var touchAction = $menu.css('ms-touch-action');
				touchAction = (touchAction === 'pan-y') ? 'auto' : 'pan-y';
				$menu.css('ms-touch-action', touchAction);
			},
			applyHandlers = function ($menu, o) {
				var targets = 'li:has(' + o.popUpSelector + ')';
				if ($.fn.hoverIntent && !o.disableHI) {
					$menu.hoverIntent(over, out, targets);
				}
				else {
					$menu
						.on('mouseenter.superfish', targets, over)
						.on('mouseleave.superfish', targets, out);
				}
				var touchevent = 'MSPointerDown.superfish';
				if (!ios) {
					touchevent += ' touchend.superfish';
				}
				if (wp7) {
					touchevent += ' mousedown.superfish';
				}
				$menu
					.on('focusin.superfish', 'li', over)
					.on('focusout.superfish', 'li', out)
					.on(touchevent, 'a', o, touchHandler);
			},
			touchHandler = function (e) {
				var $this = $(this),
					$ul = $this.siblings(e.data.popUpSelector);

				if ($ul.length > 0 && $ul.is(':hidden')) {
					$this.one('click.superfish', false);
					if (e.type === 'MSPointerDown') {
						$this.trigger('focus');
					} else {
						$.proxy(over, $this.parent('li'))();
					}
				}
			},
			over = function () {
				var $this = $(this),
					o = getOptions($this);

				if($(this).parents('.megamenu').length > 0) return;

				clearTimeout(o.sfTimer);
				$this.siblings().superfish('hide').end().superfish('show');
			},
			out = function () {
				var $this = $(this),
					o = getOptions($this);
				if (ios) {
					$.proxy(close, $this, o)();
				}
				else {
					clearTimeout(o.sfTimer);
					o.sfTimer = setTimeout($.proxy(close, $this, o), o.delay);
				}
			},
			close = function (o) {
				o.retainPath = ($.inArray(this[0], o.$path) > -1);
				this.superfish('hide');

				if (!this.parents('.' + o.hoverClass).length) {
					o.onIdle.call(getMenu(this));
					if (o.$path.length) {
						$.proxy(over, o.$path)();
					}
				}
			},
			getMenu = function ($el) {
				return $el.closest('.' + c.menuClass);
			},
			getOptions = function ($el) {
				return getMenu($el).data('sf-options');
			};

		return {
			// public methods
			hide: function (instant) {
				if (this.length) {
					var $this = this,
						o = getOptions($this);
					if (!o) {
						return this;
					}

					if($(this).hasClass('menu-item-over') && $(this).hasClass('megamenu')) {
						return true;
					}

					var not = (o.retainPath === true) ? o.$path : '',
						$ul = $this.find('li.' + o.hoverClass).add(this).not(not).removeClass(o.hoverClass).children(o.popUpSelector),
						speed = o.speedOut;

					if (instant) {
						$ul.show();
						speed = 0;
					}
					o.retainPath = false;
					o.onBeforeHide.call($ul);

					if(o.dropdownStyle == 'minimal') {
						var $this = $(this);
						o.onHide.call($this);
					} else {

						$ul.stop(true, true).animate(o.animationOut, speed, function () {
							var $this = $(this);
							o.onHide.call($this);
						});
					}

					if($(this).parents('.megamenu').length > 0) return;

					//megamenu removes transparent option
					if($('#header-outer[data-megamenu-rt="1"]').length > 0 && $('#header-outer[data-transparent-header="true"]').length > 0) {

						if($('#header-outer.scrolled-down').length == 0 && $('#header-outer.small-nav').length == 0 && $('#header-outer.detached').length == 0 && $('#header-outer.fixed-menu').length == 0) {
							$('#header-outer').addClass('transparent');
						}
						if($('#header-outer[data-permanent-transparent="1"][data-transparent-header="true"]').length > 0) {
							$('#header-outer').addClass('transparent');
						}

					}
				}
				return this;
			},
			show: function () {

				if($(this).parents('.megamenu').length > 0) return;

				var o = getOptions(this);
				if (!o) {
					return this;
				}
			
				var $this = this.addClass(o.hoverClass),
					$ul = $this.children(o.popUpSelector);


				//megamenu removes transparent option
				if($('#header-outer[data-megamenu-rt="1"]').length > 0 &&
				   $(this).hasClass('megamenu') &&
					 !$(this).hasClass('width-75') &&
					 !$(this).hasClass('width-50') &&
					 $('#header-outer').attr('data-transparent-header') == 'true' ) {

					$('#header-outer').addClass('no-transition');
					$('#header-outer').removeClass('transparent');

				}

				o.onBeforeShow.call($ul);


				//make sure the ul has space
				if(!$ul.hasClass('tracked-pos') &&
					 !$($ul).parents('li').hasClass('megamenu') &&
					!$($ul).parents('ul').hasClass('sub-menu') &&
					 $ul.offset() ) {

					$ul.addClass('temp-hidden-display');
					var docW = $("#top .container").width();
					var elm = $ul;
					var off = elm.offset();
					var l = off.left - ($(window).width() - docW)/2;
					var w = elm.width();
					var isEntirelyVisible = (l+w <= $(window).width()-100);

					if ( ! isEntirelyVisible ) {
						$ul.parents('li').addClass('edge');
					} else {
						$ul.parents('li').removeClass('edge');
					}

					$ul.removeClass('temp-hidden-display');

				}

				if(o.dropdownStyle == 'minimal') {
					o.onShow.call($ul);
				} else {
					$ul.stop(true, true).animate(o.animation, o.speed, function () {
						o.onShow.call($ul);
					});
				}

				//show on left class
				if( !$ul.hasClass('tracked-pos') ) {
					if( $ul.length > 0 && $ul.parents('.sub-menu').length > 0 && $ul.parents('.sf-menu').length > 0 ) {
						if($ul.offset().left + $ul.outerWidth() > $(window).width()) {
							$ul.addClass('on-left-side');
							$ul.find('ul').addClass('on-left-side');
						}
					}
				}

				$ul.addClass('tracked-pos');

				return this;
			},
			destroy: function () {
				return this.each(function () {
					var $this = $(this),
						o = $this.data('sf-options'),
						$hasPopUp;
					if (!o) {
						return false;
					}
					$hasPopUp = $this.find(o.popUpSelector).parent('li');
					clearTimeout(o.sfTimer);
					toggleMenuClasses($this, o);
					toggleAnchorClass($hasPopUp);
					toggleTouchAction($this);
					// remove event handlers
					$this.off('.superfish').off('.hoverIntent');
					// clear animation's inline display style
					$hasPopUp.children(o.popUpSelector).attr('style', function (i, style) {
						return style.replace(/display[^;]+;?/g, '');
					});
					// reset 'current' path classes
					o.$path.removeClass(o.hoverClass + ' ' + c.bcClass).addClass(o.pathClass);
					$this.find('.' + o.hoverClass).removeClass(o.hoverClass);
					o.onDestroy.call($this);
					$this.removeData('sf-options');
				});
			},
			init: function (op) {
				return this.each(function () {
					var $this = $(this);
					if ($this.data('sf-options')) {
						return false;
					}
					var o = $.extend({}, $.fn.superfish.defaults, op),
						$hasPopUp = $this.find(o.popUpSelector).parent('li');
					o.$path = setPathToCurrent($this, o);

					$this.data('sf-options', o);

					toggleMenuClasses($this, o);
					toggleAnchorClass($hasPopUp);
					toggleTouchAction($this);
					applyHandlers($this, o);

					$hasPopUp.not('.' + c.bcClass).superfish('hide', true);

					o.onInit.call(this);
				});
			}
		};
	})();

	$.fn.superfish = function (method, args) {
		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		}
		else if (typeof method === 'object' || ! method) {
			return methods.init.apply(this, arguments);
		}
		else {
			return $.error('Method ' +  method + ' does not exist on jQuery.fn.superfish');
		}
	};

	$.fn.superfish.defaults = {
		popUpSelector: 'ul,.sf-mega, .nectar-global-section-megamenu', // within menu context
		hoverClass: 'sfHover',
		pathClass: 'overrideThisToUse',
		pathLevels: 1,
		delay: 800,
		animation: {opacity: 'show'},
		animationOut: {opacity: 'hide'},
		speed: 'normal',
		speedOut: 'fast',
		cssArrows: true,
		disableHI: false,
		onInit: $.noop,
		onBeforeShow: $.noop,
		onShow: $.noop,
		onBeforeHide: $.noop,
		onHide: $.noop,
		onIdle: $.noop,
		onDestroy: $.noop,
		dropdownStyle: ($('body[data-dropdown-style="minimal"]').length > 0) ? 'minimal' : 'classic'
	};

	// soon to be deprecated
	$.fn.extend({
		hideSuperfishUl: methods.hide,
		showSuperfishUl: methods.show
	});

})(jQuery);
