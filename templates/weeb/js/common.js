
jQuery(function(){

//	main
	
	
	jQuery('.mod_check').buttonset();
	jQuery('.mod_radio').buttonset();
	
//main end


//mkr page range slider	
	
	
	jQuery('#mkr-range-slider').slider({
      range: true,
      min: 2,
      max: 33,
      values: [ 2, 33 ],
      slide: function( event, ui ) {
		jQuery( ".rsu-val1" ).val("от " + ui.values[ 0 ]);
		jQuery( ".rsu-val2" ).val("до " + ui.values[ 1 ] );
		
      },
	  stop: function(event, ui) {
				jQuery("#floor_b").val(ui.values[ 0 ]);
				jQuery("#floor_e").val(ui.values[ 1 ]);			
			}
    });
	
	jQuery("#mkr-range-slider").find("span:first").append('<input type="text" name="rsu-val1" value="от 2" readonly class="rsu-val1">');
	jQuery("#mkr-range-slider").find("span:last").append('<input type="text" name="rsu-val2" value="до 33" readonly class="rsu-val2">');
   

function change_el(){
		//Количество комнат
		var rooms_list = jQuery("input.rooms-need");
		var rooms = "";
		for(var i=0; i<rooms_list.length; i++){
			if(jQuery("#"+rooms_list[i].id).prop("checked")){
				rooms += " " + rooms_list[i].value;
			}
		}
		jQuery("#rooms").val(rooms);
}		
jQuery("input.rooms-need").change(function(){
		change_el();
	})	

	jQuery("#square-from, #square-to").keyup(function(){
		var m = jQuery("#square-from").val();
		var b = jQuery("#square-to").val();
		jQuery("#general_space_b").val(m);
		jQuery("#general_space_e").val(b);
    });		
	jQuery("#price").keyup(function(){
		var maxprice = jQuery("#price").val();
		jQuery("#price_e").val(maxprice);
	})

//mkr page range slider end	
	
//mkr planirovka range slider
	
	jQuery('#mkrSquare').slider({
      range: true,
      min: 1,
      max: 150,
      values: [ 1, 150 ],
      slide: function( event, ui ) {
		jQuery( "#mkrSquareLeft" ).val(ui.values[ 0 ]);
		jQuery( "#mkrSquareRight" ).val(ui.values[ 1 ] );
      }
    });
	
	jQuery('#mkrPrice').slider({
      range: true,
      min: 1,
      max: 50,
      values: [ 1, 50 ],
      slide: function( event, ui ) {
		jQuery( "#mkrPriceLeft" ).val(ui.values[ 0 ]);
		jQuery( "#mkrPriceReft" ).val(ui.values[ 1 ] );
      }
    });
	
	jQuery('#mkrFloor').slider({
      range: true,
      min: 2,
      max: 33,
      values: [ 2, 33 ],
      slide: function( event, ui ) {
		jQuery( "#mkrFloorLeft" ).val(ui.values[ 0 ]);
		jQuery( "#mkrFloorReft" ).val(ui.values[ 1 ] );
      }
    });
	
//	jQuery('#mkr-range-slider').slider({
//      range: true,
//      min: 0,
//      max: 25,
//      values: [ 0, 25 ],
//      slide: function( event, ui ) {
//		jQuery( ".rsu-val1" ).val("от " + ui.values[ 0 ]);
//		jQuery( ".rsu-val2" ).val("до " + ui.values[ 1 ] );
//      }
//    });
//	
//	jQuery("#mkr-range-slider").find("span:first").append('<input type="text" name="rsu-val1" value="от 1" readonly class="rsu-val1">');
//	jQuery("#mkr-range-slider").find("span:last").append('<input type="text" name="rsu-val2" value="до 25" readonly class="rsu-val2">');
   	
	
//mkr planirovka range slider end	

});

