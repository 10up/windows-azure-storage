/* eslint-disable no-var */
( function () {

	/**
	 * Checks for webp support.
	 * @param {function} callback Callback function to call
	 */
	var WebpIsSupported = function ( callback ) {
		// If the browser doesn't has the method createImageBitmap, you can't display webp format
		if ( !window.createImageBitmap ) {
			callback( false );
			return;
		}

		// Base64 representation of a white point image
		var webpdata = 'data:image/webp;base64,UklGRiQAAABXRUJQVlA4IBgAAAAwAQCdASoCAAEAAQAcJaQAA3AA/v3AgAA=';

		// Retrieve the Image in Blob Format
		fetch( webpdata ).then( function ( response ) {
			return response.blob();
		} ).then( function ( blob ) {
			// If the createImageBitmap method succeeds, return true, otherwise false
			createImageBitmap( blob ).then( function () {
				callback( true );
			}, function () {
				callback( false );
			} );
		} );
	};

	/**
	 * Swaps webp images for original images.
	 */
	var doNotLoadWebp = function () {
		var images = [].slice.call( document.getElementsByClassName( 'webp-format' ) );
		if ( !images.length ) {
			return;
		}

		images.forEach( function ( image ) {
			var source = image.getAttribute( 'data-orig-src' );
			var srcset = image.getAttribute( 'data-orig-srcset' );

			if ( source ) {
				image.setAttribute( 'src', source );
				image.setAttribute( 'data-src', source );
			}

			if ( srcset ) {
				image.setAttribute( 'srcset', srcset );
				image.setAttribute( 'data-srcset', srcset );
			}

			image.removeAttribute( 'data-orig-src' );
			image.removeAttribute( 'data-orig-srcset' );

			if ( image.classList.contains( 'avatar' ) && image.hasAttribute( 'srcset' ) ) {
				image.setAttribute( 'srcset', image.getAttribute( 'srcset' ).replace( /webp/g, image.getAttribute( 'src' ).split( '.' ).pop() ) );
			}
		} );
	};

	/**
	 * Cleans up the Webp images by removing data-orig-src and data-orig-srcset
	 */
	var cleanupWebp = function () {
		var images = [].slice.call( document.querySelectorAll( '.webp-format' ) );
		if ( !images.length ) {
			return;
		}

		images.forEach( function ( image ) {
			image.removeAttribute( 'data-orig-src' );
			image.removeAttribute( 'data-orig-srcset' );
		} );
	};

	/**
	 * Check compatibility
	 */
	var checkCompat = function () {
		WebpIsSupported( function ( supported ) {
			if ( !supported ) {
				// Do the swap
				doNotLoadWebp();
			} else {
				cleanupWebp();
			}
		} );
	};

	/**
	 * Init
	 */
	var init = function () {
		document.addEventListener( 'DOMContentLoaded', checkCompat );
		document.addEventListener( 'loadmore:contentadded', checkCompat );
	};

	init();
} )();
