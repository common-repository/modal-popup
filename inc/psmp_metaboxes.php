<?php
//Metaboxes Creation
function psmp_register_metabox() {
	$prefix = '_psmp_modal_';

	$cmb_group = new_cmb2_box( array(
		'id'           => $prefix . 'metabox',
		'title'        => __( 'Popup editor', 'psmp_modal' ),
		'object_types' => array( 'psmp_modal' ),
	) );

	$cmb_group->add_field( array(
		'name'				=> 'Popup content',
		'id'				=> $prefix . 'heading_content',
		'type'				=> 'title',
		'row_classes' => 'de_hundred de_heading',
	) );

	$cmb_group->add_field( array(
		'name'				=> 'Header/title',
		'id'				=> $prefix . 'header',
		'type'				=> 'text',
		'attributes' => array( 'placeholder' => "eg. Special offer!" ),
		'sanitization_cb'	=> 'psmp_html_allowed_sani_cb',
		'row_classes' => 'de_twentyfive de_text de_input',
	) );

	$cmb_group->add_field( array(
		'name' 				=> 'Subheader/subtitle',
		'id' 				=> $prefix . 'subheader',
		'type'   			=> 'text',
		'attributes' => array( 'placeholder' => "eg. Discover our new products!" ),
		'sanitization_cb' 	=> 'psmp_html_allowed_sani_cb',
		'row_classes' => 'de_seventyfive de_text de_input',
	) );

	$cmb_group->add_field( array(
		'name' 		=> __( 'Primary image/logo', 'psmp_modal' ),
		'desc' => "Shows in the layout.",
		'id'   		=> $prefix . 'image',
		'type' 		=> 'file',
		'options' 	=> array(
			'url' 	=> false
		),
		'row_classes' => 'de_first de_fifty de_upload de_text de_input',
	) );
	
	$cmb_group->add_field( array(
		'name' 		=> __( 'Background image', 'psmp_modal' ),
		'desc' => "Background image for your popup.",
		'id'   		=> $prefix . 'image_background',
		'type' 		=> 'file',
		'options' 	=> array(
			'url' 	=> false
		),
		'attributes' 	=> array(
			'data-conditional-id' 		=> $prefix . 'layout',
			'data-conditional-value' 	=> json_encode(array('psmp_full_picture'))
		),
		'row_classes' => 'de_fifty de_upload de_text de_input',
	) );

	$cmb_group->add_field( array(
		'name' 	=> 'Additional content',
		'desc' 	=> 'Custom content for your popup, this gives you more control (totally optional).',
		'id' 	=> $prefix . 'content',
		'type' 	=> 'wysiwyg',
		'options' => array(
			'textarea_rows' => 7,
		),
		'row_classes' => 'de_first de_seventyfive de_textarea de_input',
	) );
	
	$cmb_group->add_field( array(
		'name' 	=> 'Add buttons',
		'desc' 	=> 'You can add buttons to your popup\'s content by using this simple shortcode:<br/><br/><strong style="font-size:12px; color:whitesmoke; background:#333; padding:5px 8px; display:block;">[psmp_button url=\'http://site.com\' text=\'View site\']</strong><br/>The result will be similar to this:<br/><a class="psmp_button">Sample button</a>',
		'id' 	=> $prefix . 'info_ontent',
		'type' 	=> 'title',
		
		'row_classes' => 'de_twentyfive de_info de_text',
	) );

	$cmb_group->add_field( array(
		'name'				=> 'Popup style',
		'id'				=> $prefix . 'heading_style',
		'type'				=> 'title',
		'row_classes' => 'de_hundred de_heading',
	) );
	
		$cmb_group->add_field( array(
		'name' 		=> __( 'Primary Color', 'psmp_modal' ),
		'id'   		=> $prefix . 'first_color',
		'type' 		=> 'colorpicker',
		'options' 	=> array(
			'url' 	=> false
		),
		'attributes' 	=> array(
			'data-conditional-id' 		=> $prefix . 'layout',
			'data-conditional-value' 	=> json_encode(array('psmp_half_colored'))
		),
		'row_classes' => 'de_first de_twentyfive de_upload de_input',
	) );
	
	$cmb_group->add_field( array(
		'name' 		=> __( 'UI Color', 'psmp_modal' ),
		'desc' 	=> 'Used for buttons in your popup.',
		'id'   		=> $prefix . 'ui_color',
		'type' 		=> 'colorpicker',
		'options' 	=> array(
			'url' 	=> false
		),
		'row_classes' => 'de_twentyfive de_upload de_input',
	) );
	
	$cmb_group->add_field( array(
		'name'             => 'Modal contrast',
		'id'               => $prefix . 'contrast',
		'desc' 	=> 'Changes the color of the header and subheader texts.',
		'type'             => 'radio_inline',
		'default_cb'          => 'psmp_contrast_bright',
		'options'          => array(
			'psmp_contrast_bright' 			=> __( '<img src="'.plugins_url('../img/bright.png', __FILE__).'"/>', 'cmb2' ),
			'psmp_contrast_dark'   			=> __( '<img src="'.plugins_url('../img/dark.png', __FILE__).'"/>', 'cmb2' ),
		),
		'attributes' 	=> array(
			'data-conditional-id' 		=> $prefix . 'layout',
			'data-conditional-value' 	=> json_encode(array('psmp_full_picture','psmp_half_colored'))
		),
		'row_classes' => 'de_fifty de_text de_input',
	) );

	$cmb_group->add_field( array(
		'name'             => 'Layout',
		'id'               => $prefix . 'layout',
		'type'             => 'radio_inline',
		'default_cb'          => 'psmp_light',
		'options'          => array(
			'psmp_full_picture'		=> __( '<img src="'.plugins_url('../img/full_picture.png', __FILE__).'"/>', 'cmb2' ),
			'psmp_half_colored'		=> __( '<img src="'.plugins_url('../img/half_colored.png', __FILE__).'"/>', 'cmb2' ),
			'psmp_light'		=> __( '<img src="'.plugins_url('../img/light.png', __FILE__).'"/>', 'cmb2' ),
		),
		'row_classes' => 'de_first de_hundred de_select de_text de_input',
	) );

	$cmb_group->add_field( array(
		'name'             => 'Size',
		'id'               => $prefix . 'size',
		'desc' 	=> 'Select a width for your popup.',
		'type'             => 'select',
		'show_option_none' => false,
		'default_cb'          => 'psmp_medium',
		'options'          => array(
			'psmp_small'   	=> __( 'Small', 'cmb2' ),
			'psmp_medium'  => __( 'Medium', 'cmb2' ),
			'psmp_large'   	=> __( 'Large', 'cmb2' ),
		),
		'row_classes' => 'de_first de_twentyfive de_select de_text de_input',
	) );


	
	$cmb_group->add_field( array(
		'name'				=> 'Popup closing',
		'id'				=> $prefix . 'heading_close',
		'type'				=> 'title',
		'row_classes' => 'de_hundred de_heading',
	) );
	
	$cmb_group->add_field( array(
		'name' 		=> 'Cookie duration',
		'desc' 		=> 'Amount of days before the popup comes back when it was closed.',
		'id' 		=> $prefix . 'expired_value',
		'type'    	=> 'text',
		'default_cb' 	=> '0',
		'row_classes' => 'de_first de_fifty de_text de_input',
	) );

	
	$cmb_group->add_field( array(
		'name'				=> 'Popup opening',
		'id'				=> $prefix . 'heading_open',
		'type'				=> 'title',
		'row_classes' => 'de_hundred de_heading',
	) );

	$cmb_group->add_field( array(
		'name' 			=> 'Delay in seconds',
		'desc' 				=> __( 'Set a delay before opening.', 'psmp_modal' ),
		'id' 			=> $prefix . 'delay_value',
		'type'    		=> 'text',
		'default_cb' 		=> '3',
		'row_classes' => 'de_twentyfive de_text de_input',
	) );

	
	
	$cmb_group->add_field( array(
		'name'				=> '',
		'id'				=> $prefix . 'heading_oths',
		'type'				=> 'title',
		'row_classes' => 'de_hundred de_heading',
	) );
	
	$cmb_group2 = new_cmb2_box( array(
		'id'           => $prefix . 'metabox_settings',
		'title'        => __( 'Popup settings', 'psmp_modal' ),
		'object_types' => array( 'psmp_modal' ),
		'context'    => 'side',
		'priority'   => 'high',
	) );
	
	$cmb_group2->add_field( array(
		'name'             => 'Page restrictions',
		'id'               => $prefix . 'page_restrict',
		'desc' 				=> 'Choose where the modal will be displayed.',
		'type'             => 'select',
		'default_cb'          => 'all',
		'options'          => array(
			'all' 			=> __( 'Everywhere', 'cmb2' ),
			'posts'   			=> __( 'All posts', 'cmb2' ),
			'pages'   			=> __( 'All pages', 'cmb2' ),
		),
		'row_classes' => 'de_first de_hundred de_select de_text_side de_input',
	) );
	
	$cmb_group2->add_field( array(
		'name'				=> 'Others',
		'id'				=> $prefix . 'heading_others',
		'type'				=> 'title',
		'row_classes' => 'de_hundred de_heading',
	) );
	
	$cmb_group2->add_field( array(
		'name'             => 'Quick enable/disable',
		'id'               => $prefix . 'active',
		'desc' 				=> 'Set to inactive to completely disable this popup.',
		'type'             => 'select',
		'default_cb'          => 'yes',
		'options'          => array(
			'yes' 			=> __( 'Active', 'cmb2' ),
			'no'   			=> __( 'Inactive', 'cmb2' ),
		),
		'row_classes' => 'de_first de_hundred de_select de_text_side de_input',
	) );
	
	$cmb_group2->add_field( array(
		'name'             => 'Force original fonts',
		'id'               => $prefix . 'force_fonts',
		'desc' 				=> 'Use plugin\'s font instead of your theme\'s.',
		'type'             => 'select',
		'default_cb'          => 'no',
		'options'          => array(
			'yes' 			=> __( 'Yes', 'cmb2' ),
			'no'   			=> __( 'No', 'cmb2' ),
		),
		'row_classes' => 'de_first de_hundred de_select de_text_side de_input',
	) );
	
	$cmb_group2->add_field( array(
		'name'				=> '',
		'id'				=> $prefix . 'heset_oths',
		'type'				=> 'title',
		'row_classes' => 'de_hundred de_heading',
	) );
	
	// PRO version
    $pro_group = new_cmb2_box( array(
        'id' => $prefix . 'pro_mb',
        'title' => '<span style="font-weight:400;">Upgrade to <strong>PRO version</strong></span>',
        'object_types' => array( 'psmp_modal' ),
       'context' => 'side',
        'priority' => 'low',
        'row_classes' => 'de_hundred de_heading',
    ));
	
	$pro_group->add_field( array(
		'name' => '',
			'desc' => '<div><span class="dashicons dashicons-yes"></span> More layouts<br/><span class="dashicons dashicons-yes"></span> Page restrictions<br/><span class="dashicons dashicons-yes"></span> Click to Open<br/><span class="dashicons dashicons-yes"></span> Auto Close<br/><span class="dashicons dashicons-yes"></span> Screen Restrictions<br/><span class="dashicons dashicons-yes"></span> User Restrictions<br/><span class="dashicons dashicons-arrow-right"></span> And more...<br/><br/><a style="display:inline-block; background:#33b690; padding:8px 25px 8px; border-bottom:3px solid #33a583; border-radius:3px; color:white;" target="_blank" href="http://pluginlyspeaking.com/plugins/modal-popup/">See all PRO features</a><br/><span style="display:block;margin-top:14px; font-size:13px; color:#0073AA; line-height:20px;"><span class="dashicons dashicons-tickets"></span> Code <strong>MP10OFF</strong> (10% OFF)</span></div>',
			'id'   => $prefix . 'pro_desc',
			'type' => 'title',
			'row_classes' => 'de_hundred de_info de_info_side',
	));
	
	
}

//Accept html tag in CMB2 fields
function psmp_html_allowed_sani_cb($content) {
	return is_array( $content ) ? array_map( 'wp_kses_post', $content ) : wp_kses_post( $content );
}

?>
