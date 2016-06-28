/* Javscript Document  */
(function( $ ){
			$('.fancybox').fancybox();
		})( jQuery );
function checkInput(form){
		
			form.find(".validation").each(function(){				
				var val = jQuery(this).val();
				val = val.replace(" ","");			
				if(val != ""){
					// Если поле не пустое удаляем класс-указание
					jQuery(this).removeClass("empty_field");									
				} else {
					// Если поле пустое добавляем класс-указание
					jQuery(this).addClass("empty_field");					
				}
			});
}
function checkPhone(form){
	form.find(".need-int").each(function(){
		var need_int = jQuery(this).val();
		need_int = need_int.replace("_", "");
	
		if(need_int.length == 13){					
			// Если поле не пустое удаляем класс-указание
			jQuery(this).removeClass("empty_int");
		} else {
			// Если поле пустое добавляем класс-указание
			jQuery(this).addClass("empty_int");
		}
	});
}
function checkCod(form){
	form.find(".cod").each(function(){						
		if(jQuery(this).val().length == 3){					
			// Если поле не пустое удаляем класс-указание
			jQuery(this).removeClass("empty_cod");
		} else {
			// Если поле пустое добавляем класс-указание
			jQuery(this).addClass("empty_cod");
		}
	});
}
function checkTel(form){
	form.find(".phone").each(function(){
		var need_tel = jQuery(this).val();
		need_tel = need_tel.replace("_", "");			
		if(need_tel.length == 15){					
			// Если поле не пустое удаляем класс-указание
			jQuery(this).removeClass("empty_tel");
		} else {
			// Если поле пустое добавляем класс-указание
			jQuery(this).addClass("empty_tel");
		}
	});
}
function submit_form(el){
	var form = jQuery("#"+el.id);		
	checkInput(form);
	checkPhone(form);
	checkCod(form);
	checkTel(form);

	var sizeEmpty = form.find(".empty_field").size();
	var sizeInt = form.find(".empty_int").size();
	var sizeCod = form.find(".empty_cod").size();
	var sizeTel = form.find(".empty_tel").size();
			if(sizeEmpty>0 || sizeInt>0 || sizeCod > 0 || sizeTel > 0){
				alert('Заполните правильно обязательные поля!!!');
				return false;
			}else{
				var data   = form.serialize();
				if(form == 'form_perezvonite_mne'){
					data = data+"&title="+form.parent().parent().parent().parent().parent().parent().find('.fancybox-title .child').html();
				}
			
				jQuery.ajax({
					url: "/index.php?option=com_flats&task=sub_form&format=raw", // путь к обработчику
					type: "POST", // метод передачи
					data: {form_data: data},
					dataType: "json",
					success: function(json){	
					
						if(json){	
							yaCounter38057075.reachGoal('ORDER');						
							//alert('Спасибо, что обратились к нам! Наш менеджер свяжется с Вами.');						
							alert(json.html);							
						}else{
							
							alert("Ошибка"); 
							
						}
					}
				});
			}			
}
jQuery(document).ready(function($) {
	$(".v-phone").inputmask("999 999 9999");
	$(".phone").inputmask({'mask': '+7(999)999 9999'});
	$(".need-int").inputmask({'mask': '(999)999 9999'});
});