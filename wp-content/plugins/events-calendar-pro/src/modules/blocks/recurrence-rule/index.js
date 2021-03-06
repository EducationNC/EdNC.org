/**
 * External dependencies
 */
import { DateTime } from '@moderntribe/events/icons';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import EventRecurring from './container';

/**
 * Module Code
 */

export default {
	id: 'event-pro-recurrence-rule',
	title: __( 'Rules', 'events-gutenberg' ),
	description: __(
		'Add recurrence to your event.',
		'events-gutenberg'
	),
	icon: <DateTime />,
	category: 'tribe-events',
	keywords: [ 'event', 'events-gutenberg', 'tribe' ],

	parent: [ 'tribe/event-pro-recurrence' ],

	supports: {
		html: false,
	},

	edit: EventRecurring,

	save( props ) {
		return null;
	},
};
