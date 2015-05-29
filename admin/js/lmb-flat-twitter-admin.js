(function ($) {

    "use strict";

    /**
     * Removes error message and adds the done message
     */
    function remove_error_msg() {

        $( '.flat-twitter-options-error-msg' ).remove();
    
    }

    /**
     * Removes loading animation
     */
    function remove_loading_animation() {

        setTimeout(function() { 
 
            $( '.flat-twitter-options-loading-window' ).remove();
 
        }, 500 );
 
    }

    /**
     * Serializes the form and perform the ajax request
     */
    $( document ).ready(function() {

        var $error_msg_content = '<p class="flat-twitter-options-error-msg">Please fill out all the fields</p>';
        var $loading_content = '<div class="flat-twitter-options-loading-window"></div>';

        $( '.flat-twitter-options-submit' ).click(function ( e ) {

            e.preventDefault();

            $( '.flat-twitter-options-done-msg' ).fadeOut( 200 );

            var access_params = {};
            var params_ok = true;

            $( '.flat-twitter-option-title' ).append( $loading_content );
            remove_error_msg();

            $( '.flat-twitter-option-input' ).each(function () {

                var $name = $( this ).attr( 'name' );
                var $value = $( this ).val();
                access_params[ $name ] = $value;

                if ( $value.length === 0 ) {
                    
                    // don't even attempt to post without having all the parameters
                    setTimeout( function() {

                        $( '.flat-twitter-option-title' ).after( $error_msg_content );
                   
                    }, 500 );

                    // remove the loading animation
                    remove_loading_animation();
                    params_ok = false;
                    return false;

                }

            });

            if ( !params_ok ) {
                return false;
            }
                            
            $.post( lmb_flat_twitter_admin_ajax_script.ajaxurl, { 

                action: 'lmb_flat_twitter_admin',
                data: access_params,
                security: lmb_flat_twitter_admin_ajax_script.nonce
        
            }).done(function() {
                
                remove_error_msg();
                remove_loading_animation();

                setTimeout(function() {

                     $( '.flat-twitter-options-done-msg' ).fadeIn( 200 );

                }, 700);

               
            }).fail(function() {
        
                remove_error_msg();
                remove_loading_animation();
        
            });

        });

    });

}) (jQuery);