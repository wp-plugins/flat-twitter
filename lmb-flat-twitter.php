<?php
/**
 * Plugin Name: Flat Twitter
 * Plugin URI: http://lambertmb.com
 * Description: Flat Twitter plugin gives you two widgets that will let you showcase your Twitter Profile or your Feeds with flat design graphics. You will be able to customize the widgets without writing a single line of code.
 * Text Domain: lmb-flat-twitter-text-domain
 * Version: 1.0
 * Author: Lambert Mata
 * Author URI: http://lambertmb.com
 */

define( 'LMB_FLAT_TWITTER_PLUGIN_DIR_URL', plugin_dir_url( __FILE__  ) );
define( 'LMB_FLAT_TWITTER_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );

// include the administration panel
if ( is_admin() ) {

	require_once( LMB_FLAT_TWITTER_PLUGIN_DIR_PATH .  'admin/lmb-flat-twitter-admin.php' );

}

// include the widgets
require_once( LMB_FLAT_TWITTER_PLUGIN_DIR_PATH . 'inc/class-lmb-flat-twitter-feeds-widget.php' );
require_once( LMB_FLAT_TWITTER_PLUGIN_DIR_PATH . 'inc/class-lmb-flat-twitter-profile-widget.php' );

// the ajax handler
require_once( LMB_FLAT_TWITTER_PLUGIN_DIR_PATH . 'inc/lmb-flat-twitter-feed-update.php' );


/**
 * Adds the plugin default css rules plus the custom style defined in the widget by the user.
 *
 * @return (void) 
 */
if ( ! function_exists( 'lmb_flat_twitter_styles' ) ) :

	function lmb_flat_twitter_styles() {

		// main plugin css file
		wp_enqueue_style( 'lmb_flat_twitter_styles', LMB_FLAT_TWITTER_PLUGIN_DIR_URL . 'css/lmb-flat-twitter.css', array(), '1.0', 'all' ); 

		global $wpdb;

		// get all the registered widgets
		$widgets = $wpdb->get_results( "SELECT * FROM {$wpdb->options} WHERE option_name LIKE 'widget_%'" );
		
		$feeds_inline_style = '';
		$profile_inline_style = '';

		foreach ( $widgets as $widget ) {

			// check if is flat twitter widget
			if ( $widget->option_name === 'widget_lmb-flat-twitter-profile-widget' 
					|| $widget->option_name === 'widget_lmb-flat-twitter-feeds-widget' ) {

				$unserialized_options = maybe_unserialize( $widget->option_value );

				// if is a flat twitter widget get the ids of the active widgets
				$widget_ids = array_keys( $unserialized_options );

				// iterate using the ids since a registered widget could have multiple instances
				foreach( $widget_ids as $widget_id ) {

					// now we can get the widget stored data and add the inline style
					$option_values = $unserialized_options[ $widget_id ];
				
					if ( $widget->option_name === 'widget_lmb-flat-twitter-profile-widget' ) {

						$profile_background_color	 = $option_values['profile_background_color'];

						$profile_cover_color		 = $option_values['profile_cover_color'];

						$profile_cover_style		 = $option_values['profile_cover_style'];

						$profile_follow_button_color = $option_values['profile_follow_button_color'];

						$profile_avatar_border_color = $option_values['profile_avatar_border_color'];

						$profile_width = $option_values['profile_width'];

						// prevent from adding empty css rules
						if ( ! empty( $profile_background_color ) ) {

							$profile_inline_style .= "
								#lmb-flat-twitter-profile-widget-$widget_id .flat-twitter-profile,
				    			#lmb-flat-twitter-profile-widget-$widget_id .flat-twitter-profile-counters.horizontal {
							  		background-color: $profile_background_color;
				    			}
				    		";
							
						}

						if ( ! empty( $profile_cover_color ) ) {

							$profile_inline_style .= "
								#lmb-flat-twitter-profile-widget-$widget_id  .flat-twitter-profile-cover {
						        	background-color: $profile_cover_color;
						    	}
						    ";

						}

						if ( ! empty( $profile_avatar_border_color ) ) {

							$profile_inline_style .= "
					    		#lmb-flat-twitter-profile-widget-$widget_id  .flat-twitter-profile-avatar {
									background-color: $profile_avatar_border_color;
									border-color: $profile_avatar_border_color;
							    }
						    ";

						}

						if ( ! empty( $profile_follow_button_color ) ) {

							$profile_inline_style .= "
								#lmb-flat-twitter-profile-widget-$widget_id  .flat-twitter-profile-follow-button {
									background-color: $profile_follow_button_color;
						   		}
						   		#lmb-flat-twitter-profile-widget-$widget_id  .flat-twitter-profile-follow-button:hover {
									border-color: $profile_follow_button_color;
									color: $profile_follow_button_color!important;
						   		}
						   	";

						}

						if ( ! empty( $profile_width ) ) {

							$widget_width = (string)$profile_width . 'px';

							$feeds_inline_style .= "
							    #lmb-flat-twitter-profile-widget-$widget_id .flat-twitter-profile {
									width: $widget_width;
							    }
							";

						}	
		   
					}

					if ( $widget->option_name === 'widget_lmb-flat-twitter-feeds-widget' ) {

						$feed_avatar_border_color = $option_values[ 'feed_avatar_border_color' ];

						$feed_width = $option_values[ 'feed_width' ];

						if ( ! empty( $feed_avatar_border_color ) ) {

							$feeds_inline_style .= "
							    #lmb-flat-twitter-feeds-widget-$widget_id  .flat-twitter-feeds-feed-avatar {
									background-color: $feed_avatar_border_color;
									border-color: $feed_avatar_border_color;
							    }
							";

						}

						if ( ! empty( $feed_width ) ) {

							$widget_width = (string)$feed_width . 'px';

							$feeds_inline_style .= "
							    #lmb-flat-twitter-feeds-widget-$widget_id .flat-twitter-feeds {
									width: $widget_width;
							    }
							";

						}	   	

					}

				}

			}
						
		}

		wp_add_inline_style( 'lmb_flat_twitter_styles', $profile_inline_style );

		wp_add_inline_style( 'lmb_flat_twitter_styles', $feeds_inline_style );
	
	}

endif;

add_action( 'wp_enqueue_scripts', 'lmb_flat_twitter_styles' );


/**
 * Adds the scripts of the plugin.
 *
 * @return (void)
 */
if ( ! function_exists( 'lmb_flat_twitter_scripts' ) ) :

	function lmb_flat_twitter_scripts() {

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-effects-slide' );

		// add the support for twitter intents
		wp_enqueue_script( 'lmb_flat_twitter_script_twitter_intents', LMB_FLAT_TWITTER_PLUGIN_DIR_URL . 'lib/twitter-intents/widget.js', true );	

		$script_deps = array( 
			'jquery',
			'jquery-effects-slide',
		);

		// the main script used to make the ajax request, add the counter animation and the actions menu
		wp_enqueue_script( 'lmb_flat_twitter_script_main', LMB_FLAT_TWITTER_PLUGIN_DIR_URL . 'js/lmb-flat-twitter.js', $script_deps, '1.0', true );

		$args = array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
		);

		// this is necessary to find the url to make the ajax requests
		wp_localize_script( 'lmb_flat_twitter_script_main', 'lmb_flat_twitter_script_url', $args );

	}

endif;

add_action( 'wp_enqueue_scripts', 'lmb_flat_twitter_scripts' ); 


/**
 * Registers the widgets of the plugin.
 *
 * @return (void)
 */
if ( ! function_exists( 'lmb_flat_twitter_register_widgets' ) ) :

	function lmb_flat_twitter_register_widgets() {

		register_widget( 'Lmb_Flat_Twitter_Profile_Widget' ); 
		register_widget( 'Lmb_Flat_Twitter_Feeds_Widget' ); 	

	}

endif;

add_action( 'widgets_init', 'lmb_flat_twitter_register_widgets' );

/**
 * Adds the localization.
 *
 * @return (void)
 */
if ( ! function_exists( 'lmb_flat_twitter_localization' ) ) :

	function lmb_flat_twitter_localization() {

		load_plugin_textdomain( 'lmb-flat-twitter-text-domain', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );

	}

endif;

add_action( 'plugins_loaded', 'lmb_flat_twitter_localization' );

/**
 * Adds the color picker support to the widgets options. When configuring a widget
 * if a user clicks an input assigned for color selection, the color picker will be
 * popped out.
 *
 * @return (void)
 */
if ( ! function_exists( 'lmb_flat_twitter_widget_color_picker_support' ) ) :

function lmb_flat_twitter_widget_color_picker_support() {

	wp_enqueue_script( 'jquery' );

	wp_enqueue_style( 'wp-color-picker' );
	
	wp_enqueue_script( 'iris', admin_url( 'js/iris.min.js' ), array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ), false, 1 );

	// the script used to assign the color picker to inputs with 'add-color-picker' class.
	wp_enqueue_script( 'lmb_flat_twitter_script_widget_color_picker', LMB_FLAT_TWITTER_PLUGIN_DIR_URL . 'js/color-picker.js', array( 'jquery' ), '1.0', false );

}

endif;
	
if ( is_admin() ) {
	add_action( 'load-widgets.php', 'lmb_flat_twitter_widget_color_picker_support' );
}