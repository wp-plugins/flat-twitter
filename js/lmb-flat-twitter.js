(function ($) {

	'use strict';

	/**
	 * Triggers the animation counter.
	 */
	function lmb_flat_twitter_animate_counter( $counter_id ) {

		var length = parseInt( $counter_id.text() );

		$( { counter: 0 } ).animate( { counter: length }, {
			duration: 3000,
			easing:'easeInQuad',
			step: function() { 
	
				$counter_id.text( lmb_flat_twitter_number_formatter( Math.floor( this.counter ) ) );
				
		    },
		    complete: function() {
		    	// fix the number
		    	$counter_id.text( lmb_flat_twitter_number_formatter( Math.floor( length ) ) );

		    }
		});

	}

	/**
	 * Registers the media toggle which allows to show the feeds media.
	 */
	function lmb_flat_twitter_add_media_toggle( $toggle ) {

		$( '.flat-twitter-feeds' ).on( 'click', $toggle, function () {

			var $media = $( this ).prev().children( '.flat-twitter-feed-content-media-wrap' );

			if ( !$media.is( ':visible' ) ) {
				
				$media.slideDown();
			
			} else {

				$media.slideUp();
				
			}

		});

	}

	/**
	 * Registers the action toggle for the actions menu with the Twitter intents.
	 */
	function lmb_flat_twitter_action_toggle( $toggle ) {

		$( '.flat-twitter-feeds' ).on( 'click', $toggle, function () {

			var $actions_menu = $( this ).next();

			if ( !$actions_menu.is( ':visible' ) ) {

				$( this ).html( '&minus;' );
				
				$actions_menu.show("slide", { direction: "left" }, 400 );
			
			} else {

				$( this ).html( '&plus;' );

				$actions_menu.hide("slide", { direction: "left" }, 400 );
				
			}

		});

	}

	/**
	 * Uses the data attributes to pass the options to each widget.
	 */
	function lmb_flat_twitter_perform_request( current_feed_widget ) {

		var $widget_id = current_feed_widget.parent().attr( 'id' );
		var $current_feed_widgets_list = current_feed_widget.find( '.flat-twitter-feeds-feed-list' );

		var $data = {};

		// build the associative array of all the data
		$data['widget_id']		= $widget_id;
		$data['type'] 			= current_feed_widget.data( 'feed-type' );
		$data['limit']			= current_feed_widget.data( 'feeds-limit' );
		$data['refresh_rate']	= current_feed_widget.data( 'feed-refresh-rate' );
		$data['screen_name']	= current_feed_widget.data( 'feed-screen-name' );
		$data['avatar_style']	= current_feed_widget.data( 'feed-avatar-style' );
		$data['live_update']	= current_feed_widget.data( 'feed-live-update' );
		$data['display_border']	= current_feed_widget.data( 'feed-display-border' );
		$data['avatar_border']	= current_feed_widget.data( 'feed-avatar-border' );
		$data['display_media']	= current_feed_widget.data( 'feed-display-media' );

		// refresh only if live update is set to yes
		if ( $data['live_update'] != 'yes' ) {

			return;

		}
		
		if ( $data['type'] == 'search' ) {
		
			$data['search_query'] = current_feed_widget.data( 'feed-search-query' );
		
		}

		//prepare the json string
		var $json_data_string = JSON.stringify( $data );

		$.post( lmb_flat_twitter_script_url.ajaxurl, { 

            action: 'lmb_flat_twitter_request_new_feeds',
            data: $json_data_string
    
        }).done(function( result ) {

        	// replace the feed list with the html result
        	$current_feed_widgets_list.replaceWith( result );
			
        });

	}

	/**
	 * Formats the given number.
	 */
	 function lmb_flat_twitter_number_formatter( n ) {
	 
	 	if ( n > 999999 ) {
	 	
	 		return Math.round( ( n / 1000000 ) * 10 ) / 10 + 'M';
	 	
	 	} else if ( n > 999 ) {
	 	
	 		return Math.round( ( n / 1000 ) * 10 ) / 10 + 'K';
	 	
	 	}
	 	
	 	return n;
	 }


	$( document ).ready( function () {

		var $flat_twitter_feeds_feed_toggle		= '.flat-twitter-feeds-toggle';
		var $flat_twitter_feeds_media_toggle	= '.flat-twitter-feeds-media-toggle';
		var $flat_twitter_feeds					= $( '.flat-twitter-feeds' );
		var $flat_twitter_feeds_widget 			= $( '.lmb-flat-twitter-feeds-widget' );

		// 1 minute
		var $default_refresh_rate = 60 * 1000;

		// register the toggles
		lmb_flat_twitter_action_toggle( $flat_twitter_feeds_feed_toggle );
		lmb_flat_twitter_add_media_toggle( $flat_twitter_feeds_media_toggle );
		
		// for each feed widget set the refresh interval
		$flat_twitter_feeds.each( function () {

			var current_feed_widget = $( this );

			var feed_refresh = setInterval( function() {

				lmb_flat_twitter_perform_request( current_feed_widget );
		
			}, $default_refresh_rate );
			
			// disable the feeds refresh when the feed widget is hovered
			$flat_twitter_feeds_widget.hover( function() {

			    clearInterval( feed_refresh );

			}, function() {
			
			    feed_refresh = setInterval( function() {
			    	
			    	lmb_flat_twitter_perform_request( current_feed_widget );

			    }, $default_refresh_rate );
			
			});

		});
			
	});

	$( window ).load( function () {

		//	animate the counters of each widget counter
		$( '.flat-twitter-profile-counter' ).each( function() {   

	    	if ( $( this ).hasClass( 'flat-twitter-profile-counter-followers' ) ) {
	    	
	    		lmb_flat_twitter_animate_counter( $( this ).find( 'span' ) );
	    	
	    	}

	    	if ( $( this ).hasClass( 'flat-twitter-profile-counter-following' ) ) {
	    	
	    		lmb_flat_twitter_animate_counter(  $( this ).find( 'span' ) );
	    	
	    	}

	    	if ( $( this ).hasClass( 'flat-twitter-profile-counter-tweets' ) ) {

	    		lmb_flat_twitter_animate_counter(  $( this ).find( 'span' ) );
	    	
	    	}
	           
	    });

	});
	
}) (jQuery);