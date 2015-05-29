<?php

/** 
 * Callback function for add_options_page which displays the admin menu in the settings page
 * which is used to insert the tokens that will be used to connect to Twitter API.
 *
 * The tokens are stored in an associative array which has 'lmb_flat_twitter_connection_params'
 * as key. 
 *
 * The key of the option in the database is 'lmb_flat_twitter'.
 *
 *	array (
 *		[lmb_flat_twitter_connection_params] => array (
 *			[consumer_key]			=> XXXXXX
 *			[consumer_secret]		=> XXXXXX
 *			[access_token]			=> XXXXXX
 *			[access_token_secret]	=> XXXXXX
 *		)
 *	)
 *
 *	@return (void)
 */

if ( ! function_exists( 'lmb_display_flat_twitter_admin' ) ) :

	function lmb_display_flat_twitter_admin() {

		$consumer_key = '';
		$consumer_secret = '';
		$access_token = '';
		$access_token_secret = '';

		$twitter_apps_url = 'https://apps.twitter.com/';

		$option_array = get_option( 'lmb_flat_twitter' );

		//	get the tokens from the database and if set show in the text fields
		
		if ( isset( $option_array ) && null !== ( $connection_params = $option_array['lmb_flat_twitter_connection_params'] ) ) {
		
				$consumer_key			= isset( $connection_params['consumer_key'] ) ? $connection_params['consumer_key'] :'';

				$consumer_secret		= isset( $connection_params['consumer_secret'] ) ? $connection_params['consumer_secret'] :'';

				$access_token 			= isset( $connection_params['access_token'] ) ? $connection_params['access_token'] :'';

				$access_token_secret 	= isset( $connection_params['access_token_secret'] ) ? $connection_params['access_token_secret'] : '';	
		}

	?>

		<div class="flat-twitter-options-wrapper">

			<div class="flat-twitter-options">

				<div class="flat-twitter-option-title">
					
					<h1><?php echo sanitize_text_field( get_admin_page_title() ); ?></h1>
	
					<div class="flat-twitter-options-done-msg">Settings Saved</div>

				</div>
				
				<form class="flat-twitter-options-form">

					<ul>
						<li class="flat-twitter-option">

							<label class="flat-twitter-option-label"><?php _e( 'Consumer Key', 'lmb-flat-twitter-text-domain' ); ?></label>

							<input name="consumer_key" value="<?php echo esc_attr( $consumer_key ); ?>" class="flat-twitter-option-input" type="text">
					
						</li>

						<li class="flat-twitter-option">

							<label class="flat-twitter-option-label"><?php _e( 'Consumer Secret', 'lmb-flat-twitter-text-domain' ); ?></label>

							<input name="consumer_secret" value="<?php echo esc_attr( $consumer_secret ); ?>" class="flat-twitter-option-input" type="text">

						</li>

						<li class="flat-twitter-option">

							<label class="flat-twitter-option-label"><?php _e( 'Access Token', 'lmb-flat-twitter-text-domain' ); ?></label>

							<input name="access_token" value="<?php echo esc_attr( $access_token ); ?>" class="flat-twitter-option-input" type="text">

						</li>

						<li class="flat-twitter-option">

							<label class="flat-twitter-option-label"><?php _e( 'Access Token Secret', 'lmb-flat-twitter-text-domain' ); ?></label>

							<input name="access_token_secret" value="<?php echo esc_attr( $access_token_secret ); ?>" class="flat-twitter-option-input" type="text">

						</li>

						<li class="flat-twitter-option">

							<button class="flat-twitter-options-submit"><?php _e( 'Submit', 'lmb-flat-twitter-text-domain' ); ?></button>

						</li>
					</ul>

				</form>

				<p class="flat-twitter-information">

					<strong><?php _e( 'Important', 'lmb-flat-twitter-text-domain' ); ?></strong>: 

					<?php printf( "%1s <a href='%2s' target='_blank' >Twitter Apps.</a>", _e( 'In order to obtain the required "keys" and "tokens", create a Twitter Application at the following link."', 'lmb-flat-twitter-text-domain' ), $twitter_apps_url ); ?>
			
				</p>

			</div>

		</div>

	<?php

	}

endif;

/**
 * Callback function for admin_menu action used to register the option page for the plugin.
 *
 * @return (void)
 */

if ( ! function_exists( 'lmb_flat_twitter_admin_init' ) ) :

	function lmb_flat_twitter_admin_init() {

		//	the settings page
		add_options_page( 
			__( 'Flat Twitter Settings', 'lmb-flat-twitter-text-domain' ), 
			'Flat Twitter',
			'manage_options', 
			__FILE__ , 
			'lmb_display_flat_twitter_admin'
		);

	}

endif;

add_action( 'admin_menu', 'lmb_flat_twitter_admin_init' );

/**
 * Callback function for admin_enqueue_scripts action used to add the admin menu stylesheets and
 * scripts.
 *
 * @return (void)
 */
if ( ! function_exists( 'lmb_flat_twitter_admin_enqueue_scripts' ) ) :

	function lmb_flat_twitter_admin_enqueue_scripts() {

		$plugin_admin_dir = plugin_dir_url( __FILE__ );

		wp_enqueue_script( 'lmb_flat_twitter_admin_script', $plugin_admin_dir . 'js/lmb-flat-twitter-admin.js', array( 'jquery' ), '1.0', false );
		
		wp_enqueue_style( 'lmb_flat_twitter_admin_style', $plugin_admin_dir . 'css/lmb-flat-twitter-admin.css', array(), '1.0', 'all' );	

		$args = array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'lmb-flat-twitter-nonce' ),
		);

		wp_localize_script( 'lmb_flat_twitter_admin_script', 'lmb_flat_twitter_admin_ajax_script', $args );
	}

endif;

add_action( 'admin_enqueue_scripts', 'lmb_flat_twitter_admin_enqueue_scripts' );

/**
 * Callback function for the wp_ajax action used to retrieve the data posted when an ajax call is
 * made by the script. Gets and stores the values in 'lmb_flat_twitter' options.
 *
 * @return (void)
 */
if ( ! function_exists( 'lmb_flat_twitter_admin_process_ajax_request' ) ) :

	function lmb_flat_twitter_admin_process_ajax_request() {

		$security_nonce = $_POST['security'];

		if( ! wp_verify_nonce( $security_nonce, 'lmb-flat-twitter-nonce' ) ) {

			die( 'Permissions denied!' );
		
		}
	
		if ( ! isset( $_POST['data'] ) && in_array( '', $_POST['data'] ) ) {

			die( 'Ajax request failed' );
		
		}

		$connection_params['lmb_flat_twitter_connection_params'] = $_POST['data'];

		// update the 'lmb_flat_twitter_connection_params' array of values stored in the database
		update_option( 'lmb_flat_twitter', $connection_params );
		
		die();

	}

endif;

add_action( 'wp_ajax_lmb_flat_twitter_admin', 'lmb_flat_twitter_admin_process_ajax_request' );

