<?php
/**
 * Plugin Name: Modal Popup
 * Plugin URI: http://pluginlyspeaking.com/plugins/modal-popup/
 * Description: Modal Popup is the easiest and fastest way to add a popup to your site.
 * Author: PluginlySpeaking
 * Version: 1.2.1
 * Author URI: http://www.pluginlyspeaking.com
 * License: GPL2
 */


require_once dirname( __FILE__ ) . '/inc/class-tgm-plugin-activation.php';


// CMB2 required for Modal Popup
add_action( 'tgmpa_register', 'psmp_register_required_plugins' );
function psmp_register_required_plugins() {
	$plugins = array(
		array(
			'name'      => 'CMB2',
			'slug'      => 'cmb2',
			'required'  => true,
		),
	);

	$config = array(
		'id'           => 'modal-popup',
		'default_path' => '',
		'menu'         => 'tgmpa-install-plugins',
		'capability'   => 'manage_options',
		'has_notices'  => true,
		'dismissable'  => false,
		'dismiss_msg'  => 'Without CMB2, Modal Popup won\'t work.',
		'is_automatic' => true,
		'message'      => '',
	);

	tgmpa( $plugins, $config );
}


// Add CMB2 librairies
add_action( 'admin_init', 'psmp_add_cmb2' );
function psmp_add_cmb2() {
	if ( is_plugin_active( WP_PLUGIN_DIR . '/cmb2/init.php' ) ) {
		require_once WP_PLUGIN_DIR . '/cmb2/init.php';
	}
}

// Add CMB2 conditionals
include_once( dirname( __FILE__ ) . '/inc/cmb2-conditionals/cmb2-conditionals.php' );

// Add the metaboxes (CMB2)
add_action( 'cmb2_init', 'psmp_register_metabox' );
require_once('inc/psmp_metaboxes.php');

// Add PHP color tool
require_once('inc/color_brightness.php');

// Check for the PRO version
add_action( 'admin_init', 'psmp_free_pro_check' );
function psmp_free_pro_check() {
    if (is_plugin_active('modal-popup-pro/modal-popup-pro.php')) {

        function my_admin_notice(){
        echo '<div class="updated">
                <p><strong>PRO</strong> version is activated.</p>
              </div>';
        }
        add_action('admin_notices', 'my_admin_notice');

        deactivate_plugins(__FILE__);
    }
}

// Enqueue scripts & styles
add_action( 'wp_enqueue_scripts', 'psmp_add_script' );
function psmp_add_script() {
	wp_enqueue_style( 'psmp_css', plugins_url('css/psmp.css', __FILE__));
	wp_enqueue_script('jquery');
	wp_enqueue_script( 'psmp_js', plugins_url('js/psmp.js', __FILE__), array( 'jquery' ));
	wp_enqueue_script( 'psmp_cookie_js', plugins_url('inc/js.cookie.js', __FILE__), array( 'jquery' ));
}

// Enqueue admin styles
add_action( 'admin_enqueue_scripts', 'add_admin_psmp_style' );
function add_admin_psmp_style() {
	wp_enqueue_style( 'psmp', plugins_url('css/psmp_admin.css', __FILE__));
}


// Create modal post type
add_action( 'init', 'psmp_create_type' );
function psmp_create_type() {
	register_post_type( 'psmp_modal',
		array(
			'labels' => array(
				'name' => 'Popups',
				'singular_name' => 'Popup'
			),
			'public' => true,
			'has_archive' => false,
			'hierarchical' => false,
			'supports'           => array( 'title' ),
			'menu_icon'    => 'dashicons-megaphone',
		)
	);
}


// Adjust visible element on the admin panel
add_action( 'admin_head-post-new.php', 'psmp_admin_css' );
add_action( 'admin_head-post.php', 'psmp_admin_css' );
function psmp_admin_css() {
    global $post_type;
    $post_types = array(
			'psmp_modal',
		);
    if(in_array($post_type, $post_types))
		echo '<style type="text/css">#edit-slug-box, #post-preview, #view-post-btn{display: none;}</style>';
}

// Remove preview link on the admin panel
add_filter( 'post_row_actions', 'psmp_remove_view_link' );
function psmp_remove_view_link( $action ) {
    unset ($action['view']);
    return $action;
}

// Fill Active columns
add_action( 'manage_psmp_modal_posts_custom_column' , 'psmp_custom_columns_active', 10, 2 );
function psmp_custom_columns_active( $column, $post_id ) {
    switch ( $column ) {
	case 'active' :
		global $post;
		$post_id = '' ;
		$post_id = $post->ID;
		$prefix = '_psmp_modal_';
		global $wp;
		$current_url = home_url(add_query_arg(array(),$wp->request));
		$current_url = $current_url.'?post_type=psmp_modal';
		
		$active_modal = get_post_meta( get_the_id(), $prefix . 'active', true );
		if ($active_modal == 'yes')
			$active = '<a href="'.$current_url.'&postid='.$post_id.'&next=no"><img src="'.plugins_url('img/modal_on.png', __FILE__).'"/></a>';
	    else
			$active = '<a href="'.$current_url.'&postid='.$post_id.'&next=yes"><img src="'.plugins_url('img/modal_off.png', __FILE__).'"/></a>';
		echo $active;
	    break;
    }
	
	if(isset($_GET['postid']) && isset($_GET['next'])) {
		update_post_meta($_GET['postid'], '_psmp_modal_active', $_GET['next']);
	}
}



// Create the Active columns
add_filter('manage_psmp_modal_posts_columns' , 'psmp_add_columns_active');
function psmp_add_columns_active($columns) {
  return array_merge($columns, array('active' => __('Active'),));
}


// Parsing function of a TinyMCE field
function psmp_get_wysiwyg_output( $meta_key, $post_id = 0 ) {
    global $wp_embed;
    $post_id = $post_id ? $post_id : get_the_id();
    $content = get_post_meta( $post_id, $meta_key, 1 );
    $content = $wp_embed->autoembed( $content );
    $content = $wp_embed->run_shortcode( $content );
    $content = do_shortcode( $content );
    $content = wpautop( $content );
    return $content;
}

// Create modal in footer
add_action( 'wp_footer', 'psmp_add_content' );
function psmp_add_content() {

	global $post;
	$page_id = $post->ID;
	$args = array('post_type' => 'psmp_modal', 'numberposts'=>-1);
	$custom_posts = get_posts($args);
	$output = '';

	foreach($custom_posts as $post) : setup_postdata($post);
	
		$prefix = '_psmp_modal_';
		$active_modal = get_post_meta( get_the_id(), $prefix . 'active', true );
		
		$page_restrict = get_post_meta( get_the_id(), $prefix . 'page_restrict', true );
		$check = array();
		$all_posts = get_posts();
		$all_pages = get_pages(); 

		switch ($page_restrict) {
			case "all":
				if ( $all_posts ) {
					foreach ( $all_posts as $post_check ) {
						$check[] = $post_check->ID;
					}
				}
				if ( $all_pages ) {
					foreach ( $all_pages as $page_check ) {
					  $check[] = $page_check->ID;
					}
				}
				break;
			case "posts":
				if ( $all_posts ) {
					foreach ( $all_posts as $post_check ) {
						$check[] = $post_check->ID;
					}
				}
				break;
			case "pages":
				if ( $all_pages ) {
					foreach ( $all_pages as $page_check ) {
					  $check[] = $page_check->ID;
					}
				}
				break;
			default:
				if ( $all_posts ) {
					foreach ( $all_posts as $post_check ) {
						$check[] = $post_check->ID;
					}
				}
				if ( $all_pages ) {
					foreach ( $all_pages as $page_check ) {
					  $check[] = $page_check->ID;
					}
				}
				break;
		}
		
		if ($active_modal == 'yes' && in_array($page_id, $check, false))
		{
			$postid = get_the_ID();
			
			// Content and style of the modal
			$header = get_post_meta( get_the_id(), $prefix . 'header', true );
			$subheader = get_post_meta( get_the_id(), $prefix . 'subheader', true );
			$image = get_post_meta( get_the_id(), $prefix . 'image', true );
			$content = get_post_meta( get_the_id(), $prefix . 'content', true );
			$content = do_shortcode($content);
			$content = wpautop($content);
			$size = get_post_meta( get_the_id(), $prefix . 'size', true );
			$layout = get_post_meta( get_the_id(), $prefix . 'layout', true );
			$ui_color = get_post_meta( get_the_id(), $prefix . 'ui_color', true );
			$force_fonts = get_post_meta( get_the_id(), $prefix . 'force_fonts', true );
			$fonts_class = '';
			if($force_fonts == 'yes')
				$fonts_class = 'psmp_force_font';

			// Closable using button
			$closable = get_post_meta( get_the_id(), $prefix . 'closable', true );
			if($closable == '')
				$closable = 'yes';
			$closable_class = '';
			if($closable == 'yes')
				$closable_class = 'psmp_button_closable';

			// Create wrapper classes
			$ps_modal_class = "psmp_modal ".$fonts_class." ".$layout." ".$size." ".$closable_class."" ;

			// Open on page load
			$open_on_load = 'yes';
			$delay_value = 0;
			if($open_on_load == 'yes')
				$delay_value = get_post_meta( get_the_id(), $prefix . 'delay_value', true );

			// Cookie duration
			$expired_value = get_post_meta( get_the_id(), $prefix . 'expired_value', true );
			 if ($expired_value == "")
				 $expired_value = 0;

			// Create modal
			$output .= '';
			$output .= '<div id="psmp_clickcatcher_'.$postid.'" class="psmp_clickcatcher"></div>';
			$output .= '<div id="psmp_modal_'.$postid.'" class="'.$ps_modal_class.'">';

			// Layout switch
			switch($layout) {

				// ————————————
				// FULL PICTURE
				// ————————————
				case 'psmp_full_picture' :
					$image_background = get_post_meta( get_the_id(), $prefix . 'image_background', true );
					$contrast = get_post_meta( get_the_id(), $prefix . 'contrast', true );
					$output .= '
					<script type="text/javascript">
					$=jQuery.noConflict();
					$(document).ready(function()
					{
					$("#psmp_modal_'.$postid.'").css("background-image", "url('.$image_background.')");
					$("#psmp_modal_'.$postid.'").addClass("'.$contrast.'");
					if($("#psmp_modal_'.$postid.'").find(".psmp_button").length > 0)
					{
						$("#psmp_modal_'.$postid.'").find(".psmp_button").css("background-color","'.$ui_color.'");
					}
					});
					</script>

					<a id="psmp_close_'.$postid.'" class="psmp_close">
						<img src="'.plugins_url('img/full_picture_close.png', __FILE__).'"/>
					</a>

					<div class="psmp_image">
						<img src="'.$image.'"/>
					</div>
					<h1 class="psmp_header" >'.$header.'</h1>
					<h3 class="psmp_subheader" >'.$subheader.'</h3>
					<div class="clearfix"></div>

					<div class="psmp_content">'.$content.'</div>
					';
					break;

				// ————————————
				// HALF COLORED
				// ————————————
				case 'psmp_half_colored' :
					$first_color = get_post_meta( get_the_id(), $prefix . 'first_color', true );
					$contrast = get_post_meta( get_the_id(), $prefix . 'contrast', true );
					$output .= '
					<script type="text/javascript">
					$=jQuery.noConflict();
					$(document).ready(function()
					{
					$("#psmp_modal_'.$postid.'").addClass("'.$contrast.'");
					if($("#psmp_modal_'.$postid.'").find(".psmp_button").length > 0)
					{
						$("#psmp_modal_'.$postid.'").find(".psmp_button").css("background-color","'.$ui_color.'");
					}
					});
					</script>
					<a id="psmp_close_'.$postid.'" class="psmp_close">
						<img src="'.plugins_url('img/half_colored_close.png', __FILE__).'"/>
					</a>

					<div class="psmp_upper_content" style="background:'.$first_color.';">
						<div class="psmp_image">
							<img src="'.$image.'"/>
						</div>
						<h1 class="psmp_header" >'.$header.'</h1>
						<h3 class="psmp_subheader" >'.$subheader.'</h3>
						<div class="clearfix"></div>
					</div>

					<div class="psmp_lower_content">
						<div class="psmp_content">
							'.$content.'
						</div>
					</div>
					';
					break;

				// ————————————
				// LIGHT
				// ————————————
				case 'psmp_light' :
					$first_color = get_post_meta( get_the_id(), $prefix . 'first_color', true );
					$output .= '
					<script type="text/javascript">
					$=jQuery.noConflict();
					$(document).ready(function()
					{
					if($("#psmp_modal_'.$postid.'").find(".psmp_button").length > 0)
					{
						$("#psmp_modal_'.$postid.'").find(".psmp_button").css("background-color","'.$ui_color.'");
					}
					});
					</script>
					<a id="psmp_close_'.$postid.'" class="psmp_close">
						<img src="'.plugins_url('img/light_close.png', __FILE__).'"/>
					</a>
					<div class="psmp_image">
						<img src="'.$image.'"/>
					</div>
					<h1 class="psmp_header" >'.$header.'</h1>
					<h3 class="psmp_subheader" >'.$subheader.'</h3>
					<div class="psmp_content">'.$content.'</div>
					';
					break;

				default :
					break;
			}

			$output .= '</div>';

			//modal_popup js function : Waiting a click or Waiting a delay to Open on Load
			$output .= '<script type="text/javascript">';
			$output .= '$=jQuery.noConflict();';
			$output .= '$(document).ready(function()';
			$output .= '{';
			$output .= 'modal_popup("'.$postid.'","'.$open_on_load.'",'.$delay_value.','.$expired_value.');';
			$output .= '});';
			$output .= '</script>';
		}
	endforeach; wp_reset_query();
	echo $output;
}

// Creation of the Shortcode "Button"
add_shortcode( 'psmp_button', 'psmp_shortcode_button' );
function psmp_shortcode_button($atts) {

	extract(shortcode_atts(array(	"url" => '', "text" => ''	), $atts));

	$output = '<a href="'.$url.'" class="psmp_button">'.$text.'</a>';

	return $output;

}
?>
