<?php

require_once( plugin_dir_path( __FILE__ ) . 'lmb-flat-twitter-util.php' );
require_once( plugin_dir_path( __FILE__ ) . 'class-lmb-oauth.php' );

// add emoji support
require( plugin_dir_path( __FILE__ ) . '../lib/emojione/lib/php/autoload.php');
Emojione\Emojione::$imageType = 'png';
Emojione\Emojione::$imagePathPNG = plugin_dir_url( __FILE__ ) . '../lib/emojione/assets/png/';

/**
 * Twitter Feeds Widget class.
 */
class Lmb_Flat_Twitter_Feeds_Widget extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    function __construct() {
        $args = array(
        	'classname' => 'lmb-flat-twitter-feeds-widget',
        	'description' => __( 'A Widget for displaying Twitter Feeds', 'lmb-flat-twitter-text-domain' )
        );
        parent::__construct( 'lmb-flat-twitter-feeds-widget', 'Flat Twitter Feeds', $args );

    }

    function form( $instance ) {

    	// if not set create empty vars 	
    	$feed_screen_name 			= isset( $instance['feed_screen_name'] ) ? $instance['feed_screen_name'] : '';

    	$feed_width 				= isset( $instance['feed_width'] ) ? $instance['feed_width'] : '';

    	$feed_display_media 		= isset( $instance['feed_display_media'] ) ? $instance['feed_display_media'] : '';

    	$feed_live_update 			= isset( $instance['feed_live_update'] ) ? $instance['feed_live_update'] : '';

    	$feed_display_border 		= isset( $instance['feed_display_border'] ) ? $instance['feed_display_border'] : '';

    	$feed_avatar_style 			= isset( $instance['feed_avatar_style'] ) ? $instance['feed_avatar_style'] : '';

    	$feed_avatar_border 		= isset( $instance['feed_avatar_border'] ) ? $instance['feed_avatar_border'] : '';

    	$feed_avatar_border_color 	= isset( $instance['feed_avatar_border_color'] ) ? $instance['feed_avatar_border_color'] : '';
    	
    	$feeds_limit 				= isset( $instance['feeds_limit'] ) ? $instance['feeds_limit'] : '';

    	$feed_refresh_rate 			= isset( $instance['feed_refresh_rate'] ) ? $instance['feed_refresh_rate'] : '';

    	$feed_type 					= isset( $instance['feed_type'] ) ? $instance['feed_type'] : '';

    	$feed_search_query 			= isset( $instance['feed_search_query'] ) ? $instance['feed_search_query'] : '';

   		?>
   		
			<ul class="flat-twitter-widget-option-list">
			
				<li>
			
					<label><?php _e( 'Username:', 'lmb-flat-twitter-text-domain' ); ?></label>
			
					<input class="widefat" value="<?php echo esc_attr( $feed_screen_name ); ?>" name="<?php echo $this->get_field_name( 'feed_screen_name' ); ?>" type="text">
			
					<small><?php _e( 'Your Twitter username.', 'lmb-flat-twitter-text-domain' ); ?></small>
		
				</li>

				<li>
			
					<label><?php _e( 'Widget width:', 'lmb-flat-twitter-text-domain' ); ?></label>
			
					<input class="widefat" value="<?php echo esc_attr( $feed_width ); ?>" name="<?php echo $this->get_field_name( 'feed_width' ); ?>" type="text">
			
					<small><?php _e( 'The width in pixels (without px). The minimum width is 280px.', 'lmb-flat-twitter-text-domain' ); ?></small>
		
				</li>

				<li>
		
					<label><?php _e( 'Display Feeds Media:', 'lmb-flat-twitter-text-domain' ); ?></label>
		
					<select class="widefat" name="<?php echo $this->get_field_name( 'feed_display_media' ); ?>">
		
						<option value="yes" <?php selected( $feed_display_media, 'yes' ); ?>><?php _e( 'Yes', 'lmb-flat-twitter-text-domain' ); ?></option>
		
						<option value="click" <?php selected( $feed_display_media, 'click' ); ?>><?php _e( 'On click', 'lmb-flat-twitter-text-domain' ); ?></option>
		
						<option value="no" <?php selected( $feed_display_media, 'no' ); ?>><?php _e( 'Hide', 'lmb-flat-twitter-text-domain' ); ?></option>

					</select>	

					<small><?php _e( 'Wheter or not to show the youtube video/photo of a tweet.', 'lmb-flat-twitter-text-domain' ); ?></small>
			
				</li>

				<li>
	
					<p><input name="<?php echo $this->get_field_name( 'feed_live_update' ); ?>" value="yes" type="checkbox" <?php checked( $feed_live_update, 'yes' )?>><label><?php _e( 'Enable Live Update', 'lmb-flat-twitter-text-domain' ); ?></label></p>

				</li>
		
				<li>
	
					<p><input name="<?php echo $this->get_field_name( 'feed_display_border' ); ?>" value="yes" type="checkbox" <?php checked( $feed_display_border, 'yes' )?>><label><?php _e( 'Display Feeds Border', 'lmb-flat-twitter-text-domain' ); ?></label></p>

				</li>

				<li>
		
					<label><?php _e( 'Avatar Style:', 'lmb-flat-twitter-text-domain' ); ?></label>
		
					<select class="widefat" name="<?php echo $this->get_field_name( 'feed_avatar_style' ); ?>">
		
						<option value="round" <?php  selected( $feed_avatar_style, 'round' ); ?>><?php _e( 'Round', 'lmb-flat-twitter-text-domain' ); ?></option>
		
						<option value="rect" <?php  selected( $feed_avatar_style, 'rect' ); ?>><?php _e( 'Rectangular', 'lmb-flat-twitter-text-domain' ); ?></option>
		
					</select>			
		
				</li>
		
				<li>
		
					<p><input name="<?php echo $this->get_field_name( 'feed_avatar_border' ); ?>" value="yes" type="checkbox" <?php checked( $feed_avatar_border, 'yes' )?>><label><?php _e( 'Display Avatar Border', 'lmb-flat-twitter-text-domain' ); ?></label></p>
		
				</li>

				<li>
	
					<label><?php _e( 'Avatar Border Color:', 'lmb-flat-twitter-text-domain' ); ?></label>
	
					<input class="add-color-picker widefat" value="<?php echo esc_attr( $feed_avatar_border_color ); ?>"name="<?php echo $this->get_field_name( 'feed_avatar_border_color' ); ?>"type="text">
	
					<small><?php _e( 'This will be ignored if the Avatar Border is not displayed.', 'lmb-flat-twitter-text-domain' ); ?></small><br>

				</li>
		
				<li>

					<label><?php _e( 'Tweets to Display:', 'lmb-flat-twitter-text-domain' ); ?></label>

					<input name="<?php echo $this->get_field_name( 'feeds_limit' ); ?>" value="<?php echo esc_attr( $feeds_limit ); ?>" class="widefat" type="text">

					<small><?php _e( 'How many tweets will be displayed.', 'lmb-flat-twitter-text-domain' ); ?></small>
		
				</li>

				<li>

					<label><?php _e( 'Refresh Rate:', 'lmb-flat-twitter-text-domain' ); ?></label>

					<input name="<?php echo $this->get_field_name( 'feed_refresh_rate' ); ?>" value="<?php echo esc_attr( $feed_refresh_rate ); ?>" class="widefat" type="text">

					<small><?php _e( 'How many minutes before refreshing. The minimum is 1 minute.', 'lmb-flat-twitter-text-domain' ); ?></small>
		
				</li>

				<li>

					<label><?php _e( 'Feed Type:', 'lmb-flat-twitter-text-domain' ); ?></label>

					<select name="<?php echo $this->get_field_name( 'feed_type' ); ?>" class="widefat">

						<option value="user_timeline" <?php selected( $feed_type, 'user_timeline' ); ?>><?php _e( 'User Timeline', 'lmb-flat-twitter-text-domain' ); ?></option>

						<option value="home_timeline" <?php selected( $feed_type, 'home_timeline' ); ?>><?php _e( 'Home Timeline', 'lmb-flat-twitter-text-domain' ); ?></option>

						<option value="search" <?php selected( $feed_type, 'search' ); ?>><?php _e( 'Search', 'lmb-flat-twitter-text-domain' ); ?></option>

					</select>

					<small><?php _e( 'User Timeline requires the username to be set.', 'lmb-flat-twitter-text-domain' ); ?></small><br>
			
					<small><?php _e( 'Home Timeline does not require the username to be set.', 'lmb-flat-twitter-text-domain' ); ?></small><br>

					<small><?php _e( 'Search require the search query to be set.', 'lmb-flat-twitter-text-domain' ); ?></small><br>
		
				</li>

				<li>

					<label><?php _e( 'Search Query:', 'lmb-flat-twitter-text-domain' ); ?></label>

					<input name="<?php echo $this->get_field_name( 'feed_search_query' ); ?>" value="<?php  echo esc_attr( $feed_search_query ); ?>" class="widefat" type="text">

					<a href="https://support.twitter.com/groups/53-discover/topics/215-search/articles/71577-using-advanced-search" target="_blank"><small><?php _e( 'Please click here to find more about the Twitter available search options.', 'lmb-flat-twitter-text-domain' ); ?></small></a>

				</li>

			</ul>
			
   		<?php

    }

    function update( $new_instance, $old_instance ) {

		// sanitize the values and set to defaults if not set

		$safe_instance = array();

		// username can contain up to 15 chars including numbers, letters, and underscores

		// if not valid set it to blank
		if ( isset( $new_instance['feed_screen_name'] ) ) {
			//	'@' is not necessary
			$safe_instance['feed_screen_name'] = preg_replace( '/@/', '', $new_instance['feed_screen_name'] );
			$safe_instance['feed_screen_name'] = preg_match( '/^[a-zA-Z0-9_]{3,15}$/', $safe_instance['feed_screen_name'] ) ? $safe_instance['feed_screen_name'] : '';
	
		} else { // not set

			$safe_instance['feed_screen_name'] = '';

		}

		// the widget width
		if ( isset( $new_instance['feed_width'] ) ) {

    		$safe_instance['feed_width'] = $new_instance['feed_width'];
    	
    	} else {

    		$safe_instance['feed_width'] = '';

    	}


		// wheter or not to show the tweet media
		if ( isset( $new_instance['feed_display_media'] ) 
				&&( $new_instance['feed_display_media'] === 'yes' 
					|| $new_instance['feed_display_media'] === 'no'
					|| $new_instance['feed_display_media'] === 'click'  ) ) {

			$safe_instance['feed_display_media'] = $new_instance['feed_display_media']; 

		} else { // if not set or not valid set to default

			$safe_instance['feed_display_media'] = 'click';    			

		}

		// enable live update of feeds
		if ( isset( $new_instance['feed_live_update'] ) && $new_instance['feed_live_update'] === 'yes' ) {

			$safe_instance['feed_live_update'] = $new_instance['feed_live_update'];
		}
		else { // if not set or not valid set to default

			$safe_instance['feed_live_update'] = '';

		}

		// display widget borders
		if ( isset( $new_instance['feed_display_border'] ) && $new_instance['feed_display_border'] === 'yes' ) {

			$safe_instance['feed_display_border'] = $new_instance['feed_display_border'];
		}
		else { // if not set or not valid set to default

			$safe_instance['feed_display_border'] = '';

		}

		// the avatar style
		if ( isset( $new_instance['feed_avatar_style'] ) 
				&&( $new_instance['feed_avatar_style'] === 'rect' 
					|| $new_instance['feed_avatar_style'] === 'round' ) ) {

			$safe_instance['feed_avatar_style'] = $new_instance['feed_avatar_style']; 

		} else { // if not set or not valid set to default

			$safe_instance['feed_avatar_style'] = 'round';    			

		}

		// display avatar border
		if ( isset( $new_instance['feed_avatar_border'] ) && $new_instance['feed_avatar_border'] === 'yes' ) {

			$safe_instance['feed_avatar_border'] = $new_instance['feed_avatar_border'];
		
		} else { // if not set or not valid set to default

			$safe_instance['feed_avatar_border'] = '';

		}

		//avatar border color
		if ( isset( $new_instance['feed_avatar_border_color'] ) && lmb_flat_twitter_validate_color( $new_instance['feed_avatar_border_color'] ) ) {

    		$safe_instance['feed_avatar_border_color'] = $new_instance['feed_avatar_border_color'];
    	
    	} else {

    		$safe_instance['feed_avatar_border_color'] = '';

    	} 

		// the feeds to show
		if ( isset( $new_instance['feeds_limit'] ) ) {

			$safe_instance['feeds_limit'] = absint( intval( $new_instance['feeds_limit'] ) );
			$safe_instance['feeds_limit'] = ( $safe_instance['feeds_limit'] > 0 ) ? $safe_instance['feeds_limit'] : 1;

		}	else { // if not set or not too low set to 1

			$safe_instance['feeds_limit'] = 1;

		}

		// the request refresh rate
		if ( isset( $new_instance['feed_refresh_rate'] ) ) {

			$safe_instance['feed_refresh_rate'] = absint( intval( $new_instance['feed_refresh_rate'] ) );
			$safe_instance['feed_refresh_rate'] = ( $safe_instance['feed_refresh_rate'] < 1 ) ? '5' : $safe_instance['feed_refresh_rate'];

		} else { // default is 5 minutes

			$safe_instance['feed_refresh_rate'] = 5;

		}

		// the feed type
		if ( isset( $new_instance['feed_type'] ) 
				&&( $new_instance['feed_type'] === 'user_timeline' 
					|| $new_instance['feed_type'] === 'home_timeline'
					|| $new_instance['feed_type'] === 'search'  ) ) {

			$safe_instance['feed_type'] = $new_instance['feed_type']; 

		} else { // if not set or not valid set to default

			$safe_instance['feed_type'] = 'home_timeline';    			

		}
		
		// search query
		if ( isset( $new_instance['feed_search_query'] ) ) {
		
			$safe_instance['feed_search_query'] = sanitize_text_field( $new_instance['feed_search_query'] );

		} else { // if not set leave blank
		
			$safe_instance['feed_search_query'] = '';
		
		}

    	return $safe_instance; 
    }

    /**
     * Displays the Twitter feeds.
     * The widget uses the data pulled from Twitter, but before making a new request checks if the
     * cache is set and not expired.
     * The key used with transients is build using 'lmb_flat_twitter_feeds_data' plus the ID of the
     * current widget.
     *
     * @return (void)
     */
    function widget( $args, $instance ) {
    	
    	echo $args['before_widget'];

    	extract( $instance );

    	// check the feed type
    	if ( $feed_type === 'timeline' ) { // $feed_screen_name must be specified

    		if ( ! isset( $screen_name ) || empty( $screen_name ) ) {

    			die( __( "If you choose the user timeline feed, you must specify the username!", 'lmb-flat-twitter-text-domain' ) );

    		}

    	} elseif ( $feed_type === 'search' ) { // $feed_search_query must be specified
    		
    		if ( ! isset( $feed_search_query ) || empty( $feed_search_query ) ) {

    			die( __( "If you choose the search feed, you must specify the search query!", 'lmb-flat-twitter-text-domain' ) );

    		}

    	}

    	// build the key used to retrieve the cached data
    	$widget_id = str_replace( 'lmb-flat-twitter-feeds-widget-', '', $args['widget_id'] );
    	
    	$cache = get_transient( 'lmb_flat_twitter_feeds_data_' . $widget_id );

    	//if the cache is expired or does not exists, or the widget feed options have been changed,
    	//or the username has changed, or the feeds limit is changed as well then make a new request
    	if ( ! $cache || ( $cache && isset( $cache['type'] ) && $cache['type'] !== $feed_type ) 
    			|| ( $cache && isset( $cache['screen_name'] ) && $cache['screen_name'] !== $feed_screen_name )
    			|| ( $cache && isset( $cache['refresh_rate'] ) && $cache['refresh_rate'] !== $feed_refresh_rate )
    			|| ( $cache && isset( $cache['search_query'] ) && $cache['search_query'] !== $feed_search_query )
    			|| ( $cache && isset( $cache['limit'] ) && $cache['limit'] !== $feeds_limit ) ) {

	    	// get the tokens to authenticate the connection
			$options = get_option( 'lmb_flat_twitter' );

			if ( isset( $options['lmb_flat_twitter_connection_params'] ) ) {

				$config = $options['lmb_flat_twitter_connection_params'];

			} else {
				
				die( __( 'Authentication tokens not set!','lmb-flat-twitter-text-domain' ) );

			}

			$oauth = new Lmb_OAuth( $config );

			// choose a different API depending on the feed type
			$twitter_api = '';

			if ( $feed_type === 'user_timeline' ) {

				$twitter_api = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
				$params = array( 
					'screen_name' => $feed_screen_name,
					'count' => strval( $feeds_limit ),
				);

			}

			// if the feed type is home type, the feeds will be pulled from the authenticating
			// user timeline
			if ( $feed_type === 'home_timeline' ) { 
				
				$twitter_api = 'https://api.twitter.com/1.1/statuses/home_timeline.json';
				$params = array( 
					'count' => strval( $feeds_limit ),
				);

			} 


			if ( $feed_type === 'search' ) {
				
				$twitter_api = 'https://api.twitter.com/1.1/search/tweets.json';
				$params = array( 
					'q' => rawurlencode( $feed_search_query ), 
					'count' => strval( $feeds_limit ),
				);

			}

			// perform the request 
			$oauth->prepare_request( 'GET', $twitter_api, $params );

			$raw_result =  $oauth->perform_request();
			
			$response_code = wp_remote_retrieve_response_code( $raw_result );

			if ( $response_code !== 200  ) {

				$error_message = ( $response_code === 404 ) ? __( '<br>Please check the username you entered in the screen name option', 'lmb-flat-twitter-text-domain' ) : sprintf( "<br>%1s %2s", __( 'Error code: ', 'lmb-flat-twitter-text-domain' ), $response_code );

				die( sprintf( "%1s %2s!",__( 'Could not complete the request.', 'lmb-flat-twitter-text-domain' ), $error_message ) );

			}

			// the result of the request to Twitter
			$result = json_decode( wp_remote_retrieve_body( $raw_result ) );

			$feeds = array(); // the feeds that will be stored using transients

			if( $feed_type === 'search' ) {

				$result = $result->statuses;

			}
			
			// iterate through the result and get the feeds
			foreach ( $result as $index => $feed ) {

				$youtube_url = '';
				$media_photo = '';

				// if a feed has a youtube video save the link
				if ( isset( $feed->entities->urls ) ) {

					foreach( $feed->entities->urls as $key => $url ) {
						
						$youtube_url = lmb_flat_twitter_get_youtube_url( $url->expanded_url );

					}

				}

				// if a feed has a picture get the link
				if ( isset( $feed->entities->media ) ) {

					foreach( $feed->entities->media as $key => $url ) {
					
						$media_photo = $url->media_url;		
					
					}
				
				}
			
				// feed data
				$feeds[ $index ] = array(
					'created_at'	=> $feed->created_at,
					'id'			=> sanitize_text_field( $feed->id_str ),
					'name'		 	=> sanitize_text_field( $feed->user->name ),
					'screen_name'	=> sanitize_text_field( $feed->user->screen_name ),
					'avatar'		=> esc_url( $feed->user->profile_image_url ),
					'text'		 	=> sanitize_text_field( $feed->text ),
					'youtube'		=> esc_url( $youtube_url ),
					'media_photo'	=> esc_url( $media_photo ),
				);
			
			}			

			// each value, except 'feeds', is used before making a new request to check if the options
			// have been changed.
			$feeds_data = array( 
				'type' 			=> $feed_type,				// the type of the widget feed
				'feeds' 		=> $feeds, 					// an array containg all the stored feeds
				'limit' 		=> $feeds_limit,			// used to check how many feeds to display
				'search_query'	=> $feed_search_query,		// the query for the search
				'refresh_rate' 	=> $feed_refresh_rate,		// how many minutes before making a new request
				'screen_name'	=> $feed_screen_name,		// the screen name used to show the user timeline
			);

			// set the cache 
			set_transient( 'lmb_flat_twitter_feeds_data_' . $widget_id, $feeds_data, $feed_refresh_rate * 60 );
			
			$transient_data = $feeds_data;
			
    	} else { // use the stored cache

    		$transient_data = $cache;
    		
    	}

    	// data needed by 'lmb-flat-twitter-feed-update.php' which cannot access directly the widget options
    	// these options are passed with the html data attributes
    	$data_feed['type'] 				= sprintf( 'data-feed-type="%1s"', esc_attr( $feed_type ) );
    	$data_feed['limit']				= sprintf( 'data-feeds-limit="%1s"', esc_attr( $feeds_limit ) );  
    	$data_feed['refresh_rate']		= sprintf( 'data-feed-refresh-rate="%1s"', esc_attr( $feed_refresh_rate ) );
    	$data_feed['screen_name']		= sprintf( 'data-feed-screen-name="%1s"', esc_attr( $feed_screen_name ) );
    	$data_feed['avatar_style']		= sprintf( 'data-feed-avatar-style="%1s"', esc_attr( $feed_avatar_style ) );
    	$data_feed['live_update']		= sprintf( 'data-feed-live-update="%1s"', esc_attr( $feed_live_update ) );
    	$data_feed['display_border'] 	= sprintf( 'data-feed-display-border="%1s"', esc_attr( $feed_display_border ) );
    	$data_feed['avatar_border'] 	= sprintf( 'data-feed-avatar-border="%1s"', esc_attr( $feed_avatar_border ) );
    	$data_feed['display_media']		= sprintf( 'data-feed-display-media="%1s"', esc_attr( $feed_display_media ) );
    	
		$data_fields = implode( ' ', $data_feed );

    	if ( $feed_type === 'search' ) {

    		// additional data attribute for search feeds
    		$data_fields .= ( $feed_type === 'search') ? ( sprintf( 'data-feed-search-query="%1s"', esc_attr( $feed_search_query ) ) ) : '';
    		  
    	} 

    	?>

    	<div class="flat-twitter-feeds" <?php echo $data_fields; ?>>

    		<div class="flat-twitter-feeds-wrap">
   
    			<ul class="flat-twitter-feeds-feed-list">
					<?php
						if ( ! isset( $transient_data['feeds'] ) ) {

							die( __( 'An error occured while trying to retrieve data! Please refresh.', 'lmb-flat-twitter-text-domain' ) );

						}
					?>

    				<?php foreach ( $transient_data['feeds'] as $index => $feed ) { ?>

    				<?php

    					extract( $feed );

    					$img_dir = plugins_url() . '/lmb-flat-twitter/img';

    				?>
   
	    				<li id="flat-twitter-feeds-feed-<?php echo esc_attr( $id ); ?>" class="flat-twitter-feeds-feed <?php echo ( $feed_display_border === 'yes' ) ? 'flat-twitter-naked-layout' : '';?> ">
	    					
	    					<header class="flat-twitter-feeds-header">
	    						
	    						<img src="<?php echo esc_url( $avatar ); ?>" class="flat-twitter-feeds-feed-avatar <?php echo ( $feed_avatar_style === 'round' ) ? 'flat-twitter-avatar-style-round' : ''; ?> <?php echo ( $feed_avatar_border === 'yes' ) ? 'flat-twitter-add-border' : ''; ?>">
	    						
	    						<a href="https://twitter.com/intent/follow?screen_name=<?php echo esc_attr( $screen_name );?>" class="flat-twitter-feeds-feed-info">
	    							
	    							<b class="flat-twitter-feeds-feed-name"><?php echo Emojione\Emojione::unicodeToImage(  sanitize_text_field( $name ) ); ?></b>

	    							<span class="flat-twitter-feeds-feed-screenname">@<?php echo sanitize_text_field( $screen_name ); ?></span>

	    						</a>
	    					
	    						<span class="flat-twitter-feeds-feed-time"><?php echo human_time_diff( strtotime( $created_at ), current_time('timestamp') ) . ' ago'; ?></span>
	    					
	    					</header>

	    					<div class="flat-twitter-feeds-feed-content">

	    						<?php echo Emojione\Emojione::unicodeToImage( lmb_flat_twitter_make_text_clickable( $text ) ); ?>

	    						<?php if ( $feed_display_media === 'click' ) : ?>

	    							<div class="flat-twitter-feed-content-media-wrap">

	    						<?php endif; ?>

		    						<?php  if ( ! empty( $youtube ) && $feed_display_media !== 'no' ) : ?>

		    							<div class="flat-twitter-feeds-content-youtube-video">

		    								<iframe width="560" height="315" src="<?php echo esc_url( $youtube ); ?>" frameborder="0" allowfullscreen></iframe>
		    							
		    							</div>

		    						<?php endif; ?>

		    						<?php if ( ! empty( $media_photo ) && $feed_display_media !== 'no' ) : ?>

											<img class="flat-twitter-feeds-content-photo" src="<?php echo esc_url( $media_photo ); ?>">

		    						<?php endif; ?>

		    					<?php if ( $feed_display_media === 'click' ) : ?>

	    							</div>

	    						<?php endif; ?>

	    					</div>

	    					<?php if ( $feed_display_media === 'click'  && ( ! empty( $youtube ) || ! empty( $media_photo ) ) ) : ?>

	    						<div class="flat-twitter-feeds-media-toggle">&hellip;</div>

	    					<?php endif; ?>

	    					<div class="flat-twitter-feeds-toggle">&plus;</div>

	    					<ul class="flat-twitter-feeds-feed-actions">
	    							
    							<li class="flat-twitter-feeds-feed-action flat-twitter-feeds-feed-action-reply">
    							
    								<a href="http://twitter.com/intent/tweet?tweet_id=<?php echo esc_attr( $id ); ?>">

    									<span class=" flat-twitter-feeds-feed-action-reply-icon"></span>
    								
    									<?php _e( 'Reply', 'lmb-flat-twitter-text-domain' );?>

    								</a>
    							
    							</li>

    							<li class="flat-twitter-feeds-feed-action flat-twitter-feeds-feed-action-retweet">
    							
    								<a href="http://twitter.com/intent/retweet?tweet_id=<?php echo esc_attr( $id ); ?>">

    									<span class="flat-twitter-feeds-feed-action-retweet-icon"></span>

    									<?php echo _e( 'Retweet', 'lmb-flat-twitter-text-domain' ) ;?>
    							
    								</a>	
    							
    							</li>

    							<li class="flat-twitter-feeds-feed-action flat-twitter-feeds-feed-action-favorite">
    							
    								<a href="http://twitter.com/intent/favorite?tweet_id=<?php echo esc_attr( $id ); ?>">

    									<span class="flat-twitter-feeds-feed-action-favorite-icon"></span>

    									<?php echo _e( 'Favorite', 'lmb-flat-twitter-text-domain' ) ;?>

    								</a>

    							</li>

    						</ul>
	    				
	    				</li>

    				<?php } ?>
   
    			</ul>
   
    		</div>
    		
    	</div>

    	<?php

    	echo $args['after_widget'];

    }

}