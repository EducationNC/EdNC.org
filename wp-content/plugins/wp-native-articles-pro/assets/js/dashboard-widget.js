//
// All the JS for the dashboard widget
//

var WPNA_DASHBOARD_WIDGET = (function($) {

	var canvas = document.getElementById('wpna-facebook-analytics-chart');

	/**
	 * Our construct function that is run when the
	 * class is first initialized
	 * @return function
	 */
	var initialize = function() {
		// Run on document ready
		$(function() {
			setup();
		});
	};

	/**
	 * Listens for an ajax response and shows a graph from the data
	 * All data transformation is handled in the backend
	 *
	 * @return null
	 */
	var setup = function() {

		wp.ajax.send( 'wpna-dashboard-widget-facebook-stats', {

			// Send a nonce with the request
			data : {
				'_ajax_nonce' : wpnaDashboardWidget.nonce
			},

			// If error fire notifications
			error : function( error ) {

				// Hide the loading spinner
				$('.wpna-loading-spinner').hide();

				// Log the error
				/*
				 * @todo Conditionally show JS errors
				 */
				console.log( error );

				// Show a generic error message
				$( canvas ).hide().after( '<p class="wpna-error"><i>' + wpnaDashboardWidget.errorMessage + '</i></p>');
			},

			// Add the remove user from site confirm dialog box
			success : function( response ) {

				$('.wpna-loading-spinner').hide();

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
									beginAtZero:false
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
