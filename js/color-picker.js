(function ($) {

	"use strict";

	function add_the_color_picker() {

		var $color_picker_items = $( '.add-color-picker' );

		//init the color picker
		$color_picker_items.iris({
			width: 300,
			palettes: true
		});

		// hide the color picker if is clicked outside
	    $( document ).click(function ( e ) {
	    
	    	$color_picker_items.iris();
	    
	        if ( !$( e.target ).is( '.add-color-picker, .iris-picker, .iris-picker-inner' ) ) {
	            $color_picker_items.iris( 'hide' );
	        }

	    });

	    $color_picker_items.click(function () {

	    	$( this ).iris();
	        $color_picker_items.iris( 'hide' );
	        $( this ).iris( 'show' );

	    });
	}

	$( document ).ready(function () {
		
		add_the_color_picker();
		
	});

	$( document ).ajaxSuccess(function () {

		add_the_color_picker();

	});

}) (jQuery);