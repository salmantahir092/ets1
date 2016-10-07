/**
 * jQuery MGlass, Displays a magnifying glass on image hover
 * http://github.com/younes0/jQuery-MGlass 
 * 
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 */
(function($) {

	// Start
	$.mglass = function(element, options) {

		// Defaults
		var defaults = {
			opacity: 0.4,
			speed: 150,
			wrapper: true
		};
	
		var plugin = this, $element = $(element);
		
		plugin.settings = {};
		

		// Constructor
		plugin.init = function() {

			plugin.settings = $.extend({}, defaults, options);
						
			if (plugin.settings.wrapper) {
				$element.wrap('<div class="mglassWrapper" />');
			}

			var 
				h = $element.height(), 
				w = $element.width(),
				b = $element.css('border-top-width')
			;

			var overlayStyle = 'width: '+w+'px; height: '+h+'px;'; 
			
			// if original image has border (border-top as reference), set width as margin
			if (b) {
				overlayStyle+= 'margin: '+b+';';
			}

			// CSS3 transition Support ?
			if ( plugin.supportsTransitions() ) {
				overlayStyle+= $.fn.mglass.transitionProperty+': opacity '+(plugin.settings.speed/1000)+'s ease;';
			}

			// Mglass Div
			$overlay = $('<div class="mglass" style="'+overlayStyle+'"></div>');
			$overlay.insertBefore($(element));

			// No CSS3 transition support : javascript fallback
			if ( !$.css3Transitions ) {

				if ( plugin.ieVersion() <= 8 ) {
					$overlay.css({"opacity": 0});
				}

				$overlay.hover(
					function () {
						$(this).css({"opacity": 0}).stop().animate({"opacity": plugin.settings.opacity}, plugin.settings.speed);
					},
					function () {
						$(this).stop().animate({"opacity": 0}, 100);
					}
				);

			}
		
		},

		plugin.supportsTransitions = function() {

			if (typeof $.css3Transitions === 'undefined') {
				var el      = document.createElement('div');
				var vendors = ['', 'Ms', 'Moz', 'Webkit', 'O'];

				for (var i = 0, len = vendors.length; i < len; i++) {
					var prop = vendors[i] + 'Transition';
					if (prop in el.style) {
						$.fn.mglass.transitionProperty = '-'+vendors[i].toLowerCase()+'-transition';
						$.css3Transitions = true;
						break;
					}
				}

				$.css3Transitions = false;
			}

			return $.css3Transitions;
		};

		plugin.ieVersion = function(){

			if (typeof $.ieVersion === 'undefined') {
				var undef,
					v = 3,
					div = document.createElement('div'),
					all = div.getElementsByTagName('i');

				while (
					div.innerHTML = '<!--[if gt IE ' + (++v) + ']><i></i><![endif]-->',
					all[0]
				);

		    	$.ieVersion = v > 4 ? v : undef;
			}

			return $.ieVersion;
		};

		// Init
		plugin.init();

	};

	// Add the plugin to the jQuery.fn object
	$.fn.mglass = function(options) {
		return this.each(function() {
			if (undefined === $(this).data('mglass')) {
				var plugin = new $.mglass(this, options);
				$(this).data('mglass', plugin);
			}
		});
	};

// End
})(jQuery);
