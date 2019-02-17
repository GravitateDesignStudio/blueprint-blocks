jQuery(document).ready(function ($) {
	function gravBlocksFormatVideoURL(url) {
		var videoId = new RegExp('[\\?&]v=([^&#]*)').exec(url);
		var formattedUrl = '';
		
		if (videoId && videoId[1]) {
			// YouTube URL
			formattedUrl = `https://youtube.com/embed/${videoId[1]}?rel=0&wmode=transparent&autoplay=1&showinfo=0`;
		} else if (videoSrc.indexOf('vimeo.com') !== -1) {
			// Vimeo URL
			var urlTest = document.createElement('a');
			urlTest.href = videoSrc;

			if (urlTest.search.indexOf('autoplay') === -1) {
				formattedUrl = urlTest.search.length
					? urlTest.href + '&autoplay=1'
					: urlTest.href + '?autoplay=1';
			}
		}

		if (!formattedUrl) {
			formattedUrl = url;
		}

		return formattedUrl;
	}

	function gravBlocksInitColorbox(selectors, config) {
		selectors.forEach(function (selector) {
			// make sure the href atttribute passes through the
			// format video url function so videos are embeded correctly
			config[selector].href = function () {
				return gravBlocksFormatVideoURL(this.href);
			};

			$(selector).colorbox(config[selector]);
		});
	}

	if (typeof blocksColorboxConfig === 'undefined') {
		return;
	}

	var foundSelectors = [];

	Object.keys(blocksColorboxConfig.params).forEach(function (selector) {
		if ($(selector).length) {
			foundSelectors.push(selector);
		}
	});

	if (!foundSelectors.length) {
		return;
	}

	// check if colorbox exists and load if needed
	if (!$.colorbox) {
		var cboxScript = document.createElement('script');
		cboxScript.type = 'text/javascript';
		cboxScript.src = blocksColorboxConfig.scriptUrl;
		cboxScript.onload = function () {
			gravBlocksInitColorbox(foundSelectors, blocksColorboxConfig.params);
		};

		document.body.appendChild(cboxScript);
	} else {
		gravBlocksInitColorbox(foundSelectors, blocksColorboxConfig.params);
	}
});
