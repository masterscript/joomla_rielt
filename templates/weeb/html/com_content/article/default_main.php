<?php 
defined('_JEXEC') or die;
$doc = JFactory::getDocument();

	$doc->addStyleSheet(JURI::base( true ).'/templates/weeb/jquery-ui-1.11.4/jquery-ui.css');
	$doc->addStyleSheet(JURI::base( true ).'/templates/weeb/css/home.css');
	

//$doc->addScript('http://cdn.jsdelivr.net/jquery/1.11.0/jquery.min.js');
$doc->addScript('http://api-maps.yandex.ru/2.1/?lang=ru-RU');

$doc->addScript('//yandex.st/jquery/cookie/1.0/jquery.cookie.min.js');
//$doc->addScript('http://cdn.jsdelivr.net/jquery/1.11.0/jquery.min.js');
	$doc->addScript(JURI::base( true ).'/templates/weeb/jquery-ui-1.11.4/jquery-ui.js');		
	//$doc->addScript(JURI::base( true ).'/templates/weeb/js/common.js');
$doc->addScript('http://api-maps.yandex.ru/2.0/?load=package.full,package.geoObjects,package.route&lang=ru-RU');
$doc->addScriptDeclaration('
	function get_flats(){
		var wrap  = jQuery("#doma_info");
		var form = jQuery("#object_filter");
		var limit = jQuery("#limit").val();
		var mkr = jQuery(".mod_select");
		var limitstart = jQuery("#limitstart").val();
		var filter_data = form.serialize();		

		jQuery("#loading-filter").show();
		jQuery.ajax({
			url: "index.php?option=com_flats&task=get_flats&format=raw", // путь к обработчику
			type: "POST", // метод передачи
			dataType: "json",
			success: function(json){
				jQuery("#loading-filter").hide();
				if(json.status=="ok"){
					wrap.html(json.html); 					
					mkr.html(json.mkr);	
					mkr.selectmenu();					
					var arr = json.items;
					/*arr = JSON.parse(arr);*/
					arr = arr.split("//");
					jQuery("#map").empty();
					
					
			function init () {
				var map = new ymaps.Map("map", {
						center: [55.7583,37.8620],
						zoom: 12
						
					}, {
						searchControlProvider: "yandex#search"
					}),
					

					// Создание макета содержимого балуна.
					// Макет создается с помощью фабрики макетов с помощью текстового шаблона.
					BalloonContentLayout = ymaps.templateLayoutFactory.createClass("");
						var all_vk = 0;
					arr.forEach(function(item, i, arr){
						var mapinfo = item.split("&&");
						
						BalloonContentLayout = ymaps.templateLayoutFactory.createClass(\'<div style="margin: 10px;">\' +
							\'<div class="map-label">\'+
								\'<div class="map-label-text">\'+
									\'<h3>\'+mapinfo[1]+\'</h3>\'+
									\'<p><span>Квартир:</span>\'+mapinfo[2]+\'</p>\'+
									\'<p><span>Комнат:</span>\'+mapinfo[6]+\'</p>\'+
									\'<p><span>Цена от:</span>\'+mapinfo[5]+\' руб.</p>\'+
									\'<p><span>Город:</span>Реутов</p>\'+
									\'<p><span>Улица:</span>\'+mapinfo[7]+\'</p>\'+
									\'<a href="\'+mapinfo[0]+\'">Подробнее</a>\'+
								\'</div>\'+
								\'<div class="map-label-img">\'+
									\'<img src="\'+mapinfo[8]+\'" alt="">\'+
								\'</div>\'+
							\'</div>\'+
						\'</div>\');
						
						
						
						
						var squareLayout = ymaps.templateLayoutFactory.createClass(\'<div class="placemark_layout_container" onmouseover="lol(this);"><span>\'+mapinfo[2]+\'</div>\');
						all_vk = Number(all_vk)+Number(mapinfo[2]);
						var placemark = new ymaps.Placemark([mapinfo[3], mapinfo[4]], {
	
							}, {
								balloonContentLayout: BalloonContentLayout,
								// Запретим замену обычного балуна на балун-панель.
								// Если не указывать эту опцию, на картах маленького размера откроется балун-панель.
								balloonPanelMaxMapArea: 0,
								
								/*iconLayout: \'default#image\',
								iconImageHref: \'/templates/weeb/images/map-purple-icon.png\',
								iconImageSize: [58, 73],
								iconImageOffset: [-3, -73]*/
								
								iconLayout: squareLayout,									
								iconShape: {   
									type: \'Rectangle\',
									coordinates: [[-29, -73],[29, 0]]
								},
																
							});
						map.geoObjects.add(placemark);
						
						if(mapinfo[9] == "10"){
							placemark.balloon.open()
						}
					})
					jQuery("#all_vk").text(all_vk);
				
				map.controls
				// Кнопка изменения масштаба.
						.add("zoomControl", { left: 5, top: 5 })
						// Список типов карты
						.add("typeSelector")
						
				map.behaviors.disable(\'scrollZoom\');
			}
					ymaps.ready(init);

				}else{
					alert("Ошибка");
				}
	jQuery( ".placemark_layout_container" ).hover(
		function() {
			alert ("a");
		}
	);
	jQuery( ".placemark_layout_container" ).click(function(){
		alert ("a");
	})
			}
		});
	}

	window.onload = function () {		
		get_flats();
	}	
	jQuery(function(){
	$.cookie("filter_main_go", null);
	$.cookie("filter_main", null);
	jQuery(".mod_check").buttonset();		


	
	function addcookie(name, value){	
		var fmain  = $.cookie("filter_main");
		if(fmain && fmain != ""){
			if(fmain.indexOf(name) + 1){
				var arr = fmain.split("&");
				var filter_main = "";
				for(var i=0; i<arr.length; i++){
					var n = arr[i].split("=");
					if(n[0] == name){
						if(i == 0){
							filter_main = name+"="+value;
						}else{
							filter_main = filter_main+"&"+name+"="+value;
						}
					}else{
						if(i == 0){
							filter_main = arr[i];
						}else{
							filter_main = filter_main+"&"+arr[i];
						}
					}
				}
				$.cookie("filter_main", filter_main);			
				
			}else{
				$.cookie("filter_main", fmain+"&"+name+"="+value);
			}			
		}else{
			$.cookie("filter_main", name+"="+value);
		}
	}	
	
	function change_el(){
		//Количество комнат
		var rooms_list = jQuery("input.rooms-need");
		var rooms = "";
		for(var i=0; i<rooms_list.length; i++){
			if(jQuery("#"+rooms_list[i].id).prop("checked")){
				rooms += " " + rooms_list[i].value;
			}
		}
		addcookie("rooms", rooms);
	}		
	jQuery("input.rooms-need").change(function(){
		change_el();
	})	
	jQuery("#price").change(function(){
		var price = jQuery("#price").val();
		addcookie("price_e", price);
	})	
	
	})

	function filter_main(){
		$.cookie("filter_main_go", "1");
		var url = jQuery(".mod_select").val();
		window.location = "/"+url;
	}
 function lol(a){
	 jQuery(a).parent().parent().click();
 }

	');

?>
<section class="result-plashka">
	<div class="center-box">
		<p><b><span id="all_vk">#</span> квартир</b> в новостройках Реутова</p>
	</div>
</section>
		<section class="map-box">
			<div id="map"></div>
			<div class="main-filter">
				<div class="center-box">
					<form action="">
						<div class="wrap left">
							<p>Комнат</p>
							<div class="mod_check">
								<input type="checkbox" class="rooms-need" value="1" id="check1"><label for="check1">1</label>
								<input type="checkbox" class="rooms-need" value="2" id="check2"><label for="check2">2</label>
								<input type="checkbox" class="rooms-need" value="3" id="check3"><label for="check3">3</label>
								<input type="checkbox" class="rooms-need" value="4" id="check4"><label for="check4">4+</label>
							</div>
						</div>
						<div class="wrap">
							<p>Район</p>
							<select class="mod_select">
								
							</select>
						</div>
						<div class="wrap price-wrap">
							<p>Цена до</p>
							<input type="text" id="price" class="price">
							<p  class="price-wrap-last">руб.</p>
						</div>
						<div class="wrap wrap-submit">
							<input type="button" onclick="javascript:filter_main();" value="Подобрать">
						</div>
					</form>
				</div>
			</div>
		</section>
		<section class="main-nov">
			<div class="center-box">
				<?php 

					$mods = JModuleHelper::getModules("banner_main"); 
					if(count($mods)>0){
						foreach($mods as $mod){
							echo JModuleHelper::renderModule($mod, array( 'style' => 'none' ));
						}
					}
				?>
				<h2>Новостройки</h2>

				<div class="nov-wrap" id="doma_info">
					
				</div>
				<!--<a href="#" title="Все новостройки" class="all-nov" >Все новостройки</a>-->
			</div>
		</section>
		<section class="main-citata mw">
			<div class="center-box">
				<div class="sale-review-text">
					<p><i>Мы очень ответственно подходим к выбору новостроек и работаем только с застройщиками, у которых прекрасная репутация и качество застройки. Т.к. мы работаем только по прямым договорам с застройщиками или инвесторами, мы не делаем никаких наценок или комиссий . Также мы отказались от работы с некачественной застройкой, неликвидными жилыми комплексами и квартирами с тесными и неудобными планировками.</i><p> 

					<p><i>Я уверен, что все наши клиенты будут жить в комфортных условиях и все дома будут сданы вовремя.</i></p>
					<small><i>Жигалов Евгений Юрьевич, генеральной директор<br> Офиса «На Юбилейном» г. Реутов</i></small>
				</div>
			</div>
		</section>
		<section class="main-best mw">
			<div class="center-box">
				<h2>Среди новостроек в Реутове мы выбрали для Вас только лучшие</h2>
				<div class="main-best-wrap">
					<ul>
						<li>Надежные застройщики <br>с безукоризненной репутацией</li>
						<li>Современные здания высокого<br>качества со всеми коммуникациями</li>
						<li>Комфортные квартиры<br>с продуманными планировками</li>
					</ul>
				</div>
			</div>
		</section>
		<section class="main-free-select mw">
			<div class="center-box">
				<div class="main-free-select-wrap">
					<h2>Бесплатный подбор квартиры</h2>
					<a href="#call_me" class="open-modal" title="Оставить заявку">Оставить заявку</a>
				</div>
			</div>
		</section>
		<div class="mkr-text mw">
			<div class="center-box">
				<h3>Мы поможем вам купить квартиру в новостройке города Реутов в рамках ваших пожеланий и бюджета!</h3>
				<h4>Забота об интересах каждого клиента </h4>
				<p><b>Риэлторы Официального центра продаж квартир в новостройках в Реутове помогут вам подобрать и купить квартиру</b>, руководствуясь вашими пожеланиями по расположению, качеству, площади, а главное – в рамках вашего бюджета. У нас всегда есть, что предложить клиенту, так как мы собрали самую большую <b>базу квартир в новостройках города Реутов</b>, и регулярно обновляем ее, пополняя новыми предложениями и объектами.</p>
				
				<h4>Гарантии защиты и безопасности </h4>
				
				<p>Мы понимаем, что <b>покупка квартиры в новостройке Реутова </b>– это серьезный шаг, к которому наш клиент готовится не один месяц, и даже год. Поэтому стремимся обеспечить безопасность на всех этапах сделки: выполняем <b>проверку застройщика и новостройки, проводим оценку квартиры и проверяем все документы</b>, сопровождаем следку на всех ее этапах. Поэтому каждый клиент получает гарантию защиты от любых проблем с покупкой жилья.</p>
				<h4>Выгодные условия сделки</h4>
				<p>Мы всегда стремимся предложить нашему клиенту больше, чем просто <b>продажа квартир в новостройках города Реутов</b>, поэтому у нас есть акции и скидки, с помощью которых клиент может сэкономить деньги, к примеру, на будущий ремонт. Также мы осуществляем <b>прямые продажи квартир от застройщиков</b>, поэтому можем предложить клиенту более выгодные, а порой и эксклюзивные условия сделки.</p>
			
			</div>
		</div>


