<?php 
defined('_JEXEC') or die;
$catid = $this->category->id;
$doc = JFactory::getDocument();
	$doc->addStyleSheet(JURI::base( true ).'/templates/weeb/jquery-ui-1.11.4/jquery-ui.css');	
	
	$doc->addScript('//yandex.st/jquery/cookie/1.0/jquery.cookie.min.js');
	$doc->addScript(JURI::base( true ).'/templates/weeb/jquery-ui-1.11.4/jquery-ui.js');	

	$doc->addScript('http://api-maps.yandex.ru/2.0/?load=package.full,package.geoObjects,package.route&lang=ru-RU');
$limit = 20;
$limitstart = 0;
$curr_link = 1;
if (isset($_GET['page']) && !empty($_GET['page'])){
	$curr_link = $_GET['page'];
	$limitstart = (int)$curr_link * $limit - $limit;
}else{
	$limitstart = 0;
}
$values = array();
if(!empty($_COOKIE["cat_id".$catid])){
	$filtr = explode ("&", $_COOKIE["cat_id".$catid]);
	foreach($filtr as $value){
		$type = explode ("=", $value);
		if(isset($type[1]) && !empty($type[1])){
		$values[$type[0]] =  str_replace("+"," ",$type[1]);		
		}
	}
}
if(!empty($_COOKIE["filter_main_go"]) && $_COOKIE["filter_main_go"] == '1' && !empty($_COOKIE['filter_main'])){
	$filtr = explode ("&", $_COOKIE["filter_main"]);
	foreach($filtr as $value){
		$type = explode ("=", $value);
		if(isset($type[1]) && !empty($type[1])){
		$values[$type[0]] =  str_replace("+"," ",$type[1]);		
		}
	}
}
if(isset($values['rooms']) && !empty($values['rooms'])){
	$checked_r = explode(' ', $values['rooms']);
}
 if(isset($values['floor_b'])){ $floor_b = $values['floor_b'];}else{$floor_b = 2;} 
 if(isset($values['floor_e'])){ $floor_e = $values['floor_e'];}else{$floor_e = 33;} 
$_SERVER['REQUEST_URI'];
$url = explode('?', $_SERVER['REQUEST_URI']);
$url = $url[0];
$doc->addScriptDeclaration('
	function get_flats_category(allid){
		var wrap  = jQuery(".list-flats");
		var form = jQuery("#object_filter");
		var limit = jQuery("#limit").val();
		var limitstart = jQuery("#limitstart").val();
		var filter_data = form.serialize();		
		jQuery.cookie("cat_id'.$catid.'", filter_data);
		jQuery("#loading-filter").show();
		jQuery.ajax({
			url: "index.php?option=com_flats&task=get_flats_category&format=raw", // путь к обработчику
			type: "POST", // метод передачи
			data: {form_data: filter_data},
			dataType: "json",
			success: function(json){
				jQuery("#loading-filter").hide();
				if(json.status=="ok"){
					wrap.html(json.html); 	
					jQuery("#pagination_list").html(json.paging);
					jQuery(".mkr-table .result").text("("+json.counter+")");
				}else{
					alert("Ошибка");
				}
			}
		});
	}
	window.onload = function () {		
		get_flats_category("'.$catid.'");
		
	}
jQuery(function(){
	$.cookie("filter_main_go", null);
	$.cookie("filter_main", null);
	
	jQuery(".mod_check").buttonset();
	jQuery("#mkr-range-slider").slider({
      range: true,
      min: 2,
      max: 33,
      values: [ '.$floor_b.', '.$floor_e.' ],
      slide: function( event, ui ) {
		jQuery( ".rsu-val1" ).val("от " + ui.values[ 0 ]);
		jQuery( ".rsu-val2" ).val("до " + ui.values[ 1 ] );
		
      },
	  stop: function(event, ui) {
				jQuery("#floor_b").val(ui.values[ 0 ]);
				jQuery("#floor_e").val(ui.values[ 1 ]);			
			}
    });
	
	jQuery("#mkr-range-slider").find("span:first").append(\'<input type="text" name="rsu-val1" value="от 2" readonly class="rsu-val1">\');
	jQuery("#mkr-range-slider").find("span:last").append(\'<input type="text" name="rsu-val2" value="до 33" readonly class="rsu-val2">\');
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
	jQuery(".submit_btn").click(function(){
		get_flats_category("'.$catid.'");	
	})
	
	jQuery(document).on("click", "[href$=\'#ch_direction\']", function(e){
		e.preventDefault();
		
		var type = jQuery(this).attr("data-type");
		var col = jQuery(this).attr("data-col");
		
		jQuery("#order_type").val(type);
		jQuery("#order_col").val(col);
				
		get_flats_category("'.$catid.'");
		
	})
	
})	
/* map */
	function init () {
				var map = new ymaps.Map("map", {
						center: [55.7566,37.8503],
						zoom: 18
						
					}, {
						searchControlProvider: "yandex#search"
					});
					
						var squareLayout = ymaps.templateLayoutFactory.createClass(\'<div class="placemark_layout_container"><a href="'.$_SERVER['REQUEST_URI'].'?dom=1" class="placemark_layout_container_a"><span>2</span></a></div>\');
						
						var placemark = new ymaps.Placemark([55.7566,37.8503], {
							
							}, {																
								iconLayout: squareLayout,									
								iconShape: {   
									type: \'Rectangle\',
									coordinates: [[-29, -73],[29, 0]]
								}
							});
						map.geoObjects.add(placemark);
			
				map.behaviors.disable(\'scrollZoom\');
			}
				ymaps.ready(init);
				
				
	/* end map */
	');

?>
					
<!-- --------------------------------------------------------- -->
<script>
</script>
<section class="mkr-page">
			<div class="center-box">
				<div class="title-all">
					<h1>Купить квартиру в ЖК «Маяк», Комсомольская 2 в Реутове без переплат и комиссий</h1>
					<p>Квартиры в новостройке Комсомольская 2 в Реутове от 5 млн. рублей!</p>
				</div>
				<div class="mkr-area" id="map" style="height: 600px; width: 100%;">
					

				</div>
				<div class="mkr-filter">
					<form action="">
						<div class="wrap left">
							<p>Комнат</p>
							<div class="mod_check">
								<input type="checkbox" class="rooms-need" value="1" id="check1" <?php if(isset($checked_r) && in_array('1', $checked_r)){echo 'checked';}?>><label for="check1">1</label>
								<input type="checkbox" class="rooms-need" value="2" id="check2" <?php if(isset($checked_r) && in_array('2', $checked_r)){echo 'checked';}?>><label for="check2">2</label>
								<input type="checkbox" class="rooms-need" value="3" id="check3" <?php if(isset($checked_r) && in_array('3', $checked_r)){echo 'checked';}?>><label for="check3">3</label>
								<input type="checkbox" class="rooms-need" value="4" id="check4" <?php if(isset($checked_r) && in_array('4', $checked_r)){echo 'checked';}?>><label for="check4">4+</label>
							</div>
						</div>
						<div class="wrap">
							<p>Этаж</p>
							<div class="obertka">
								<div id="mkr-range-slider" class="range-slider-style"></div>
							</div>
						</div>
						<div class="wrap square-wrap">
							<p>Площадь от:</p>
							<input type="text" id="square-from" value="<?php if(isset($values['general_space_b'])){echo $values['general_space_b'];} ?>">
							<p>до:</p>
							<input type="text" id="square-to" value="<?php if(isset($values['general_space_e'])){echo $values['general_space_e'];} ?>">
							<p>м²</p>
						</div>
						<div class="wrap price-wrap">
							<p>Цена до:</p>
							<input type="text" id="price" value="<?php if(isset($values['price_e'])){echo $values['price_e'];} ?>">

						</div>
						<div class="wrap wrap-submit">
							<input type="button" value="Подобрать" class="submit_btn">
						</div> 
					</form>
				</div>
				<div class="mkr-table">
					<h2>Квартиры в продаже <span class="result">(#)</span></h2>
					<div class="list-flats">
					
					</div>
					<div id="pagination_list" class="navigation-list">
						<ul class="wrap">
							<li class="pagination">
								<ul>
									<li><span>1</span></li>
									<li><a href="#">2</a></li>
									<li class="tochki-zvetochki"><a href="#">...</a></li>
									<li><a href="#">43</a></li>
								</ul>
							</li>
							<li class="next-page nav-btn"><a href="#" title="Следующая">Дальше ></a></li>
							<li class="prew-page nav-btn"><a href="#" title="Предыдущая">< Назад</a></li>
						</ul>
					</div>
					
					<div class="mkr-ban">
						<h3>Бесплатный подбор квартиры</h3>
						<a href="#call_me" class="open-modal" title="Бесплатная консультация">Бесплатная консультация</a>
					</div>
					<div class="mkr-text">
						<h3>Поможем купить квартиру на выгодных условиях в ЖК «Маяк» в Реутове</h3>
						<p><b>Жилой комплекс на Комсомольской 2 в Реутове</b> – это современный проект монолитно-кирпичных жилых домов, который располагается в 5 минутах ходьбы от МКАДа. Жители комплекса могут пользоваться всеми преимуществами современного города – инфраструктура включает массу торговых, образовательных и развлекательных объектов. Мы поможем вам подобрать и <b>купить квартиру в новостройке «Маяк» на Комсомольской 2 в Реутове от застройщика</b>. Более того, мы напрямую сотрудничаем с застройщиком «ЦентрСрой», поэтому сможем договориться о скидках на покупку квартиры. Если же вы хотите <b>купить квартиру в ипотеку</b>, мы поможет не только получить кредитование, но и собрать для этого полный пакет необходимых документов. </p>
						<h4>Сопровождение сделки и защита от рисков</h4>
						<p>Мы осуществляем <b>юридическое сопровождение сделки</b>, поверяем застройщика и новостройку, сводим к нулю риски связаться с долгостроем или недостроем. Поэтому мы сделаем все, чтобы покупка жилья стала для вас выгодным приобретением без проблем и траты нервов.</p>
						<h4>Наша задача выполнена, только если вы полностью довольны покупкой</h4>
						<p>Мы собираем базу из лучших <b>новостроек города Реутов</b>, которые не только отличаются выгодным расположением, но и соответствуют соотношению «цена-качество». В первую очередь мы учитываем ваш пожелания по будущей квартире, но обязательно проверяем документы на строительство и финансирование объекта. Таким образом, мы минимизируем риски связаться с недостроем или долгостроем. Также мы поможем вам <b>получить ипотеку на квартиру в новостройке Реутова</b>, соберем необходимые документы и подберем для вас наиболее выгодные условия кредитования. Мы сделаем се, что сделка прошла гладко, и вы не столкнулись с проблемами с новостройкой после оформления сделки! </p>
						
					</div>
				</div>
				
			</div>
		</section>
<form id="object_filter">
	<input type="hidden" name="rooms"			id="rooms" 				value="<?php if(isset($values['rooms'])){echo $values['rooms'];} ?>" />
	<input type="hidden" name="price_b"			id="price_b" 			value="<?php if(isset($values['price_b'])){echo $values['price_b'];} ?>" />
	<input type="hidden" name="price_e"			id="price_e" 			value="<?php if(isset($values['price_e'])){echo $values['price_e'];} ?>" />
	<input type="hidden" name="general_space_b"	id="general_space_b" 	value="<?php if(isset($values['general_space_b'])){echo $values['general_space_b'];} ?>" />
	<input type="hidden" name="general_space_e"	id="general_space_e" 	value="<?php if(isset($values['general_space_e'])){echo $values['general_space_e'];} ?>" />
	<input type="hidden" name="floor_b"			id="floor_b" 			value="<?php if(isset($values['floor_b'])){echo $values['floor_b'];} ?>" />
	<input type="hidden" name="floor_e"			id="floor_e" 			value="<?php if(isset($values['floor_e'])){echo $values['floor_e'];} ?>" />
	<input type="hidden" name="start_limit"				id="start_limit" 	        value="<?php echo $limitstart; ?>" />
	<input type="hidden" name="limit"					id="limit" 			        value="<?php echo $curr_link; ?>" />
	<input type="hidden" name="order_col"		id="order_col" 			value="<?php if(isset($values['order_col'])){echo $values['order_col'];}else{echo 'price';} ?>" />
	<input type="hidden" name="order_type"		id="order_type" 		value="<?php if(isset($values['order_type'])){echo $values['order_type'];}else{echo 'asc';} ?>" />
	
	
	<input type="hidden" name="catid"			id="catid" 				value="<?php echo $catid; ?>" />
</form>	