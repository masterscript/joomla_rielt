jQuery.noConflict();
	function get_flats_cat(){
		jQuery.ajax({
			url: "index.php?option=com_flats&task=get_flats_cat&format=raw", // путь к обработчику
			type: "POST", // метод передачи		
			dataType: "json",
			success: function(json){
				jQuery("#loading-filter").hide();
				if(json.status=="ok"){
					jQuery(".nov-wrap .left").html(json.html);
					
				}else{
					alert("Ошибка");
				}
			}
		});
	}
	jQuery(function(){
		get_flats_cat();
	})
	

