//
// All the JS for the dashboard widget
//

var WPNA_ADMIN_FBIA = (function($) {

	/**
	 * Whether a sync is currently in progress or not.
	 *
	 * @type {Boolean}
	 */
	var doingSync = false;

	/**
	 * Our construct function that is run when the
	 * class is first initialized
	 * @return function
	 */
	var initialize = function() {
		// Run on document ready
		$(function() {
			setupPostSyncer();

			setupStatusToggle();
		});
	}

	/**
	 * If basic auth is enabled on the RSS feed then toggle the
	 * username / password fields.
	 *
	 * @return function
	 */
	var setupPostSyncer = function setupPostSyncer() {

		// Check the element is visible.
		if ( ! $( '.wpna .wpna-sync-posts-form' ).length ) {
			return;
		}

		$( '.wpna' ).on( 'submit', '.wpna-sync-posts-form', function(e) {
			// Stop the default action.
			e.preventDefault();

			// Disable the button.
			$( this ).find( ':submit' ).attr( 'disabled', true );

			var data = $( this ).serialize();

			$( this ).append( '<span class="spinner is-active"></span><div class="wpna-progress"><div></div></div>' );
			$( '.wpna-status' ).css( 'display', 'inline-block' );

			// start the process.
			processStep( 1, data );
		});

	}

	var processStep = function processStep( step, data, self ) {

		wp.ajax.send( 'wpna_do_post_sync', {
			// Send a nonce with the request.
			data : {
				'_ajax_nonce' : wpnaPostSyncer.nonce,
				'step'        : step,
				'form'        : data
			},

			success : function( response ) {
				if ( 'done' == response.step ) {

					var exportForm = $( '.wpna-sync-posts-form' );

					exportForm.find('.spinner').remove();
					exportForm.find('.wpna-progress').remove();
					exportForm.find('.wpna-status').hide();

					// Reset the coutner in case it runs again.
					exportForm.find( '.processed' ).html( '0' );
					exportForm.find( '.total' ).html( '0' );

					exportForm.find( ':submit' ).attr( 'disabled', false );

					window.location = response.url;

				} else {

					$('.wpna-progress div').animate({
						width: response.percentage + '%',
					}, 0, function() {
						// Animation complete.
					});

					$( '.wpna-status .processed' ).html( response.processed );
					$( '.wpna-status .total' ).html( response.total );

					// Delay the next step for 2 seconds. API Rate limiting.
					setTimeout(
						processStep( parseInt( response.step ), data, self ),
						5000
					);
				}
			},

			// If error fire notifications
			error : function( error ) {
				if ( window.console && window.console.log ) {
					console.log( response );
				}
			}
		})

	}

	var setupStatusToggle = function() {

		var form = $( '.wpna-sync-posts-form' );

		var toggleStatus = function() {

			if ( 'update' === form.find( '#action' ).val() && 'production' === form.find( '#environment' ).val() ) {
				form.find( '#draft' ).closest( 'li' ).show();
			} else {
				form.find( '#draft' ).closest( 'li' ).hide();
			}
		}

		form.on( 'change', '#action, #environment', function(e) {
			toggleStatus();
		});

		toggleStatus();

	};

	// Return the initialize function.
	return initialize();

})( jQuery );
