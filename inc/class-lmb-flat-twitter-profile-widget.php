<?php

require_once( plugin_dir_path( __FILE__ ) . 'lmb-flat-twitter-util.php' );
require_once( plugin_dir_path( __FILE__ ) . 'class-lmb-oauth.php' );

// add emoji support
require( plugin_dir_path( __FILE__ ) . '../lib/emojione/lib/php/autoload.php');
Emojione\Emojione::$imageType = 'png';
Emojione\Emojione::$imagePathPNG = plugin_dir_url( __FILE__ ) . '../lib/emojione/assets/png/';

/**
 * Flat Twitter Profile Widget class.
 * If the widget is created using a shortcode, there is no need to store the data since the output will
 * be cached by the shortcode function.
 */
class Lmb_Flat_Twitter_Profile_Widget extends WP_Widget {

	 // Register widget with WordPress
    function __construct() {

        $args = array(
        	'classname' => 'lmb-flat-twitter-profile-widget',
        	'description' => __( 'A Widget for displaying Twitter Profiles', 'lmb-flat-twitter-text-domain' ),
        );

        parent::__construct( 'lmb-flat-twitter-profile-widget', __( 'Flat Twitter Profile', 'lmb-flat-twitter-text-domain' ), $args );

    }

    function form( $instance ) {

    	// if not set create empty vars   	
    	$profile_screen_name			= isset( $instance['profile_screen_name'] ) ? $instance['profile_screen_name'] : '';

    	$profile_width					= isset( $instance['profile_width'] ) ? $instance['profile_width'] : '';

    	$profile_layout 				= isset( $instance['profile_layout'] ) ? $instance['profile_layout'] : '';

    	$profile_background_color 		= isset( $instance['profile_background_color'] ) ? $instance['profile_background_color'] : '';

    	$profile_display_border 		= isset( $instance['profile_display_border'] ) ? $instance['profile_display_border'] : '';

    	$profile_avatar_border_color 	= isset( $instance['profile_avatar_border_color'] ) ? $instance['profile_avatar_border_color'] : '';

    	$profile_follow_button_style 	= isset( $instance['profile_follow_button_style'] ) ? $instance['profile_follow_button_style'] : '';

    	$profile_follow_button_color 	= isset( $instance['profile_follow_button_color'] ) ? $instance['profile_follow_button_color'] : '';
   		
   		$profile_cover_style 			= isset( $instance['profile_cover_style'] ) ? $instance['profile_cover_style'] : '';

   		$profile_cover_color 			= isset( $instance['profile_cover_color'] ) ? $instance['profile_cover_color'] : '';

   		$profile_avatar_style 			= isset( $instance['profile_avatar_style'] ) ? $instance['profile_avatar_style'] : '';

   		$profile_avatar_border 			= isset( $instance['profile_avatar_border'] ) ? $instance['profile_avatar_border'] : '';

   		$profile_followers_count 		= isset( $instance['profile_followers_count'] ) ? $instance['profile_followers_count'] : '';

   		$profile_following_count		= isset( $instance['profile_following_count'] ) ? $instance['profile_following_count'] : '';

   		$profile_tweets_count 			= isset( $instance['profile_tweets_count'] ) ? $instance['profile_tweets_count'] : '';

   		$profile_bio 					= isset( $instance['profile_bio'] ) ? $instance['profile_bio'] : '';

   		?>
   		
			<ul class="flat-twitter-widget-option-list">
			
				<li>
			
					<label><?php _e( 'Username:', 'lmb-flat-twitter-text-domain' ); ?></label>
			
					<input class="widefat" value="<?php echo esc_attr( $profile_screen_name ); ?>" name="<?php echo $this->get_field_name( 'profile_screen_name' ); ?>" type="text">
			
					<small><?php _e( 'Your Twitter username.', 'lmb-flat-twitter-text-domain' ); ?></small>
				
				</li>
			
				<li>
		
					<label><?php _e( 'Layout:', 'lmb-flat-twitter-text-domain' ); ?></label>
		
					<select class="widefat" name="<?php echo $this->get_field_name( 'profile_layout' ); ?>">
		
						<option value="default" <?php  selected( $profile_layout, 'default' ); ?>><?php _e( 'Default', 'lmb-flat-twitter-text-domain' ); ?></option>
		
						<option value="minimal" <?php  selected( $profile_layout, 'minimal' ); ?>><?php _e( 'Minimal', 'lmb-flat-twitter-text-domain' ); ?></option>
		
						<option value="horizontal" <?php  selected( $profile_layout, 'horizontal' ); ?>><?php _e( 'Horizontal', 'lmb-flat-twitter-text-domain' ); ?></option>
		
					</select>

				</li>

				<li>
			
					<label><?php _e( 'Widget width:', 'lmb-flat-twitter-text-domain' ); ?></label>
			
					<input class="widefat" value="<?php echo esc_attr( $profile_width ); ?>" name="<?php echo $this->get_field_name( 'profile_width' ); ?>" type="text">
			
					<small><?php _e( 'The width in pixels (without px). The minimum width is 280px.', 'lmb-flat-twitter-text-domain' ); ?></small>
				
				</li>
	
				<li>
	
					<label><?php _e( 'Background Color:', 'lmb-flat-twitter-text-domain' ); ?></label>
	
					<input class="add-color-picker widefat" value="<?php echo esc_attr( $profile_background_color ); ?>"name="<?php echo $this->get_field_name( 'profile_background_color' ); ?>"type="text">
	
				</li>

				<li>
	
					<p><input name="<?php echo $this->get_field_name( 'profile_display_border' ); ?>" value="yes" type="checkbox" <?php checked( $profile_display_border, 'yes' )?>><label><?php _e( 'Remove widget border.', 'lmb-flat-twitter-text-domain' ); ?></label></p>
	
				</li>
	
				<li>
	
					<label><?php _e( 'Follow Button Style:', 'lmb-flat-twitter-text-domain' ); ?></label>
	
					<select class="widefat" name="<?php echo $this->get_field_name( 'profile_follow_button_style' ); ?>">
	
						<option value="round" <?php  selected( $profile_follow_button_style, 'round' ); ?>><?php _e( 'Round', 'lmb-flat-twitter-text-domain' ); ?></option>
	
						<option value="rect" <?php  selected( $profile_follow_button_style, 'rect' ); ?>><?php _e( 'Rectangular', 'lmb-flat-twitter-text-domain' ); ?></option>
	
					</select>
	
				</li>
	
				<li>
	
					<label><?php _e( 'Follow Button Color:', 'lmb-flat-twitter-text-domain' ); ?></label>
	
					<input class="add-color-picker widefat" value="<?php echo esc_attr( $profile_follow_button_color ); ?>"name="<?php echo $this->get_field_name( 'profile_follow_button_color' ); ?>"type="text">
	
				</li>
	
				<li>
	
					<label><?php _e( 'Cover Style:', 'lmb-flat-twitter-text-domain' ); ?></label>
	
					<select class="widefat" name="<?php echo $this->get_field_name( 'profile_cover_style' ); ?>">
	
						<option value="banner" <?php  selected( $profile_cover_style, 'banner' ); ?>><?php _e( 'Banner', 'lmb-flat-twitter-text-domain' ); ?></option>
	
						<option value="color" <?php  selected( $profile_cover_style, 'color' ); ?>><?php _e( 'Color', 'lmb-flat-twitter-text-domain' ); ?></option>
	
					</select>
	
					<small><?php _e( 'If no banner is available, which means that you have not set any in your account, the cover will try to use the cover color when defined.', 'lmb-flat-twitter-text-domain' ); ?></small>
		
				</li>
		
				<li>
		
					<label><?php _e( 'Cover Color:', 'lmb-flat-twitter-text-domain' ); ?></label>
		
					<input class="add-color-picker widefat" value="<?php echo esc_attr( $profile_cover_color ); ?>" name="<?php echo $this->get_field_name( 'profile_cover_color' ); ?>" type="text">
		
					<small><?php _e( 'Note that the cover color will be applied only if the cover style is set to "Color".', 'lmb-flat-twitter-text-domain' ); ?></small>
		
				</li>
		
				<li>
		
					<label><?php _e( 'Avatar Style:', 'lmb-flat-twitter-text-domain' ); ?></label>
		
					<select class="widefat" name="<?php echo $this->get_field_name( 'profile_avatar_style' ); ?>">
		
						<option value"round" <?php  selected( $profile_avatar_style, 'round' ); ?>><?php _e( 'Round', 'lmb-flat-twitter-text-domain' ); ?></option>
		
						<option value="rect" <?php  selected( $profile_avatar_style, 'rect' ); ?>><?php _e( 'Rectangular', 'lmb-flat-twitter-text-domain' ); ?></option>
		
					</select>			
		
				</li>
		
				<li>
		
					<p><input name="<?php echo $this->get_field_name( 'profile_avatar_border' ); ?>" value="yes" type="checkbox" <?php checked( $profile_avatar_border, 'yes' )?>><label><?php _e( 'Display Avatar Border', 'lmb-flat-twitter-text-domain' ); ?></label></p>
		
				</li>

				<li>
	
					<label><?php _e( 'Avatar Border Color:', 'lmb-flat-twitter-text-domain' ); ?></label>
	
					<input class="add-color-picker widefat" value="<?php echo esc_attr( $profile_avatar_border_color ); ?>"name="<?php echo $this->get_field_name( 'profile_avatar_border_color' ); ?>"type="text">
	
					<small><?php _e( 'This will be ignored if the Avatar Border is not displayed.', 'lmb-flat-twitter-text-domain' ); ?></small><br>

				</li>
		
				<li>

					<p><input name="<?php echo $this->get_field_name( 'profile_followers_count' ); ?>" value="yes" type="checkbox" <?php checked( $profile_followers_count, 'yes' )?>><label><?php _e( 'Display Followers Count', 'lmb-flat-twitter-text-domain' ); ?></label></p>
		
					<p><input name="<?php echo $this->get_field_name( 'profile_following_count' ); ?>" value="yes" type="checkbox" <?php checked( $profile_following_count, 'yes' )?>><label><?php _e( 'Display Following Count', 'lmb-flat-twitter-text-domain' ); ?></label></p>
		
					<p><input name="<?php echo $this->get_field_name( 'profile_tweets_count' ); ?>" value="yes" type="checkbox" <?php checked( $profile_tweets_count, 'yes' )?>><label><?php _e( 'Display Tweets Count', 'lmb-flat-twitter-text-domain' ); ?></label></p>
		
				</li>
		
				<li>
		
					<p><input name="<?php echo $this->get_field_name( 'profile_bio' ); ?>" value="yes" type="checkbox" <?php checked( $profile_bio, 'yes' )?>><label><?php _e( 'Display Bio', 'lmb-flat-twitter-text-domain' ); ?></label></p>

				</li>

			</ul>	

   		<?php

    }

    function update( $new_instance, $old_instance ) {
	
		// sanitize the values and set to default if not defined

		$safe_instance = array();

		// username can contain up to 15 chars which can be numbers, letters, and underscores

		// if not valid set it to blank
		if ( isset( $new_instance['profile_screen_name'] ) ) {
			//	'@' is not necessary
			$safe_instance['profile_screen_name'] = preg_replace( '/@/', '', $new_instance['profile_screen_name'] );
			$safe_instance['profile_screen_name'] = preg_match( '/^[a-zA-Z0-9_]{3,15}$/', $safe_instance['profile_screen_name'] ) ? $safe_instance['profile_screen_name'] : '';
	
		} else { // not set

			$safe_instance['profile_screen_name'] = '';

		}


		// widget width
		if ( isset( $new_instance['profile_width'] ) ) {

    		$safe_instance['profile_width'] = $new_instance['profile_width'];
    	
    	} else {

    		$safe_instance['profile_width'] = '';

    	} 

		// choose the layout
		if ( isset( $new_instance['profile_layout'] ) 
				&& ( $new_instance['profile_layout'] === 'default' 
					|| $new_instance['profile_layout'] === 'minimal' 
					|| $new_instance['profile_layout'] === 'horizontal' )) {
			
			$safe_instance['profile_layout'] = $new_instance['profile_layout']; 

		} else { // if not set or not valid use default

			$safe_instance['profile_layout'] = 'default';

		}

		// widget background color
		if ( isset( $new_instance['profile_background_color'] ) && lmb_flat_twitter_validate_color( $new_instance['profile_background_color'] ) ) {

    		$safe_instance['profile_background_color'] = $new_instance['profile_background_color'];
    	
    	} else {

    		$safe_instance['profile_background_color'] = '';

    	} 

		// display widget borders
		if ( isset( $new_instance['profile_display_border'] ) && $new_instance['profile_display_border'] === 'yes' ) {

			$safe_instance['profile_display_border'] = $new_instance['profile_display_border'];
		}
		else { // not set or not valid

			$safe_instance['profile_display_border'] = '';

		}

		// follow button border style
		if ( isset( $new_instance['profile_follow_button_style'] ) 
				&& ( $new_instance['profile_follow_button_style'] === 'rect' 
					|| $new_instance['profile_follow_button_style'] === 'round' ) ) {
			
			$safe_instance['profile_follow_button_style'] = $new_instance['profile_follow_button_style']; 

		} else { // not set or not valid

			$safe_instance['profile_follow_button_style'] = 'rect';

		}

		// follow button color
		if ( isset( $new_instance['profile_follow_button_color'] ) && lmb_flat_twitter_validate_color( $new_instance['profile_follow_button_color'] ) ) {

    		$safe_instance['profile_follow_button_color'] = $new_instance['profile_follow_button_color'];
    	
    	} else {

    		$safe_instance['profile_follow_button_color'] = '';

    	} 

		// cover style
		if ( isset( $new_instance['profile_cover_style'] ) 
			&&  ( $new_instance['profile_cover_style'] === 'banner' 
				|| $new_instance['profile_cover_style'] === 'color' ) ) {

			$safe_instance['profile_cover_style'] =  $new_instance['profile_cover_style'];

		} else { // not set or not valid
		
			$safe_instance['profile_cover_style'] = '';
		
		}

		// cover color
		if ( isset( $new_instance['profile_cover_color'] ) && lmb_flat_twitter_validate_color( $new_instance['profile_cover_color'] ) ) {

    		$safe_instance['profile_cover_color'] = $new_instance['profile_cover_color'];
    	
    	} else {

    		$safe_instance['profile_cover_color'] = '';

    	} 

		// the avatar style
		if ( isset( $new_instance['profile_avatar_style'] ) 
				&&( $new_instance['profile_avatar_style'] === 'rect' 
					|| $new_instance['profile_avatar_style'] === 'round' ) ) {

			$safe_instance['profile_avatar_style'] = $new_instance['profile_avatar_style']; 

		} else { // if not set or not valid set to round

			$safe_instance['profile_avatar_style'] = 'round';    			

		}

		// display avatar border
		if ( isset( $new_instance['profile_avatar_border'] ) && $new_instance['profile_avatar_border'] === 'yes' ) {

			$safe_instance['profile_avatar_border'] = $new_instance['profile_avatar_border'];
		
		} else { // not set or not valid

			$safe_instance['profile_avatar_border'] = '';

		}

		//avatar border color
		if ( isset( $new_instance['profile_avatar_border_color'] ) && lmb_flat_twitter_validate_color( $new_instance['profile_avatar_border_color'] ) ) {

    		$safe_instance['profile_avatar_border_color'] = $new_instance['profile_avatar_border_color'];
    	
    	} else { // not set or not valid

    		$safe_instance['profile_avatar_border_color'] = '';

    	} 

		// display the followers count
		if ( isset( $new_instance['profile_followers_count'] ) && $new_instance['profile_followers_count'] === 'yes' ) {

			$safe_instance['profile_followers_count'] = $new_instance['profile_followers_count'];
		
		} else { // not set or not valid

			$safe_instance['profile_followers_count'] = '';

		}

		// display the following count
		if ( isset( $new_instance['profile_following_count'] ) && $new_instance['profile_following_count'] === 'yes' ) {

			$safe_instance['profile_following_count'] = $new_instance['profile_following_count'];
		
		} else { // not set or not valid

			$safe_instance['profile_following_count'] = '';

		}

		// display the tweets count
		if ( isset( $new_instance['profile_tweets_count'] ) && $new_instance['profile_tweets_count'] === 'yes' ) {

			$safe_instance['profile_tweets_count'] = $new_instance['profile_tweets_count'];
		
		} else { // not set or not valid

			$safe_instance['profile_tweets_count'] = '';

		}

		// display the description
		if ( isset( $new_instance['profile_bio'] ) && $new_instance['profile_bio'] === 'yes' ) {

			$safe_instance['profile_bio'] = $new_instance['profile_bio'];
		
		} else { // not set or not valid

			$safe_instance['profile_bio'] = '';

		}

		return $safe_instance;

    }

    /**
     * Displays the widget to the front-end according to the options set before. 
     *
     * The widget has 3 main layouts, default, minimal and horizontal. Each layout
     * allows to use different options to customize the widget.
     * The plugin in order to work needs to make API calls to Twitter, but once the data
     * is retrieved, it'cached using the Wordpress transients.
     * The options are stored using 'lmb_flat_twitter_profile_data' plus the id of the current widget.
     * 
     * @return (void)
     */
    function widget( $args, $instance ) {
	
    	echo $args['before_widget'];

   		extract( $instance ); 

		// the screen name must be specified
		if ( ! isset( $profile_screen_name ) || empty( $profile_screen_name ) ) {

			die( __( 'Please specify the Screen Name!', 'lmb-flat-twitter-text-domain' ) );

		}

		$widget_id = str_replace( 'lmb-flat-twitter-profile-widget-', '', $args['widget_id'] );
		
    	$cache = get_transient( 'lmb_flat_twitter_profile_data_' . $widget_id );

		$option_data = '';

		// if not set or expired make an API call and update the options or the screen name has been changed
		if ( ! $cache || ( $cache && $cache['screen_name'] !== $profile_screen_name ) ) {

			// get the stored tokens set in settings to authenticate
			$options = get_option( 'lmb_flat_twitter' );

			$config = array();

			if ( isset( $options['lmb_flat_twitter_connection_params'] ) && is_array( $options ) ) {

				$config = $options['lmb_flat_twitter_connection_params'];

			} else {
				
				die( __( 'Token parameters missing! Please check your settings.', 'lmb-flat-twitter-text-domain' ) );

			}
				
			$oauth = new Lmb_OAuth( $config );
		
			$params = array( 'screen_name' => $profile_screen_name );

			// perform the request to Twitter and update the options on success
			$oauth->prepare_request( 'GET', 'https://api.twitter.com/1.1/users/show.json', $params );

			$raw_result =  $oauth->perform_request();
			$response_code = wp_remote_retrieve_response_code( $raw_result );

			if ( $response_code !== 200  ) {

				$error_message = ( $response_code === 404 ) ? __( '<br>Please check the username you entered in the screen name option', 'lmb-flat-twitter-text-domain' ) : sprintf( "<br>%1s %2s", __( 'Error code: ', 'lmb-flat-twitter-text-domain' ), $response_code );

				die( sprintf( "%1s %2s!",__( 'Could not complete the request.', 'lmb-flat-twitter-text-domain' ), $error_message ) );

			}

			$result = json_decode( wp_remote_retrieve_body( $raw_result ) );

			$banner = ''; 

			if ( $profile_cover_style === 'banner' ) {

				// to retrieve the banner we have to make a second request
				$oauth->prepare_request( 'GET', 'https://api.twitter.com/1.1/users/profile_banner.json', $params );
				
				$banner_request_response = $oauth->perform_request();
				
				$banner_request_result =  json_decode( wp_remote_retrieve_body( $banner_request_response ) );

				if ( wp_remote_retrieve_response_code( $banner_request_response ) !== 200 ) {

					$banner = ''; 

				} else {

					$banner = $banner_request_result->sizes->web_retina->url;

				}

			}

			// save the result in profile data using the widget id		
			$profile_data = array(
												// get the original size
				'avatar'						=> str_replace( '_normal', '', $result->profile_image_url_https ),
				'name' 							=> sanitize_text_field( $result->name ),
				'banner' 						=> esc_url( $banner ),
				'following_count' 				=> intval( $result->friends_count ),
				'followers_count' 				=> intval( $result->followers_count ),
				'tweets_count' 					=> intval( $result->statuses_count ),
				'bio' 							=> sanitize_text_field( $result->description ),
				'screen_name'					=> sanitize_text_field( $result->screen_name ),

			);

			// 5 minutes expiration
			set_transient( 'lmb_flat_twitter_profile_data_' . $widget_id, $profile_data, '60 * 5' );

			// copy for use
			$option_data = $profile_data;

		} else { // the option data is set and is not expired

			$option_data = $cache;

		}

		// make the data available 
		extract( $option_data );

		if ( $profile_layout === 'horizontal' && $profile_followers_count !== 'yes' 
				&& $profile_following_count !== 'yes' && $profile_tweets_count !== 'yes' ) {
			
			die( __( 'When horizontal layout is selected, at least one counter must be enabled!', 'lmb-flat-twitter-text-domain' ) );
		
		}

		?>

		<div class="flat-twitter-profile <?php echo esc_attr( $profile_layout ); ?> <?php echo ( $profile_display_border === 'yes' ) ? 'flat-twitter-naked-layout' : ''; ?>">
		
			<div class="flat-twitter-profile-wrap">

				<?php if ( $profile_layout === 'horizontal' || $profile_layout === 'default' ) : ?>	
						
					<div class="flat-twitter-profile-cover <?php echo esc_attr( $profile_layout ); ?> ">
					
						<?php if ( $profile_cover_style === 'banner'  && ( ! empty( $banner ) ) ) : ?>
						
							<img src="<?php echo esc_url( $banner ); ?>">
						
						<?php endif; ?>

					</div>

				<?php endif; ?>
		
				<div class="flat-twitter-profile-info <?php echo esc_attr( $profile_layout ); ?>">
				
				<?php if ( $profile_layout === 'horizontal' || $profile_layout === 'default' ) : ?>	

					<div class="flat-twitter-profile-info-wrap">

				<?php endif; ?>
				
						<div class="flat-twitter-profile-avatar-wrap <?php echo esc_attr( $profile_layout ); ?>">
							
							<a href="https://twitter.com/intent/user?screen_name=<?php echo esc_attr( $screen_name ); ?>" target="_blank"><img alt="<?php echo esc_attr( $screen_name ); ?>" src="<?php echo esc_url( $avatar ); ?>" class="<?php echo ( $profile_avatar_style === 'round' ) ? 'flat-twitter-avatar-style-round ' : '';?>flat-twitter-profile-avatar<?php echo ( $profile_avatar_border === 'yes' ) ? ' flat-twitter-add-border' : '';?>"></a>
						
							<?php if ( $profile_layout === 'horizontal' ) : ?>	

								<a href="https://twitter.com/intent/follow?screen_name=<?php echo esc_attr( $screen_name ); ?>" target="_blank" class="<?php echo ( $profile_follow_button_style === 'round' ) ? 'flat-twitter-follow-button-style-round ' : '';?>flat-twitter-profile-follow-button <?php echo esc_attr( $profile_layout ); ?>" target="_blank">&#43;</a>	
				
							<?php endif; ?>

						</div>

				<?php if ( $profile_layout === 'minimal' ) : ?>	

					<div class="flat-twitter-profile-info-wrap <?php echo esc_attr( $profile_layout ); ?>">
					
				<?php endif; ?>
						
						<h2 class="flat-twitter-profile-name"><a href="https://twitter.com/intent/user?screen_name=<?php echo esc_attr( $screen_name ); ?>" target="_blank"><?php echo Emojione\Emojione::unicodeToImage(  sanitize_text_field( $name ) ); ?></a></h2>
						
						<p class="flat-twitter-profile-username"><a href="https://twitter.com/intent/user?screen_name=<?php echo esc_attr( $screen_name ); ?>" target="_blank"><?php echo '@' . esc_attr( $screen_name ); ?></a></p>
						
	
						<?php if ( $profile_layout === 'horizontal' || $profile_layout === 'default' && $profile_bio === 'yes' ) : ?>

							<p class="flat-twitter-profile-bio"><?php echo Emojione\Emojione::unicodeToImage( lmb_flat_twitter_make_text_clickable( $bio ) ); ?></p>

						<?php endif; ?>
						
						<?php if ( $profile_layout === 'default' || $profile_layout === 'minimal') : ?>

							<a href="https://twitter.com/intent/follow?screen_name=<?php echo esc_attr( $screen_name ); ?>" target="_blank" class="<?php echo ( $profile_follow_button_style === 'round' ) ? 'flat-twitter-follow-button-style-round ' : '';?>flat-twitter-profile-follow-button <?php echo esc_attr( $profile_layout ); ?>" target="_blank"><?php _e( 'follow', 'lmb-flat-twitter-text-domain' );?></a>	
				
						<?php endif; ?>

					</div>
				
				</div>

				<?php if ( $profile_layout === 'horizontal' || $profile_layout === 'default') : ?>
				
					<div class="flat-twitter-profile-counters <?php echo esc_attr( $profile_layout ); ?>">
			
						<ul class="flat-twitter-profile-counter-wrap">
							<?php

								// check the number of columns

								$counter_columns = 1;

								if ( $profile_followers_count === 'yes' && $profile_following_count === 'yes'
										&& $profile_tweets_count === 'yes' ) {
							
									$counter_columns = 3;
							
								} else if ( $profile_followers_count === 'yes' && $profile_following_count === 'yes' 
											|| $profile_followers_count === 'yes' && $profile_tweets_count === 'yes' 
											|| $profile_following_count === 'yes' && $profile_followers_count === 'yes'
											|| $profile_following_count === 'yes' && $profile_tweets_count === 'yes' 
											|| $profile_tweets_count === 'yes' && $profile_followers_count === 'yes'
											|| $profile_tweets_count === 'yes' && $profile_following_count === 'yes' ) {
							
									$counter_columns = 2;
							
								}

								$followers_label =	'followers';
								$tweets_label	=	'tweets';

								if ( intval( $followers_count ) == 1 ) {

									$followers_label = 'follower';

								}

								if ( intval( $tweets_count ) == 1 ) {

									$tweets_label = 'tweet';

								}


							?>
							
							<?php if ( $profile_followers_count === 'yes' ) : ?>
							
								<li class="flat-twitter-profile-counter flat-twitter-profile-counter-following flat-twitter-profile-counter-cols-<?php echo esc_attr( $counter_columns ); ?>">
							
									<p class="<?php echo esc_attr( $profile_layout ); ?>"><span><?php echo intval( $following_count ); ?></span><?php _e( 'following', 'lmb-flat-twitter-text-domain' ); ?></p>
							
								</li>

							<?php
								endif;

								if ( $profile_following_count === 'yes' ) :
							?>
								<li class="flat-twitter-profile-counter flat-twitter-profile-counter-followers flat-twitter-profile-counter-cols-<?php echo esc_attr( $counter_columns ); ?>">
							
									<p class="<?php echo esc_attr( $profile_layout ); ?>"><span><?php echo intval( $followers_count ); ?></span><?php _e( $followers_label, 'lmb-flat-twitter-text-domain' ); ?></p>
							
								</li>

							<?php
								endif;

								if ( $profile_tweets_count === 'yes' ) :
							?>
								<li class="flat-twitter-profile-counter flat-twitter-profile-counter-tweets flat-twitter-profile-counter-cols-<?php echo esc_attr( $counter_columns ); ?>">
							
									<p class="<?php echo esc_attr( $profile_layout ); ?>"><span><?php echo intval( $tweets_count ); ?></span><?php _e( $tweets_label, 'lmb-flat-twitter-text-domain' ); ?></p>
							
								</li>
		
							<?php endif; ?>
											
						</ul>

					</div>

				<?php endif; ?>

			</div>

		</div>
	
		<?php
		
    	echo $args['after_widget'];

    }

}