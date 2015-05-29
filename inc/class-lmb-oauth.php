<?php
/*------------------------------------------------------------------------------------------------*\
 * Lmb_OAuth
 *
 * Class used to access server resources using OAuth standard.
 * In order to make a request follow these steps:
 *	
 * 1 - Create a configuration array and pass it to a Lmb_OAuth object.
 *		
 *		$config = array( 
 *			'consumer_key' 			=> 'XXXXXX', 
 *			'consumer_secret' 		=> 'XXXXXX', 
 *			'access_token' 			=> 'XXXXXX',
 *			'access_token_secret 	=> 'XXXXXX',
 *		);
 *
 *		$oauth = Lmb_OAuth( $config );
 *
 * 2 - Prepare the request 
 *
 *		$method 	= 'GET';
 *		$base_uri 	= 'https://api.twitter.com/1.1/users/show.json';
 *		$params = array( 'screen_name' => 'abc' );
 *
 *		$oauth->prepare_request( $method, $base_uri, $params );
 *
 * 3 - Make the actual request and store the result in a variable.
 *
 *		$result = $oauth->perform_request();	
\*------------------------------------------------------------------------------------------------*/

class Lmb_OAuth {
	
	private $_consumer_key;
	private $_consumer_secret;
	private $_access_token;
	private $_access_token_secret;

	private $_oauth_header;
	private $_base_uri;
	private $_query_string_parameters;

	/**
	 * Initialise the Oauth object with the 'consumer_key', 'consumer_secret', 'access_token'and 
	 * the 'access_token_secret'. In order to create the object, all parameter fields must be filled.
	 *
	 * @param (array)	$config 	Contains the config tokens. 
	 */
	public function __construct( $config ) {

		if ( ! isset( $config['consumer_key'] )  
			|| ! isset( $config['consumer_secret'] )
			|| ! isset( $config['access_token'] ) 
			|| ! isset( $config['access_token_secret'] ) ) {

			return false;
			
		}

		$this->_consumer_key 			= $config['consumer_key'];
		$this->_consumer_secret 		= $config['consumer_secret'];
		$this->_access_token 			= $config['access_token'];
		$this->_access_token_secret 	= $config['access_token_secret'];

	}

	/**
	 * Prepare Request
	 *
	 * Gets and sets the HTTP method, the uri of the API and the query string parameters that will be used for the 
	 * request. Prepares the authorization signature string required by the authorization header.
	 * 
	 * @param (string)	$http_method 				GET or POST method.
	 * @param (string)	$base_uri					The URI of the chosen API.
	 * @param (array)	$query_string_parameters 	An associative array containing the key and the value of the
	 *												query string parameters.
	 *
	 * @return (void)
	 */
	public function prepare_request( $http_method, $base_uri, $query_string_parameters ) {

		// build the oauth header as specified in the Oauth 1.0 RFC
		$oauth_header = array(

			'oauth_consumer_key' 		=> 	$this->_consumer_key,
	        'oauth_nonce' 				=> 	wp_create_nonce(),
	        'oauth_signature_method'	=> 	'HMAC-SHA1',
	        'oauth_token'				=> 	$this->_access_token,
	        'oauth_timestamp' 			=> 	time(),
	        'oauth_version' 			=> '1.0',
       
		);

		$oauth_header = wp_parse_args( $oauth_header, $query_string_parameters);
		$this->_query_string_parameters = $query_string_parameters;
		
		// build the base string
		$base_string = $this->build_base_string( $http_method , $base_uri, $oauth_header );

		// create the composite key
		$composite_key = $this->_get_composite_key( $this->_consumer_secret, $this->_access_token_secret );
		
		// generate the oauth signature string
		$oauth_signature = $this->_generate_signature( $base_string, $composite_key );

		$oauth_header['oauth_signature'] = $oauth_signature;

		$this->_oauth_header = $oauth_header;

		$this->_base_uri = $base_uri;
	}

	/**
	 * Builds a base string, which is a RFC 3986 encoded concatenation of:
	 * The uppercased HTTP method, an '&', and URL without parameters, an '&'
	 * and the query parameters RFC 3986 encoded, which include the API parameters
	 * and the OAuth parameters.
	 *
	 * @param (string)	$http_method 	The uppercase http method.
	 * @param (string)	$base_uri		The API URI.
	 * @param (array)	$oauth_header	The OAuth header.
	 *
	 * @return (string)					The base string. 
	 */
	public function build_base_string( $http_method, $base_uri, $oauth_header ) {

	    $return = array();

	    // All the parameters must be sorted alphabetically
        ksort( $oauth_header );
        
        foreach( $oauth_header as $key => $value ) {
            $return[] = "$key=" . $value;
        }
        
        return strtoupper( $http_method ) . "&" . rawurlencode( $base_uri ) . '&' . rawurlencode( implode( '&', $return ) ); 
	}

	/**
	 * Generates the signature key which is a concatenation of RFC 3986 encoded consumer secret, an '&' 
	 * and RFC 3986 encoded access token secret.
	 *	
	 * @param 	(string)	$consumer_secret
	 * @param 	(string) 	$access_token_secret
	 *
	 * @return (string)		The composite key.
	 */
	private function _get_composite_key( $consumer_secret, $access_token_secret ) {

		return rawurlencode( $consumer_secret ) . '&' . rawurlencode( $access_token_secret );
	
	}

	/**
	 * Generates a signature key which is a base64 encoded, hashed base string with the signature key.
	 *
	 * @param (string) $base_string 	The base string.
	 * @param (string) $composite_key 	The signature key.
	 *
	 * @return (string)					The signature string used in the oauth authorization header.
	 */
	private function _generate_signature( $base_string, $composite_key ) {

		return base64_encode( hash_hmac( 'sha1', $base_string, $composite_key, true ) ) ;
	}

	/**
	 * Buiilds the authorization header.
	 *
	 * @return (string)
	 */
	private function _build_authorization_header( $oauth_header ) {

		$authorization_header =  array();

		foreach ( $oauth_header as $key => $value ) {
		
			$authorization_header[] = "$key=\"" . rawurlencode( $value ) . "\"";
		}
		return implode( ', ', $authorization_header );

	}

	/**
	 * Performs the actual request, and returns the request result.
	 *
	 * @return (array) Returns the raw result.
	 */
	public function perform_request() {

		$oauth = $this->_build_authorization_header( $this->_oauth_header );
	
		$args = array( 
			'sslverify'	=> false,
			'headers'	=> array(
				'Authorization' 	=> 'OAuth ' . $oauth,
				'Expect' 			=> false,
				'Accept-Encoding'	=> false,
				'Content-Type'   	=> 'application/x-www-form-urlencoded;charset=UTF-8',
			),
		);
	
		$response = wp_remote_request( add_query_arg( $this->_query_string_parameters, $this->_base_uri ), $args );
		
		return  $response ;
				
	}
}
