<?php 
defined('_JEXEC') or die;
$catid = $this->category->id;
$doc = JFactory::getDocument();
	$doc->addStyleSheet(JURI::base( true ).'/templates/weeb/jquery-ui-1.11.4/jquery-ui.css');	
	//$doc->addScript('http://cdn.jsdelivr.net/jquery/1.11.0/jquery.min.js');
	$doc->addScript('//yandex.st/jquery/cookie/1.0/jquery.cookie.min.js');
	$doc->addScript(JURI::base( true ).'/templates/weeb/jquery-ui-1.11.4/jquery-ui.js');	
	//$doc->addScript(JURI::base( true ).'/templates/weeb/js/common.js');	
	
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
// stores the device context of the canvas we use to draw the outlines
// initialized in myInit, used in myHover and myLeave
var hdc;

// shorthand func
function byId(e){return document.getElementById(e);}

// takes a string that contains coords eg - "227,307,261,309, 339,354, 328,371, 240,331"
// draws a line from each co-ord pair to the next - assumes starting point needs to be repeated as ending point.
function drawPoly(coOrdStr)
{
    var mCoords = coOrdStr.split(",");//alert(mCoords);
    var i, n;
    n = mCoords.length;

    hdc.beginPath();
    hdc.moveTo(mCoords[0], mCoords[1]);
    for (i=2; i<n; i+=2)
    {
        hdc.lineTo(mCoords[i], mCoords[i+1]);
    }
    hdc.lineTo(mCoords[0], mCoords[1]);
	hdc.fill();
    hdc.stroke();
	
}

function drawRect(coOrdStr)
{
    var mCoords = coOrdStr.split(",");
    var top, left, bot, right;
    left = mCoords[0];
    top = mCoords[1];
    right = mCoords[2];
    bot = mCoords[3];
    hdc.strokeRect(left,top,right-left,bot-top); 
}

function myHover(element)
{
    var hoveredElement = element;
    var coordStr = element.getAttribute("coords");
    var areaType = element.getAttribute("shape");

    switch (areaType)
    {
        case "polygon":
        case "poly":
            drawPoly(coordStr);
            break;

        case "rect":
            drawRect(coordStr);
    }
}

function myLeave()
{
    var canvas = byId("myCanvas");
    hdc.clearRect(0, 0, canvas.width, canvas.height);
}

function myInit()
{
    // get the target image
    var img = byId("map_img");

    var x,y, w,h;

    // get it"s position and width+height
    x = img.offsetLeft;
    y = img.offsetTop;
    w = img.clientWidth;
    h = img.clientHeight;

    // move the canvas, so it"s contained by the same parent as the image
    var imgParent = img.parentNode;
    var can = byId("myCanvas");
    imgParent.appendChild(can);

    // place the canvas in front of the image
    can.style.zIndex = 1;

    // position it over the image
    can.style.left = x+"px";
    can.style.top = y+"px";

    // make same size as the image
    can.setAttribute("width", w+"px");
    can.setAttribute("height", h+"px");

    // get it"s context
    hdc = can.getContext("2d");

    // set the "default" values for the colour/width of fill/stroke operations
    hdc.fillStyle = "rgba(255, 255, 255, 0.50)";
    hdc.strokeStyle = "rgba(255, 255, 255, 0.80)";
	hdc.lineWidth = 2;	

}
	window.onload = function () {		
		get_flats_category("'.$catid.'");
		myInit();
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
 
	');

?>
					
<!-- --------------------------------------------------------- -->
<script>
</script>
<section class="mkr-page">
			<div class="center-box">
				<div class="title-all">
					<h1>Купить квартиру в ЖК «Микрорайон 6А» - новостройке в Реутове от застройщика</h1>
					<p>Выберите лучшую квартиру от застройщика без переплат, комиссий и наценок</p>
				</div>
				<div class="mkr-area">
					<canvas id='myCanvas'></canvas>
					<img usemap="#map" id="map_img" src="templates/weeb/images/r6a_v2.jpg" alt="">
					<p><map name="map">
						<area class="maparea" onmouseover='myHover(this);' onmouseout='myLeave();' shape="poly" alt="корпус 7" coords="441,371,501,394,508,396,514,394,517,392,521,388,524,384,524,377,523,370,520,368,511,334,507,327,498,324,493,322,486,324,484,326,475,322,474,319,466,315,463,318,450,313,450,311,442,307,438,308,427,303,425,299,418,295,415,297,408,295,405,287,396,280,384,281,375,286,372,291,372,299,378,310,364,320,308,322,308,375,438,376" href="<?php echo $url;?>?dom=7">
						<area class="maparea" onmouseover='myHover(this);' onmouseout='myLeave();' shape="poly" alt="корпус 8" coords="557,383,517,385,517,439,648,438,650,383,645,383,646,377,645,332,643,325,637,320,629,318,622,318,619,318,612,325,600,325,599,323,589,323,588,326,577,327,571,321,568,319,566,320,559,320,552,323,547,328,544,333,544,340,545,344" href="<?php echo $url;?>?dom=8">
						<area class="maparea" onmouseover='myHover(this);' onmouseout='myLeave();' shape="poly" alt="корпус 9" coords="683,381,676,375,676,331,677,324,681,317,688,315,696,314,697,315,700,313,708,318,708,321,719,320,721,316,730,316,731,319,743,318,746,314,750,311,752,313,757,311,762,311,770,314,775,320,776,327,768,369,764,376,754,382,795,383,797,383,796,436,665,436,665,383" href="<?php echo $url;?>?dom=9">
						<area class="maparea" onmouseover='myHover(this);' onmouseout='myLeave();' shape="poly" alt="корпус 5" coords="804,305,794,339,827,362,840,348,845,340,861,301,861,294,874,293,873,240,742,240,742,293,787,294" href="<?php echo $url;?>?dom=5">
						<area class="maparea" onmouseover='myHover(this);' onmouseout='myLeave();' shape="poly" alt="корпус 6" coords="895,275,889,283,885,288,885,295,866,338,898,364,911,350,917,340,936,308,957,295,1027,294,1028,294,1026,240,896,240" href="<?php echo $url;?>?dom=6">
						<area class="maparea" onmouseover='myHover(this);' onmouseout='myLeave();' shape="poly" alt="корпус 1" coords="626,150,627,114,757,114,758,115,758,168,688,169,668,181,674,170,664,170,664,214,639,237,623,230,615,221,611,176,611,168,615,159,620,154" href="<?php echo $url;?>?dom=1">
						<area class="maparea" onmouseover='myHover(this);' onmouseout='myLeave();' shape="poly" alt="корпус 2" coords="589,164,599,162,599,109,468,109,468,163,514,163,533,176,530,164,539,164,538,173,538,177,545,216,554,230,570,240,594,219,595,206" href="<?php echo $url;?>?dom=2">	
						<area class="maparea" onmouseover='myHover(this);' onmouseout='myLeave();' shape="poly" alt="корпус 3" coords="516,169,385,168,385,196,376,200,370,209,367,217,370,223,373,229,394,273,409,283,415,280,434,267,438,257,430,235,446,223,516,223" href="<?php echo $url;?>?dom=3">
						<area class="maparea" onmouseover='myHover(this);' onmouseout='myLeave();' shape="poly" alt="корпус 4" coords="342,204,356,204,356,149,225,150,225,203,270,204,291,218,286,205,300,206,293,214,293,222,296,227,323,273,341,285,371,263" href="<?php echo $url;?>?dom=4">	
					</map></p>
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
							<!--<p>за</p>
							<div class="currency-wrap">
								<div class="currency-box">
									<span class="currency-label">все</span>
									<div class="currency-hidden">
										<input type="radio" name="currency-radio" id="cr_1">
										<label for="cr_1">все</label>
										<input type="radio" name="currency-radio" id="cr_2">
										<label for="cr_2">м²</label>
									</div>
								</div>
							</div>-->
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
						<h3>«Микрорайон 6А» – успешный строительный проект в Реутове  </h3>
						<p><b>ЖК «Микрорайон 6А» в Реутове</b> стал одним из самых успешных строительных проектов в городе в силу ряда причин: расположение на границе с Москвой, мощная инфраструктура, хорошая экология, отличная транспортная доступность и доступная ценовая политика. <b>Цены на квартиры в жилом комплексе «Микрорайон 6А» в Реутове</b> стартуют от 4,2 млн рублей, поэтому достаточно доступны для данного района.</p>
						<h4>Жилье класса «комфорт» в условиях хорошей экологии</h4>
						<p>Новостройка представляет собой жилье класса «комфорт» в монолитно-кирпичном доме с высокой тепло- и звукоизоляцией. При этом <b>стоимость квартир в «Микрорайоне 6А»</b> делает их доступным жильем для молодых семей, которые ищут тишины и благополучной атмосферы для воспитания детей. Каждый желающий может <b>купить готовую квартиру в новостройке ЖК «Микрорайон 6А»</b> или квартиру на стадии строительства в <b>8 корпусе «Мкр. 6А» в Реутове от застройщика «ЦентрСтрой»</b>. Сотрудники Официального центра продаж квартир в новостройках в Реутове помогут вам подобрать квартиру и оформить сделку купли-продажи. </p>
						<h4>Благополучный район Подмосковья для качественной жизни </h4>
						<p>Официальный центр продаж квартир в новостройках в Реутове осуществляет <b>поиск и подбор квартир в новостройках города Реутов</b>. Если вы сделали в пользу загородной жизни в тихом и благоустроенном подмосковном районе, мы поможем вам найти и <b>купить квартиру в новостройках в Реутове</b> у проверенных застройщиков и по выгодным ценам. Сегодня <b>жилые комплексы в Реутове</b> предлагают своим жильцам комфортные условия для жизни, развитую инфраструктуру рядом с домом, удобный выезд к МКАДу и все необходимое для отдыха, покупок, бизнеса и развлечений. </p>
						
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