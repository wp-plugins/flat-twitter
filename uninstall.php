<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {

	exit();

}

unregister_widget( 'lmb-flat-twitter-profile-widget' );
unregister_widget( 'lmb-flat-twitter-feeds-widget' );

// delete the admin options
delete_option( 'lmb_flat_twitter' );

// get the profile widget and feeds widgets ids

global $wpdb;

$widgets = $wpdb->get_results( "SELECT * FROM {$wpdb->options} WHERE option_name LIKE 'widget_%'" );

$flat_twitter_feeds_widget_keys = array();
$flat_twitter_profile_widget_keys = array();

foreach ( $widgets as $widget ) {

	// check if is flat twitter widget
	if ( $widget->option_name === 'widget_lmb-flat-twitter-profile-widget' 
			|| $widget->option_name === 'widget_lmb-flat-twitter-feeds-widget' ) {

		$unserialized_options = maybe_unserialize( $widget->option_value );

		// if is a flat twitter widget get the ids of the active widgets
		$widget_ids = array_keys( $unserialized_options );

		// iterate using the ids since a registered widget could have multiple instances
		foreach( $widget_ids as $widget_id ) {
		
			if ( $widget->option_name === 'widget_lmb-flat-twitter-profile-widget' ) {

				if ( strpos( $widget_id,'multiwidget' ) === false ) {

					array_push( $flat_twitter_profile_widget_keys, 'lmb_flat_twitter_profile_data_' . $widget_id );

				}
				   
			}

			if ( $widget->option_name === 'widget_lmb-flat-twitter-feeds-widget' ) {

				if ( strpos( $widget_id,'multiwidget' ) === false ) {

					array_push( $flat_twitter_feeds_widget_keys, 'lmb_flat_twitter_feeds_data_' . $widget_id );

				}
			
			}

		}

	}
				
}

// delete profile widget cache options
foreach ( $flat_twitter_profile_widget_keys as $index => $value ) {
	
	delete_transient( $value );

}

// delete feeds widget cache options
foreach ( $flat_twitter_feeds_widget_keys as $index => $value ) {
	
	delete_transient( $value );

}

// make sure to delete all left plugin data
$wpdb->query("	DELETE FROM {$wpdb->options}  
				WHERE 	option_name='widget_lmb-flat-twitter-profile-widget' 
					OR option_name='widget_lmb-flat-twitter-feeds-widget'
					OR	option_name LIKE '_transient_lmb_flat_twitter_%'
					OR	option_name LIKE '_transient_timeout_lmb_flat_twitter%';"
);
