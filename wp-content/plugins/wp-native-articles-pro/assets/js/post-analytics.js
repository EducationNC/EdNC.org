//
// All the JS for the dashboard widget
//

var WPNA_POST_ANALYTICS = (function($) {

	var canvas = document.getElementById('wpna-fbia-chart');

	/**
	 * Our construct function that is run when the
	 * class is first initialized
	 * @return function
	 */
	var initialize = function() {
		// Run on document ready
		$(function() {
			setupSelectors();

			if ( $('#fbia_status'.length) ){
				setupImportStatus();
			}

			if ( $('#fbia_analytics').length ) {
				setupInsights();
			}
		});
	};

	/**
	 * Setup selectors to run when somerhign changes
	 * @return function
	 */
	var setupSelectors = function setupSelectors() {

		if ( $( '[data-wpna-datepicker="true"]' ).length ) {
			$( '[data-wpna-datepicker="true"]' ).datepicker({ dateFormat : 'yy-mm-dd' });
		}

		if ( $( '[data-wpna-redraw-analytics="true"]' ).length ) {
			$( '[data-wpna-redraw-analytics="true"]' ).change(function() {

				$('.wpna-loading-spinner').show();

				setupInsights();
			});
		}

		if ( $( '.wpna-edit-fbia-status' ).length ) {
			setupPostStatus();
		}
	}

	var setupPostStatus = function setupPostStatus() {
		$( '.misc-fbia-status' ).on( 'click', '.wpna-edit-fbia-status', function( e ) {
			e.preventDefault();

			$( '#fbia-status-select' ).slideDown( 'fast' );

			$( this ).hide();

		});

		$( '.misc-fbia-status' ).on( 'click', '.wpna-cancel-fbia-status', function( e ) {
			e.preventDefault();

			$( '#fbia-status-select' ).slideUp( 'fast' );

			$( '.wpna-edit-fbia-status' ).show();

		});

		$( '.misc-fbia-status' ).on( 'click', '.wpna-save-fbia-status', function( e ) {
			e.preventDefault();

			$( '.wpna-fbia-status-text' ).text( $( '#wpna_fbia_status option:selected' ).text() );

			$( '#fbia-status-select' ).slideUp( 'fast' );

			$( '.wpna-edit-fbia-status' ).show();

		});
	}

	/**
	 * Listens for an ajax response and shows the import status of the post.
	 *
	 * @return null
	 */
	var setupImportStatus = function setupImportStatus() {

		wp.ajax.send( 'wpna-post-meta-box-facebook-import-status', {

			// Send a nonce with the request
			data : {
				'_ajax_nonce': wpnaPostAnalytics.nonce,
				'post_id': wpnaPostAnalytics.post_id
			},

			// If error fire notifications
			error : function( response ) {

				// Hide the loading spinner
				$('#fbia_status .wpna-loading-spinner').hide();

				// Remove any existing errors
				$('#fbia_status .wpna-error').remove();

				// Log the error
				/*
				 * @todo Conditionally show JS errors
				 */
				console.log( response );

				// Show an error message
				// wpnaPostAnalytics.errorMessage
				$( '#fbia_status' ).append( '<p class="wpna-error"><i>' + response + '</i></p>');
			},

			// Add the error codes to the page
			success : function( response ) {

				// Load the template, pass the response to it, output on page
				var template = wp.template( 'wpna-fbia-import-status' );
				$( '#fbia_status' ).append( template( response ) );

				// If the import failed make sure the status tab is active
				if ( 'FAILED' == response.status ) {
					var index = $('#wpna-tabs a[href="#fbia_status"]').parent().index();
					$('#wpna-tabs').tabs('option', 'active', index);
					$('#wpna-tabs a[href="#fbia_status"]').addClass('wpna-error');
				} else if ( 'SUCCESS' == response.status ) {
					$('#wpna-tabs a[href="#fbia_status"]').addClass('wpna-success');
				}

			}
		});

	};

	/**
	 * Listens for an ajax response and shows a graph from the data
	 * All data transformation is handled in the backend
	 *
	 * @return null
	 */
	var setupInsights = function setupInsights() {

		wp.ajax.send( 'wpna-post-meta-box-facebook-stats', {

			// Send a nonce with the request
			data : {
				'_ajax_nonce': wpnaPostAnalytics.nonce,
				'post_id': wpnaPostAnalytics.post_id,
				'since': $('#wpna-fbia-analytics-since').val(),
				'until': $('#wpna-fbia-analytics-until').val(),
				'metric': $('#wpna-fbia-analytics-metric').val()
			},

			// If error fire notifications
			error : function( error ) {

				// Hide the loading spinner
				$('#fbia_analytics .wpna-loading-spinner').hide();

				// Remove any existing errors
				$('#fbia_analytics .wpna-error').remove();

				// Log the error
				/*
				 * @todo Conditionally show JS errors
				 */
				console.log( error );

				// Show a generic error message
				$( canvas ).hide().after( '<p class="wpna-error"><i>' + wpnaPostAnalytics.errorMessage + '</i></p>');
			},

			// Add the remove user from site confirm dialog box
			success : function( response ) {

				$('#fbia_analytics .wpna-loading-spinner').hide();

				var ctx = canvas;

				var myChart = new Chart(ctx, {
					type: 'bar',
					data: response,
					options: {
						tooltips: {
							mode: 'label',
							callbacks: {
								afterTitle: function() {
									// Create so this can be accessed later.
									window.WPNALabelTotal = 0;
								},
								label: function( tooltipItem, data ) {
									var corporation = data.datasets[ tooltipItem.datasetIndex ].label;
									var valor = data.datasets[ tooltipItem.datasetIndex ].data[ tooltipItem.index ];

									// If it is last dataset, calculate the total and store it.
									if ( tooltipItem.datasetIndex === data.datasets.length - 1 ) {
										// Loop through all datasets to get the actual total of the index
										for ( var i = 0; i < data.datasets.length; i++ ) {
											WPNALabelTotal += data.datasets[ i ].data[ tooltipItem.index ];
										}
									}

									return valor.toLocaleString();
								},
								footer: function() {
									return 'Total: ' + window.WPNALabelTotal.toLocaleString();
								}
							}
						},
						maintainAspectRatio: true,
						responsive: true,
						scales: {
							yAxes: [{
								stacked: true,
								ticks: {
									beginAtZero: true,
									suggestedMax: 10,
									callback: function (value) {
										if ( value % 1 === 0 ) {
											return value.toLocaleString();
										}
									}
								}
							}],
							xAxes: [{
								stacked: true,
								ticks: {
									beginAtZero: true
								}
							}]
						}
					}
				});

			}

		});

	};

	// Return the initialize function
	return initialize();

})( jQuery );
