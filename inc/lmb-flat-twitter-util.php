<?php

/**
 * Gets a string containing some text and makes hastags, urls and mentions clickable.
 *
 * @param $text (string) The input text. 
 *
 * @return (string) The string with the clickable anchor tags, hashtags and mentions.
 *
 */
function lmb_flat_twitter_make_text_clickable( $text ) {

	// if a link is found, make it clickable
	$result = make_clickable( $text );

	// the same for hastags 
	$result = preg_replace( '/(^|\s)#(\w*[a-zA-Z_]+\w*)/', '\1<a class="flat-twitter-clickable" href="http://twitter.com/search?q=%23\2">#\2</a>', $result );

	// and mentions
	$result = preg_replace( '/(^|\s)@(\w*[a-zA-Z_]+\w*)/', '\1<a class="flat-twitter-clickable" href="https://twitter.com/intent/user?screen_name=\2">@\2</a>', $result );


	return $result;
}

/**
 * Gets a shortened twitter url, and checks if its a youtube one. If a match
 * is found, a youtube url will be returned. Empty string otherwise.
 * 
 * @param $url 	(string) The twitter url
 *
 * @return 		(string) The youtube url
 */
function lmb_flat_twitter_get_youtube_url( $url ) {

	$headers = wp_remote_head( $url );

	$location = wp_remote_retrieve_header( $headers, 'location' );

	$youtube_link = '';

	if ( wp_remote_retrieve_response_code( $headers ) === 301 ) {

		// extract the video id 
		$video_id = '';

		if ( preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $location, $video_id ) ) {

			$youtube_link .= 'https://www.youtube.com/embed/' . $video_id[0] . '/?controls=2&modestbranding=1&showinfo=0';

		}
	
	}

	return $youtube_link;
		
}

/**
 * Tells if a hex color is valid.
 * 
 * @param $url 	(string) 	The color that nedds to be validated
 *
 * @return 		(boolean)
 */
function lmb_flat_twitter_validate_color( $color ) {

	if ( preg_match( '/^#[0-9a-f-A-F]{6}$|transparent/', $color ) !== false ) {

		return true;

	} 

	return false;

}