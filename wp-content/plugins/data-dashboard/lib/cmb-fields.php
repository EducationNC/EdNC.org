<?php

namespace Stringable\DataDash\CMB;

add_action( 'cmb2_init', __NAMESPACE__ . '\\add_metabox' );

function add_metabox() {

	$prefix = '_dd_';

	$cmb_data_viz = new_cmb2_box( array(
		'id'           => $prefix . 'data_viz',
		'title'        => 'Data Viz',
		'object_types' => array( 'data-viz' ),
		'context'      => 'normal',
		'priority'     => 'high',
	) );

	$cmb_data_viz->add_field( array(
		'name' => 'Type',
		'id' => $prefix . 'type',
		'type' => 'select',
		'show_option_none' => '(Select one)',
		'options' => array(
			'bar_chart' => 'Bar chart',
			'pie_chart' => 'Pie chart',
			'scatter_chart' => 'Scatter chart',
			'table' => 'Table',
			'cartodb_map' => 'CartoDB map',
			'text' => 'Text/image',
		),
	) );

	$cmb_data_viz->add_field( array(
		'name' => 'Data source',
		'id' => $prefix . 'data_source',
		'type' => 'text_url',
		'desc' => 'Paste sharable URL for Google Spreadsheet here.',
    'attributes' => array(
      'data-conditional-id' => $prefix . 'type',
      'data-conditional-value' => json_encode(array('bar_chart', 'pie_chart', 'scatter_chart', 'table'))
    )
	) );

	$cmb_data_viz->add_field( array(
		'name' => 'Additional options',
		'id' => $prefix . 'options',
		'type' => 'textarea_code',
		'desc' => 'Additional options for the chart in JavaScript object format.',
    'attributes' => array(
      'data-conditional-id' => $prefix . 'type',
      'data-conditional-value' => json_encode(array('bar_chart', 'pie_chart', 'scatter_chart', 'table'))
    )
	) );
	//
	// $cmb_data_viz->add_field( array(
	// 	'name' => 'Stacked bar chart',
	// 	'id' => $prefix . 'stacked',
	// 	'type' => 'checkbox',
	// 	'desc' => 'Check to display this chart as a stacked bar chart with related values placed together.',
  //   'attributes' => array(
  //     'data-conditional-id' => $prefix . 'type',
  //     'data-conditional-value' => json_encode(array('bar_chart'))
  //   )
	// ) );
	//
	// $cmb_data_viz->add_field( array(
	// 	'name' => 'Donut chart',
	// 	'id' => $prefix . 'donut',
	// 	'type' => 'checkbox',
	// 	'desc' => 'Check to display this chart as a donut chart. A donut chart is a pie chart with a hole cut out of the center.',
  //   'attributes' => array(
  //     'data-conditional-id' => $prefix . 'type',
  //     'data-conditional-value' => json_encode(array('pie_chart'))
  //   )
	// ) );
	//
	// $cmb_data_viz->add_field( array(
	//     'name' => 'Options',
	//     'desc' => 'Use the following options to customize the chart\'s appearance',
	//     'type' => 'title',
	//     'id'   => $prefix . 'options_title',
	// 		'after' => '<input type="hidden" data-conditional-id="_dd_type" data-conditional-value="["bar_chart","pie_chart","scatter_chart","table"]">',  // Fix for conditional display from https://github.com/jcchavezs/cmb2-conditionals/issues/6#issuecomment-176578817
	//     'attributes' => array(
	//       'data-conditional-id' => $prefix . 'type',
	//       'data-conditional-value' => json_encode(array('bar_chart', 'pie_chart', 'scatter_chart', 'table'))
	//     )
	// ) );
	//
	// $cmb_data_viz->add_field( array(
	// 	'name' => 'Height',
	// 	'id' => $prefix . 'height',
	// 	'type' => 'text_small',
	// 	'desc' => 'Height of chart in pixels (e.g., 400).',
	// 	'default' => '400',
  //   'attributes' => array(
  //     'data-conditional-id' => $prefix . 'type',
  //     'data-conditional-value' => json_encode(array('bar_chart', 'pie_chart', 'scatter_chart', 'table'))
  //   )
	// ) );
	//
	// $cmb_data_viz->add_field( array(
	// 	'name' => 'Point size',
	// 	'id' => $prefix . 'pointsize',
	// 	'type' => 'text_small',
	// 	'desc' => 'Diameter of data points, in pixels (e.g., 18).',
	// 	'default' => '7',
  //   'attributes' => array(
  //     'data-conditional-id' => $prefix . 'type',
  //     'data-conditional-value' => json_encode(array('scatter_chart'))
  //   )
	// ) );
	//
	// $cmb_data_viz->add_field( array(
	// 	'name' => 'Zoomable',
	// 	'id' => $prefix . 'zoom',
	// 	'type' => 'checkbox',
	// 	'desc' => 'Check to allow users to click and drag to zoom the chart.',
  //   'attributes' => array(
  //     'data-conditional-id' => $prefix . 'type',
  //     'data-conditional-value' => json_encode(array('bar_chart', 'scatter_chart'))
  //   )
	// ) );
	//
	// $cmb_data_viz->add_field( array(
	// 	'name' => 'Show legend',
	// 	'id' => $prefix . 'legend',
	// 	'type' => 'checkbox',
	// 	'desc' => 'Check to display legend on right side of chart.',
  //   'attributes' => array(
  //     'data-conditional-id' => $prefix . 'type',
  //     'data-conditional-value' => json_encode(array('bar_chart', 'pie_chart', 'scatter_chart'))
  //   )
	// ) );
	//
	// $cmb_data_viz->add_field( array(
	// 	'name' => 'Freeze first column',
	// 	'id' => $prefix . 'freezecol',
	// 	'type' => 'checkbox',
	// 	'desc' => 'Check to freeze first column on horizontal scroll.',
  //   'attributes' => array(
  //     'data-conditional-id' => $prefix . 'type',
  //     'data-conditional-value' => json_encode(array('table'))
  //   )
	// ) );
	//
	// $cmb_data_viz->add_field( array(
	// 	'name' => 'Tooltips',
	// 	'id' => $prefix . 'tooltip',
	// 	'type' => 'Select',
	// 	'desc' => 'Customize format of the tooltips for plotted values.',
	// 	'show_option_none' => 'Default',
	// 	'options' => array(
	// 		'none' => 'Hide',
	// 		'isHtml' => 'Custom'
	// 	),
  //   'attributes' => array(
  //     'data-conditional-id' => $prefix . 'type',
  //     'data-conditional-value' => json_encode(array('bar_chart', 'pie_chart', 'scatter_chart'))
  //   )
	// ) );
	//
	// $cmb_data_viz->add_field( array(
	// 	'name' => 'Annotations format',
	// 	'id' => $prefix . 'annotations',
	// 	'type' => 'select',
	// 	'desc' => 'Customize format of the data annotations for plotted values.',
	// 	'show_option_none' => 'None',
	// 	'options' => array(
	// 		'small' => 'Small',
	// 		'medium' => 'Medium',
	// 		'large' => 'Large'
	// 	),
  //   'attributes' => array(
  //     'data-conditional-id' => $prefix . 'type',
  //     'data-conditional-value' => json_encode(array('bar_chart', 'pie_chart', 'scatter_chart'))
  //   )
	// ) );
	//
	// $cmb_data_viz->add_field( array(
	//     'name' => 'Colors',
	//     'id'   => $prefix . 'colors',
	//     'type' => 'colorpicker',
	//     'desc' => 'Select custom colors to use for chart visualizations. Click "add row" button to add more colors.',
	//     'repeatable' => true,
	// 		'after' => '<input type="hidden" data-conditional-id="_dd_type" data-conditional-value="["bar_chart","pie_chart","scatter_chart"]">',  // Fix for conditional display from https://github.com/jcchavezs/cmb2-conditionals/issues/6#issuecomment-176578817
	//     'attributes' => array(
	//       'data-conditional-id' => $prefix . 'type',
	//       'data-conditional-value' => json_encode(array('bar_chart', 'pie_chart', 'scatter_chart'))
	//     )
	// ) );
	//
	// $cmb_data_viz->add_field( array(
	//     'name' => 'Axes',
	//     'desc' => 'Configure the horizontal and vertical axes.',
	//     'type' => 'title',
	//     'id'   => $prefix . 'axes',
	// 		'after' => '<input type="hidden" data-conditional-id="_dd_type" data-conditional-value="["bar_chart","scatter_chart"]">',  // Fix for conditional display from https://github.com/jcchavezs/cmb2-conditionals/issues/6#issuecomment-176578817
	//     'attributes' => array(
	//       'data-conditional-id' => $prefix . 'type',
	//       'data-conditional-value' => json_encode(array('bar_chart', 'scatter_chart'))
	//     )
	// ) );
	//
	// $cmb_data_viz->add_field( array(
	// 	'name' => 'Horizontal axis - show all labels',
	// 	'id' => $prefix . 'haxis_labels',
	// 	'type' => 'checkbox',
	// 	'desc' => 'Check to force display horizontal axis labels for each discrete point.',
  //   'attributes' => array(
  //     'data-conditional-id' => $prefix . 'type',
  //     'data-conditional-value' => json_encode(array('bar_chart', 'scatter_chart'))
  //   )
	// ) );
	//
	// $cmb_data_viz->add_field( array(
	// 	'name' => 'Horizontal axis - slanted text',
	// 	'id' => $prefix . 'haxis_slanted',
	// 	'type' => 'select',
	// 	'desc' => 'Select how the horizontal axis abels should be displayed.',
	// 	'show_option_none' => 'default (automatic)',
	// 	'options' => array(
	// 		'false' => 'Horizontal',
	// 		'diagonal' => 'Diagonal',
	// 		'true' => 'Vertical'
	// 	),
  //   'attributes' => array(
  //     'data-conditional-id' => $prefix . 'type',
  //     'data-conditional-value' => json_encode(array('bar_chart', 'scatter_chart'))
  //   )
	// ) );
	//
	// $cmb_data_viz->add_field( array(
	// 	'name' => 'Horizontal axis - gridlines',
	// 	'id' => $prefix . 'haxis_gridlines',
	// 	'type' => 'text_small',
	// 	'desc' => 'OPTIONAL. Number of vertical gridlines to display (e.g., 5).',
  //   'attributes' => array(
  //     'data-conditional-id' => $prefix . 'type',
  //     'data-conditional-value' => json_encode(array('scatter_chart'))
  //   )
	// ) );
	//
	// $cmb_data_viz->add_field( array(
	// 	'name' => 'Horizontal axis - format',
	// 	'id' => $prefix . 'haxis_format',
	// 	'type' => 'select',
	// 	'desc' => 'Customize horizontal axis number format.',
	// 	'show_option_none' => 'default (automatic)',
	// 	'options' => array(
	// 		'none' => 'None (e.g., 8000000)',
	// 		'decimal' => 'Decimal (e.g., 8,000,000)',
	// 		'currency' => 'Currency (e.g., $8,000,000)',
	// 		'short' => 'Short (e.g., 8M)',
	// 		'long' => 'Long (e.g., 8 million)',
	// 		'#,###%' => 'Percent (e.g., 50%)'
	// 	),
  //   'attributes' => array(
  //     'data-conditional-id' => $prefix . 'type',
  //     'data-conditional-value' => json_encode(array('bar_chart', 'scatter_chart'))
  //   )
	// ) );
	//
	// $cmb_data_viz->add_field( array(
	// 	'name' => 'Vertical axis - format',
	// 	'id' => $prefix . 'vaxis',
	// 	'type' => 'select',
	// 	'desc' => 'Customize vertical axis format.',
	// 	'show_option_none' => 'default (automatic)',
	// 	'options' => array(
	// 		'none' => 'None (e.g., 8000000)',
	// 		'decimal' => 'Decimal (e.g., 8,000,000)',
	// 		'currency' => 'Currency (e.g., $8,000,000)',
	// 		'short' => 'Short (e.g., 8M)',
	// 		'long' => 'Long (e.g., 8 million)',
	// 		'#,###%' => 'Percent (e.g., 50%)'
	// 	),
  //   'attributes' => array(
  //     'data-conditional-id' => $prefix . 'type',
  //     'data-conditional-value' => json_encode(array('bar_chart', 'scatter_chart'))
  //   )
	// ) );
	//
	// $cmb_data_viz->add_field( array(
	//     'name' => 'Chart area (Optional)',
	//     'desc' => 'Optionally configure the placement and size of the chart area (where the chart itself is drawn, excluding axis and legends)',
	//     'type' => 'title',
	//     'id'   => $prefix . 'chartarea_title',
	// 		'after' => '<input type="hidden" data-conditional-id="_dd_type" data-conditional-value="["bar_chart","pie_chart","scatter_chart"]">',  // Fix for conditional display from https://github.com/jcchavezs/cmb2-conditionals/issues/6#issuecomment-176578817
	//     'attributes' => array(
	//       'data-conditional-id' => $prefix . 'type',
	//       'data-conditional-value' => json_encode(array('bar_chart', 'pie_chart', 'scatter_chart'))
	//     )
	// ) );
	//
	// $cmb_data_viz->add_field( array(
	// 	'name' => 'Chart area - left',
	// 	'id' => $prefix . 'chartarea_left',
	// 	'type' => 'text_small',
	// 	'desc' => 'How far to draw the chart from the left border, in pixels (e.g., 40). If this field is left blank, the layout will be automatically generated.',
  //   'attributes' => array(
  //     'data-conditional-id' => $prefix . 'type',
  //     'data-conditional-value' => json_encode(array('bar_chart', 'pie_chart', 'scatter_chart'))
  //   )
	// ) );
	//
	// $cmb_data_viz->add_field( array(
	// 	'name' => 'Chart area - top',
	// 	'id' => $prefix . 'chartarea_top',
	// 	'type' => 'text_small',
	// 	'desc' => 'How far to draw the chart from the top border, in pixels (e.g., 10). If this field is left blank, the layout will be automatically generated.',
  //   'attributes' => array(
  //     'data-conditional-id' => $prefix . 'type',
  //     'data-conditional-value' => json_encode(array('bar_chart', 'pie_chart', 'scatter_chart'))
  //   )
	// ) );
	//
	// $cmb_data_viz->add_field( array(
	// 	'name' => 'Chart area - width',
	// 	'id' => $prefix . 'chartarea_width',
	// 	'type' => 'text_small',
	// 	'desc' => 'How wide to draw chart inside chart area, as a percentage (e.g., 85%). If this field is left blank, the layout will be automatically generated.',
  //   'attributes' => array(
  //     'data-conditional-id' => $prefix . 'type',
  //     'data-conditional-value' => json_encode(array('bar_chart', 'pie_chart', 'scatter_chart'))
  //   )
	// ) );
	//
	// $cmb_data_viz->add_field( array(
	// 	'name' => 'Chart area - height',
	// 	'id' => $prefix . 'chartarea_height',
	// 	'type' => 'text_small',
	// 	'desc' => 'How tall to draw chart inside chart area, as a percentage (e.g., 80%). If this field is left blank, the layout will be automatically generated.',
  //   'attributes' => array(
  //     'data-conditional-id' => $prefix . 'type',
  //     'data-conditional-value' => json_encode(array('bar_chart', 'pie_chart', 'scatter_chart'))
  //   )
	// ) );
	//
	// $trendines = $cmb_data_viz->add_field( array(
	//     'id'          => $prefix . 'trendlines',
	//     'type'        => 'group',
	//     'options'     => array(
	//         'group_title'   => 'Trendline {#}', // since version 1.1.4, {#} gets replaced by row number
	//         'add_button'    => 'Add Another Trendline',
	//         'remove_button' => 'Remove Trendline',
	//         'closed'     => true
	//     ),
	// 		'before_group_row' => '<input type="hidden" data-conditional-id="_dd_type" data-conditional-value="scatter_chart">',  // Fix for conditional display from https://github.com/jcchavezs/cmb2-conditionals/issues/6#issuecomment-176578817
	//     'attributes' => array(
	//       'data-conditional-id' => $prefix . 'type',
	//       'data-conditional-value' => json_encode(array('scatter_chart'))
	//     )
	// ) );
	//
	// $cmb_data_viz->add_group_field( $trendines, array(
	// 	'name' => 'Trendline',
	// 	'id' => 'trendline',
	// 	'type' => 'select',
	// 	'desc' => 'Select type of trendline to calculate and display.',
	// 	'show_option_none' => 'None',
	// 	'options' => array(
	// 		'linear' => 'Linear',
	// 		'exponential' => 'Exponential',
	// 		'polynomial' => 'Polynomial'
	// 	),
	// 	'attributes' => array(
	// 		'data-conditional-id' => $prefix . 'type',
	// 		'data-conditional-value' => json_encode(array('scatter_chart'))
	// 	)
	// ) );
	//
	// $cmb_data_viz->add_group_field( $trendines, array(
	// 	'name' => 'Trendline color',
	// 	'id' => 'trendline_color',
	// 	'type' => 'colorpicker',
	// 	'desc' => 'Select color to display trendline.',
	// 	'default' => '#AAAAAA',
  //   'attributes' => array(
	// 		'data-conditional-id' => $prefix . 'type',
	// 		'data-conditional-value' => json_encode(array('scatter_chart'))
  //   )
	// ) );
	//
	// $cmb_data_viz->add_group_field( $trendines, array(
	// 	'name' => 'Trendline width',
	// 	'id' => 'trendline_width',
	// 	'type' => 'text_small',
	// 	'desc' => 'Width of trendline, in pixels (e.g., 7).',
  //   'attributes' => array(
	// 		'data-conditional-id' => $prefix . 'type',
	// 		'data-conditional-value' => json_encode(array('scatter_chart'))
  //   )
	// ) );

	$cmb_data_viz->add_field( array(
		'name' => 'Columns',
		'id' => $prefix . 'columns',
		'type' => 'textarea_code',
		'desc' => 'Configurations for the view\'s columns in JavaScript object format.',
    'attributes' => array(
      'data-conditional-id' => $prefix . 'type',
      'data-conditional-value' => json_encode(array('bar_chart', 'pie_chart', 'scatter_chart', 'table'))
    )
	) );

	$cmb_data_viz->add_field( array(
		'name' => 'CartoDB URL',
		'id' => $prefix . 'cartodb_url',
		'type' => 'oembed',
    'attributes' => array(
      'data-conditional-id' => $prefix . 'type',
      'data-conditional-value' => 'cartodb_map'
    )
	) );

	$cmb_data_viz->add_field( array(
		'name' => 'Static Map Image',
		'id' => $prefix . 'static_map_image',
		'type' => 'file',
		'desc' => 'Export static map image from CartoDB.',
    'attributes' => array(
      'data-conditional-id' => $prefix . 'type',
      'data-conditional-value' => 'cartodb_map'
    )
	) );

	$cmb_data_viz->add_field( array(
		'name' => 'Data',
		'id' => $prefix . 'text-based_data',
		'type' => 'wysiwyg',
		'options' => array(
			'wpautop' => true,
			'media_buttons' => true,
			'teeny' => false,
      'editor_height' => 5,
		),
    'after_field' => '<input type="hidden" data-conditional-id="_dd_type" data-conditional-value="text">',  // Fix for conditional display from https://github.com/jcchavezs/cmb2-conditionals/issues/6#issuecomment-176578817
    'attributes' => array(
      'data-conditional-id' => $prefix . 'type',
      'data-conditional-value' => 'text'
    )
	) );

	$cmb_data_viz->add_field( array(
		'name' => 'Notes',
		'id' => $prefix . 'notes',
		'type' => 'wysiwyg',
		'options' => array(
			'wpautop' => true,
			'media_buttons' => false,
			'teeny' => true,
      'editor_height' => 5,
		),
	) );

	$cmb_data_viz->add_field( array(
		'name' => 'Source',
		'id' => $prefix . 'source',
		'type' => 'wysiwyg',
		'options' => array(
			'wpautop' => true,
			'media_buttons' => false,
			'teeny' => true,
      'editor_height' => 5,
		),
	) );

	$cmb_data = new_cmb2_box( array(
		'id'           => 'data_dashboard',
		'title'        => 'Data Dashboard',
		'object_types' => array( 'data' ),
		'context'      => 'normal',
		'priority'     => 'high',
		'show_names'   => false,
	) );

	$cmb_data->add_field( array(
		'name'    => 'Data Visualizations',
		'desc'    => 'Drag posts from the left column to the right column to add them to this section. You may rearrange the order of the posts in the right column by dragging and dropping.',
		'id'      => $prefix . 'data_visualizations',
		'type'    => 'custom_attached_posts',
		'options' => array(
			'show_thumbnails' => false,
			'filter_boxes'    => true,
			'query_args'      => array( 'posts_per_page' => -1, 'post_type' => 'data-viz' ),
		)
	) );

	$cmb_data->add_field( array(
		'name' => 'About Text',
		'id' => $prefix . 'intro',
		'type' => 'wysiwyg',
		'options' => array(
			'wpautop' => true,
			'media_buttons' => false,
			'teeny' => true,
      'editor_height' => 8,
		),
    'show_names' => true
	) );
}
