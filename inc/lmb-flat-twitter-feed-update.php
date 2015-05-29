<?php

/**
 * Handles the ajax requests made by the feeds widget. 
 */
if ( ! function_exists( 'lmb_flat_twitter_send_new_feeds' ) ) :

	function lmb_flat_twitter_send_new_feeds() {


		// all the posted data are required
		if ( ! isset( $_POST['data'] ) ) {

			die();

		}

		// define the widget options
		$post_data = json_decode( stripslashes( $_POST['data'] ) );

		$feed_type 				= $post_data->type;
		$feed_screen_name		= $post_data->screen_name;
		$feed_refresh_rate		= $post_data->refresh_rate;
		$feeds_limit 			= $post_data->limit;
		$feed_display_media		= $post_data->display_media;
		$feed_avatar_style		= $post_data->avatar_style;
		$feed_display_border 	= $post_data->display_border;
		$feed_avatar_border 	= $post_data->avatar_border;
		$feed_live_update	 	= $post_data->live_update;
		$feed_search_query		= '';


		if ( $feed_type === 'search' ) {

			$feed_search_query = $post_data->search_query;

		}

		// extract the key from the widget id
		$widget_id = $post_data->widget_id;

		$transient_key_prefix = 'lmb_flat_twitter_feeds_data_';
		$key = str_replace( 'lmb-flat-twitter-feeds-widget-', '', $widget_id );

		// get the cache
		$cache = get_transient( $transient_key_prefix . $key );

		//if the cache is expired or does not exists make a new request
    	if ( $cache  === false ) {

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
			set_transient( $transient_key_prefix . $key, $feeds_data, $feed_refresh_rate * 60 );
			
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
    	$data_feed['live_update']	 	= sprintf( 'data-feed-live-update="%1s"', esc_attr( $feed_live_update ) );
    	$data_feed['display_border'] 	= sprintf( 'data-feed-display-border="%1s"', esc_attr( $feed_display_border ) );
    	$data_feed['avatar_border'] 	= sprintf( 'data-feed-avatar-border="%1s"', esc_attr( $feed_avatar_border ) );
    	$data_feed['display_media']		= sprintf( 'data-feed-display-media="%1s"', esc_attr( $feed_display_media ) );
    	
		$data_fields = implode( ' ', $data_feed );

    	if ( $feed_type === 'search' ) {

    		// additional data attribute for search feeds
    		$data_fields .= ( $feed_type === 'search') ? ( sprintf( 'data-feed-search-query="%1s"', esc_attr( $feed_search_query ) ) ) : '';
    		  
    	} 

    	?>

    		<ul class="flat-twitter-feeds-feed-list">
				<?php
					if ( ! isset( $transient_data['feeds'] ) ) {

						die( __( 'An error occured while trying to retrieve data! Please refresh.', 'lmb-flat-twitter-text-domain' ) );

					}
				?>

    			<?php foreach ( $transient_data['feeds'] as $index => $feed ) { 

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
    	
		<?php

		die();

	}

endif;

add_action( 'wp_ajax_nopriv_lmb_flat_twitter_request_new_feeds', 'lmb_flat_twitter_send_new_feeds' );

add_action( 'wp_ajax_lmb_flat_twitter_request_new_feeds', 'lmb_flat_twitter_send_new_feeds');