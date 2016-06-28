<?php 
defined('_JEXEC') or die;

$doc = JFactory::getDocument();
	$doc->addStyleSheet(JURI::base( true ).'/templates/weeb/jquery-ui-1.11.4/jquery-ui.css');	
	//$doc->addScript('http://cdn.jsdelivr.net/jquery/1.11.0/jquery.min.js');
	$doc->addScript(JURI::base( true ).'/templates/weeb/jquery-ui-1.11.4/jquery-ui.js');

	$doc->addScript(JURI::base( true ).'/templates/weeb/js/jquery.slimscroll.min.js');	
	//$doc->addScript(JURI::base( true ).'/templates/weeb/js/common.js');	


$dom = $_GET['dom'];
$catid = $this->category->id;

if($catid == 10){
	$dom1 = array(1,2,3,4,5,6);
	$dom2 = array(8,9);
	if(in_array($dom, $dom1)){
		$plan_dom = 1;
		$auto_etazh = 2;
	}elseif(in_array($dom, $dom2)){
		$plan_dom = 8;
		$auto_etazh = 2;
	}else{
		$plan_dom = $dom;
		$auto_etazh = 2;
	}
	$name = 'Микрорайон 6а';
}elseif($catid == 14){
	$plan_dom = $dom;
	$name = 'ЖК «Маяк»';
}

	$db	= JFactory::getDbo();
	$query1 = $db->getQuery(true);
	$query1->select('*');
	$query1->from('#__flats');
	$query1->where('published = 1');
	$query1->where('catid = '.$catid);	
	$query1->where('dom = '.$dom);		
	$db->setQuery($query1);
	$flats = $db->loadObjectList();
	$dom_sekciya = array();
	$m_price = '';
	$sqm_price = '';
	foreach($flats as $flat){
		$p = (int)$flat->price_2;
		$mp = (int)$flat->sqm_price;
		
		if(empty($m_price)){
			$m_price = $p;
		}else{
			if($m_price > $p){
				$m_price = $p;
			}
		}
		if(empty($sqm_price)){
			$sqm_price = $mp;
		}else{
			if($sqm_price > $mp){
				$sqm_price = $mp;
			}
		}			
		if(!isset($dom_sekciya[$flat->sekciya][$flat->etazh])){
			$dom_sekciya[$flat->sekciya][$flat->etazh] = 0;
		}
		$dom_sekciya[$flat->sekciya][$flat->etazh] = $dom_sekciya[$flat->sekciya][$flat->etazh]+1;
	}
	
$doc->addScript(JURI::base( true ).'/templates/weeb/js/dom.js');		
$doc->addScriptDeclaration('
jQuery(function(){
	
	
	
	jQuery(".mod_check").buttonset();
	jQuery("#mkrSquare").slider({
      range: true,
      min: 1,
      max: 150,
      values: [ 1, 150 ],
      slide: function( event, ui ) {
		jQuery( "#mkrSquareLeft" ).val(ui.values[ 0 ]);
		jQuery( "#mkrSquareRight" ).val(ui.values[ 1 ] );
      },
	  stop: function(event, ui) {
				jQuery("#general_space_b").val(ui.values[ 0 ]);
				jQuery("#general_space_e").val(ui.values[ 1 ]);	
				get_flats();				
			}
    });
	
	jQuery("#mkrPrice").slider({
      range: true,
      min: 1,
      max: 50,
      values: [ 1, 50 ],
      slide: function( event, ui ) {
		jQuery( "#mkrPriceLeft" ).val(ui.values[ 0 ]);
		jQuery( "#mkrPriceReft" ).val(ui.values[ 1 ] );
      },
	  stop: function(event, ui) {
				jQuery("#price_b").val(ui.values[ 0 ]*1000000);
				jQuery("#price_e").val(ui.values[ 1 ]*1000000);	
				get_flats();				
			}
    });
	
	jQuery("#mkrFloor").slider({
      range: true,
      min: 2,
      max: 33,
      values: [ 2, 33 ],
      slide: function( event, ui ) {
		jQuery( "#mkrFloorLeft" ).val(ui.values[ 0 ]);
		jQuery( "#mkrFloorReft" ).val(ui.values[ 1 ] );
      },
	  stop: function(event, ui) {
				jQuery("#floor_b").val(ui.values[ 0 ]);
				jQuery("#floor_e").val(ui.values[ 1 ]);	
				get_flats();				
			}
    });

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
		get_flats();
	})

})
');
?>

<!-- ------------------------------------------------ -->
		<section class="mkr-planirovka">
			<div class="center-box">
				<div class="title-all">
					<h1>Купить квартиру в <?php echo $name; ?> - новостройке в Реутове</h1>
					<p>Выберите лучшую квартиру от застройщика без переплат, комиссий и наценок</p>
				</div>
				<div class="mkr-planirovka-top">
					<div class="kr-planirovka-info">
						<h2>Квартиры в новостройке <?php echo $name; ?></h2>
						<h3>от <?php echo number_format($m_price, 0, ' ', ' '); ?> рублей!</h3>
						<p>от <?php echo number_format($sqm_price, 0, ' ', ' '); ?> руб. за м²</p>
						<img src="/templates/weeb/images/arrow-yellow-down.png" alt="">
					</div>
					<div class="mkr-planirovka-filter">
						<h3>Поиск квартиры по параметрам</h3>
						<div class="line-top">
							<div class="wrap left">
								<p>Комнат</p>
								<div class="mod_check">
									<input type="checkbox" class="rooms-need" value="1" id="check1"><label for="check1">1</label>
									<input type="checkbox" class="rooms-need" value="2" id="check2"><label for="check2">2</label>
									<input type="checkbox" class="rooms-need" value="3" id="check3"><label for="check3">3</label>
								</div>
							</div>
							<div class="wrap">
								<p>Площадь</p>
								<span class="label-input">от</span>
								<input type="text" value="1" id="mkrSquareLeft" readonly class="left-input">
								<div class="obertka">
									<div id="mkrSquare" class="range-slider-line range-slider-style"></div>
								</div>
								<span class="label-input">до</span>
								<input type="text" value="150" id="mkrSquareRight" readonly class="right-input">
								<span class="label-input">м²</span>
							</div>
						</div>
						<div class="line-down">
							<div class="wrap left">
								<p>Этаж</p>
								<span class="label-input">с</span>
								<input type="text" value="2" id="mkrFloorLeft" readonly class="left-input">
								<div class="obertka">
									<div id="mkrFloor" class="range-slider-line range-slider-style"></div>
								</div>
								<span class="label-input">по</span>
								<input type="text" value="33" id="mkrFloorReft" readonly class="right-input">
							</div>
							<div class="wrap">
								<p>Цена</p>
								<span class="label-input">от</span>
								<input type="text" value="1" id="mkrPriceLeft" readonly class="left-input">
								<div class="obertka">
									<div id="mkrPrice" class="range-slider-line range-slider-style"></div>
								</div>
								<span class="label-input">до</span>
								<input type="text" value="50" id="mkrPriceReft" readonly class="right-input">
								<span class="label-input">млн. руб.</span>
							</div>
						</div>	
					</div>
					
				</div>
				<div class="mkr-planirovka-down">
					<div class="scheme-house">

							<div class="shema-doma"  <?php if($catid == 10 && $plan_dom !=1 || $catid == 14){echo "style='margin-top: 70px;'";}?>>
<canvas id='myCanvas'></canvas>
<div class='vspluvashka-etazh'>
	<p>Свободных кв.: <span class='info-kv-free'></span></p>
	<p>Этаж: <span class='info-etazh'></span></p>
	<p class='info-section'></p>
</div>
<div class="container-img">

<img usemap="#map"  id="map_img" src="templates/weeb/images/flat<?php echo $catid; ?>/<?php echo $catid.'-'.$plan_dom; ?>.jpg">
</div>

<p><map name="map">
<?php if($catid == 10 &&  $plan_dom == 1){ 
	$coordinates = array(
		//1 => '84,573,365,572,365,619,83,620',
		2 => '67,554,382,554,382,564,67,564',
		3 => '67,534,383,535,383,545,67,544',
		4 => '67,516,382,515,383,525,68,526',
		5 => '67,496,383,497,382,507,68,507',
		6 => '67,477,382,477,382,488,68,488',
		7 => '68,458,382,459,381,469,67,469',
		8 => '67,439,383,439,382,449,68,450',
		9 => '68,420,382,420,382,430,68,431',
		10 => '68,402,383,402,382,411,68,411',
		11 => '67,382,381,381,382,392,67,391',
		12 => '67,363,383,363,382,374,67,374',
		13 => '68,345,383,345,382,354,68,355',
		14 => '67,325,382,326,381,335,68,336',
		15 => '67,306,382,307,383,316,67,317',
		16 => '68,288,382,288,382,298,68,298',
		17 => '67,269,382,269,382,279,67,279',
		18 => '68,250,383,250,382,259,68,260',
		19 => '67,230,382,230,382,240,67,240',
		20 => '67,212,382,212,382,222,68,222', 
		21 => '68,192,382,193,382,203,67,202',
		22 => '66,173,384,174,385,183,66,184',
		23 => '63,155,387,154,386,164,64,165',
		24 => '61,135,389,135,387,145,62,145',
		25 => '60,118,390,118,390,125,60,127'
	);

	foreach($coordinates as $etazh=>$coordinate){		
		if(empty($dom_sekciya[0][$etazh])){
			$dom_sekciya[0][$etazh] = 0;
		}
		echo '<area class="etazh-area" onclick="javascript:etazh_info('.$catid.', '.$dom.', 0, '.$etazh.', '.$dom_sekciya[0][$etazh].', \'Этаж '.$etazh.'\')" title="Этаж '.$etazh.'" onmouseover="myHover(this);" data-sec="" data-floor="'.$etazh.'" data-kv="'.$dom_sekciya[0][$etazh].'" onmouseout="myLeave();" shape="poly" alt="Этаж '.$etazh.'" coords="'.$coordinate.'">';
	} ?>
<?php }elseif($catid == 10 &&  $plan_dom == 8){
		$coordinates = array(
			//1 => array(1 => '1,643,700,643,700,680,1,680', 2 => '1,643,700,643,700,680,1,680', 3 => '1,643,700,643,700,680,1,680'),
			2 => array(1 => '1,620,192,620,192,644,1,644', 2 => '205,620,496,620,496,644,205,644', 3 => '509,620,699,620,699,644,509,644'),
			3 => array(1 => '1,596,192,596,192,619,1,619', 2 => '205,596,496,596,496,619,205,619', 3 => '509,596,699,596,699,619,509,619'),
			4 => array(1 => '1,571,192,571,192,595,1,595', 2 => '205,571,496,571,496,595,205,595', 3 => '509,571,699,571,699,595,509,595'),
			5 => array(1 => '1,546,192,546,192,570,1,570', 2 => '205,546,496,546,496,570,205,570', 3 => '509,546,699,546,699,570,509,570'),
			6 => array(1 => '1,522,192,522,192,545,1,545', 2 => '205,522,496,522,496,545,205,545', 3 => '509,522,699,522,699,545,509,545'),
			7 => array(1 => '1,497,192,497,192,521,1,521', 2 => '205,497,496,497,496,521,205,521', 3 => '509,497,699,497,699,521,509,521'),
			8 => array(1 => '1,471,192,471,192,496,1,496', 2 => '205,471,496,471,496,496,205,496', 3 => '509,471,699,471,699,496,509,496'),
			9 => array(1 => '1,447,192,447,192,472,1,472', 2 => '205,447,496,447,496,472,205,472', 3 => '509,447,699,447,699,472,509,472'),
			10 => array(1 => '1,424,192,424,192,448,1,448', 2 => '205,424,496,424,496,448,205,448', 3 => '509,424,699,424,699,448,509,448'),
			11 => array(1 => '1,399,192,399,192,423,1,423', 2 => '205,399,496,399,496,423,205,423', 3 => '509,399,699,399,699,423,509,423'),
			12 => array(1 => '1,374,192,374,192,398,1,398', 2 => '205,374,496,374,496,398,205,398', 3 => '509,374,699,374,699,398,509,398'),
			13 => array(1 => '1,349,192,349,192,374,1,374', 2 => '205,349,496,349,496,374,205,374', 3 => '509,349,699,349,699,374,509,374'),
			14 => array(1 => '1,325,192,325,192,349,1,349', 2 => '205,325,496,325,496,349,205,349', 3 => '509,325,699,325,699,349,509,349'),
			15 => array(1 => '1,300,192,300,192,325,1,325', 2 => '205,300,496,300,496,325,205,325', 3 => '509,300,699,300,699,325,509,325'),
			16 => array(1 => '1,276,192,276,192,300,1,300', 2 => '205,276,496,276,496,300,205,300', 3 => '509,276,699,276,699,300,509,300'),
			17 => array(1 => '1,251,192,251,192,276,1,276', 2 => '205,251,496,251,496,276,205,276', 3 => '509,251,699,251,699,276,509,276'),
			18 => array(1 => '1,226,192,226,192,250,1,250', 2 => '205,226,496,226,496,250,205,250', 3 => '509,226,699,226,699,250,509,250'),
			19 => array(1 => '1,202,192,202,192,226,1,226', 2 => '205,202,496,202,496,226,205,226', 3 => '509,202,699,202,699,226,509,226'),
			20 => array(1 => '1,177,192,177,192,202,1,202', 2 => '205,177,496,177,496,202,205,202', 3 => '509,177,699,177,699,202,509,202'),
			21 => array(1 => '1,152,192,152,192,177,1,177', 2 => '205,152,496,152,496,177,205,177', 3 => '509,152,699,152,699,177,509,177'),
			22 => array(1 => '1,130,192,130,192,152,1,152', 2 => '205,130,496,130,496,152,205,152', 3 => '509,130,699,130,699,152,509,152'),
			23 => array(1 => '1,107,192,107,192,129,1,129', 2 => '205,107,496,107,496,129,205,129', 3 => '509,107,699,107,699,129,509,129'),
			24 => array(1 => '1,82,192,82,192,106,1,106', 2 => '205,82,496,82,496,106,205,106', 3 => '509,82,699,82,699,106,509,106')
			//25 => array(1 => '1,58,192,58,192,81,1,81', 2 => '205,58,496,58,496,81,205,81', 3 => '509,58,699,58,699,81,509,81')
		);
		foreach($coordinates as $etazh=>$val){
			foreach($val as $section=>$coordinate){
				if(empty($dom_sekciya[$section][$etazh])){
					$dom_sekciya[$section][$etazh] = 0;
				}
				echo '<area class="etazh-area" onclick="javascript:etazh_info('.$catid.', '.$dom.', '.$section.', '.$etazh.', '.$dom_sekciya[$section][$etazh].', \'Этаж '.$etazh.'\')" title="Этаж '.$etazh.'" onmouseover="myHover(this);" data-sec="'.$section.'" data-floor="'.$etazh.'" data-kv="'.$dom_sekciya[$section][$etazh].'" onmouseout="myLeave();" shape="poly" alt="Этаж '.$etazh.'" coords="'.$coordinate.'">';
			}
		}
?>

	<?php }elseif($catid == 10 &&  $plan_dom == 7){
	$coordinates = array(
			//1 => array(1 => '1,643,700,643,700,680,1,680', 2 => '1,643,700,643,700,680,1,680', 3 => '1,643,700,643,700,680,1,680'),
			2 => array(1 => '1,412,143,412,143,428,1,428', 2 => '149,412,289,412,289,429,148,428', 3 => '295,412,420,412,420,428,295,428', 4 => '427,412,565,412,565,428,427,428', 5 => '574,412,699,412,699,428,574,428'),
			3 => array(1 => '1,396,143,396,143,412,1,412', 2 => '149,396,289,396,289,412,149,412', 3 => '295,396,420,396,420,412,295,412', 4 => '427,396,565,396,565,412,427,412', 5 => '574,396,699,396,699,412,574,412'),
			4 => array(1 => '1,380,143,380,143,396,1,396', 2 => '149,380,289,380,289,396,149,396', 3 => '295,380,420,380,420,396,295,396', 4 => '427,380,565,380,565,396,427,396', 5 => '574,380,699,380,699,396,574,396'),
			5 => array(1 => '1,364,143,364,143,380,1,380', 2 => '149,364,289,364,289,380,149,380', 3 => '295,364,420,364,420,380,295,380', 4 => '427,364,565,364,565,380,427,380', 5 => '574,364,699,364,699,380,574,380'),
			6 => array(1 => '1,348,143,348,143,364,1,364', 2 => '149,348,289,348,289,364,149,364', 3 => '295,348,420,348,420,364,295,364', 4 => '427,348,565,348,565,364,427,364', 5 => '574,348,699,348,699,364,574,364'),
			7 => array(1 => '1,332,143,332,143,348,1,348', 2 => '149,332,289,332,289,348,149,348', 3 => '295,332,420,332,420,348,295,348', 4 => '427,332,565,332,565,348,427,348', 5 => '574,332,699,332,699,348,574,348'),
			8 => array(1 => '1,315,143,315,143,332,1,332', 2 => '149,315,289,315,289,332,149,332', 3 => '295,315,420,315,420,332,295,332', 4 => '427,315,565,315,565,332,427,332', 5 => '574,315,699,315,699,332,574,332'),
			9 => array(1 => '1,299,143,299,143,315,1,315', 2 => '149,299,289,299,289,315,149,315', 3 => '295,299,420,299,420,315,295,315', 4 => '427,299,565,299,565,315,427,315', 5 => '574,299,699,299,699,315,574,315'),
			10 => array(1 => '1,283,143,283,143,299,1,299', 2 => '149,283,289,283,289,299,149,299', 3 => '295,283,420,283,420,299,295,299', 4 => '427,283,565,283,565,299,427,299', 5 => '574,283,699,283,699,299,574,299'),
			11 => array(1 => '1,266,143,266,143,283,1,283', 2 => '149,266,289,266,289,283,149,283', 3 => '295,266,420,266,420,283,295,283', 4 => '427,266,565,266,565,283,427,283', 5 => '574,266,699,266,699,283,574,283'),
			12 => array(1 => '1,250,143,250,143,266,1,266', 2 => '149,250,289,250,289,266,149,266', 3 => '295,250,420,250,420,266,295,266', 4 => '427,250,565,250,565,266,427,266', 5 => '574,250,699,250,699,266,574,266'),
			13 => array(1 => '1,234,143,234,143,250,1,250', 2 => '149,234,289,234,289,250,149,250', 3 => '295,234,420,234,420,250,295,250', 4 => '427,234,565,234,565,250,427,250', 5 => '574,234,699,234,699,250,574,250'),
			14 => array(1 => '1,217,143,217,143,234,1,234', 2 => '149,217,289,217,289,234,149,234', 3 => '295,217,420,217,420,234,295,234', 4 => '427,217,565,217,565,234,427,234', 5 => '574,217,699,217,699,234,574,234'),
			15 => array(1 => '1,201,143,201,143,217,1,217', 2 => '149,201,289,201,289,217,149,217', 3 => '295,201,420,201,420,217,295,217', 4 => '427,201,565,201,565,217,427,217', 5 => '574,201,699,201,699,217,574,217'),
			16 => array(1 => '1,185,143,185,143,201,1,201', 2 => '149,185,289,185,289,201,149,201', 3 => '295,185,420,185,420,201,295,201', 4 => '427,185,565,185,565,201,427,201', 5 => '574,185,699,185,699,201,574,201'),
			17 => array(1 => '1,168,143,168,143,185,1,185', 2 => '149,168,289,168,289,185,149,185', 3 => '295,168,420,168,420,185,295,185', 4 => '427,168,565,168,565,185,427,185', 5 => '574,168,699,168,699,185,574,185'),
			18 => array(1 => '1,152,143,152,143,168,1,168', 2 => '149,152,289,152,289,168,149,168', 3 => '295,152,420,152,420,168,295,168', 4 => '427,152,565,152,565,168,427,168', 5 => '574,152,699,152,699,168,574,168'),
			19 => array(1 => '1,136,143,136,143,152,1,152', 2 => '149,136,289,136,289,152,149,152', 3 => '295,136,420,136,420,152,295,152', 4 => '427,136,565,136,565,152,427,152', 5 => '574,136,699,136,699,152,574,152'),
			20 => array(1 => '1,120,143,120,143,136,1,136', 2 => '149,120,289,120,289,136,149,136', 3 => '295,120,420,120,420,136,295,136', 4 => '427,120,565,120,565,136,427,136', 5 => '574,120,699,120,699,136,574,136'),
			21 => array(1 => '1,102,143,102,143,119,1,119', 2 => '149,102,289,102,289,119,149,119', 3 => '295,102,420,102,420,119,295,119', 4 => '427,102,565,102,565,119,427,119', 5 => '574,102,699,102,699,119,574,119'),
			22 => array(1 => '1,88,143,88,143,102,1,102', 2 => '149,88,289,88,289,102,149,102', 3 => '295,88,420,88,420,102,295,102', 4 => '427,88,565,88,565,102,427,102', 5 => '574,88,699,88,699,102,574,102'),
			23 => array(1 => '1,71,143,71,143,88,1,88', 2 => '149,71,289,71,289,88,149,88', 3 => '295,71,420,71,420,88,295,88', 4 => '427,71,565,71,565,88,427,88', 5 => '574,71,699,71,699,88,574,88'),
			24 => array(1 => '1,54,143,55,143,71,1,71', 2 => '149,54,289,55,289,71,149,71', 3 => '295,54,420,55,420,71,295,71', 4 => '427,54,565,55,565,71,427,71', 5 => '574,54,699,55,699,71,574,71'),
			25 => array(1 => '1,38,143,38,143,54,1,54', 2 => '149,38,289,38,289,54,149,54', 3 => '295,38,420,38,420,54,295,54', 4 => '427,38,565,38,565,54,427,54', 5 => '574,38,699,38,699,54,574,54')
		);
		foreach($coordinates as $etazh=>$val){
			foreach($val as $section=>$coordinate){
				if(empty($dom_sekciya[$section][$etazh])){
					$dom_sekciya[$section][$etazh] = 0;
				}
				echo '<area class="etazh-area" onclick="javascript:etazh_info('.$catid.', '.$dom.', '.$section.', '.$etazh.', '.$dom_sekciya[$section][$etazh].', \'Этаж '.$etazh.'\')" title="Этаж '.$etazh.'" onmouseover="myHover(this);" data-sec="'.$section.'" data-floor="'.$etazh.'" data-kv="'.$dom_sekciya[$section][$etazh].'" onmouseout="myLeave();" shape="poly" alt="Этаж '.$etazh.'" coords="'.$coordinate.'">';
			}
		}
	?>
	
<?php }elseif($catid == 14){
		$coordinates = array(
			//1 => array(1 => '', 2 => ''),
			2 => array(1 => '398,481,683,481,683,500,398,500', 2 => '17,489,387,489,387,507,17,507'), 
			3 => array(1 => '398,461,683,461,683,480,398,480', 2 => '17,469,387,469,387,489,17,489'),
			4 => array(1 => '398,441,683,441,683,460,398,460', 2 => '17,449,387,449,387,469,17,469'),
			5 => array(1 => '398,421,683,421,683,440,398,440', 2 => '17,429,387,429,387,449,17,449'),
			6 => array(1 => '398,401,683,401,683,420,398,420', 2 => '17,411,387,411,387,429,17,429'),
			7 => array(1 => '398,381,683,381,683,400,398,400', 2 => '17,391,387,391,387,411,17,411'),
			8 => array(1 => '398,361,683,361,683,380,398,380', 2 => '17,371,387,371,387,391,17,391'),
			9 => array(1 => '398,341,683,341,683,360,398,360', 2 => '17,351,387,351,387,371,17,371'),
			10 => array(1 => '398,321,683,321,683,340,398,340', 2 => '17,331,387,331,387,351,17,351'),
			11 => array(1 => '398,301,683,301,683,320,398,320', 2 => '17,311,387,311,387,331,17,331'),
			12 => array(1 => '398,281,683,281,683,300,398,300', 2 => '17,291,387,291,387,311,17,311'),
			13 => array(1 => '398,261,683,261,683,282,398,282', 2 => '17,271,387,271,387,291,17,291'),
			14 => array(1 => '398,243,683,243,683,262,398,262', 2 => '17,251,387,251,387,271,17,271'),
			15 => array(1 => '398,223,683,223,683,244,398,244', 2 => '17,231,387,231,387,251,17,251'),
			16 => array(1 => '398,203,683,203,683,224,398,224', 2 => '17,213,387,213,387,232,17,232'),
			17 => array(1 => '398,183,683,183,683,204,398,204', 2 => '17,193,387,193,387,212,17,212'),
			18 => array(1 => '398,163,683,163,683,184,398,184', 2 => '17,173,387,173,387,192,17,192'),
			19 => array(1 => '398,145,683,145,683,164,398,164', 2 => '17,153,387,153,387,172,17,172'),
			20 => array(1 => '398,125,683,125,683,144,398,144', 2 => '17,133,387,133,387,152,17,152'),
			21 => array(1 => '398,105,683,105,683,124,398,124', 2 => '17,113,387,113,387,132,17,132'),
			22 => array(1 => '405,87,699,87,699,104,405,104', 2 => '1,95,403,95,403,114,1,114'),
			23 => array(1 => '405,68,699,68,699,87,405,87', 2 => '1,75,403,75,403,95,1,95'),
			24 => array(1 => '405,48,699,48,699,67,405,67', 2 => '1,55,403,55,403,74,1,74'),
			25 => array(1 => '405,28,699,28,699,47,405,47', 2 => '1,35,403,35,403,55,1,55')
		);	
		foreach($coordinates as $etazh=>$val){
			foreach($val as $section=>$coordinate){
				if(empty($dom_sekciya[$section][$etazh])){
					$dom_sekciya[$section][$etazh] = 0;
				}
				echo '<area class="etazh-area" onclick="javascript:etazh_info('.$catid.', '.$dom.', '.$section.', '.$etazh.', '.$dom_sekciya[$section][$etazh].', \'Этаж '.$etazh.'\')" title="Этаж '.$etazh.'" onmouseover="myHover(this);" data-sec="'.$section.'" data-floor="'.$etazh.'" data-kv="'.$dom_sekciya[$section][$etazh].'" onmouseout="myLeave();" shape="poly" alt="Этаж '.$etazh.'" coords="'.$coordinate.'">';
			}
		}
} ?>
</map></p>
</div>											
					</div>
					<div class="table-result" id="collection-list" >
						
					</div>
				</div>
				<div class="mkr-ban">
					<h3>Бесплатный подбор квартиры</h3>
					<a href="#call_me" class="open-modal" title="Бесплатная консультация">Бесплатная консультация</a>
				</div>
				<?php if($catid==10){?>
				<div class="mkr-text">
					<h3>Квартиры в новостройках Реутова – комфортная жизнь в ближайшем Подмосковье</h3>
					<p>ЖК «Микрорайон 6А» в Реутове стал одним из самых успешных строительных проектов в городе в силу ряда причин: расположение на границе с Москвой, мощная инфраструктура, хорошая экология, отличная транспортная доступность и доступная ценовая политика. Цены на квартиры в жилом комплексе «Микрорайон 6А» в Реутове стартуют от 4,2 млн рублей, поэтому достаточно доступны для данного района.</p>
					<p>Новостройка представляет собой жилье класса «комфорт» в монолитно-кирпичном доме с высокой тепло- и звукоизоляцией. При этом стоимость квартир в «Микрорайоне 6А» делает их доступным жильем для молодых семей, которые ищут тишины и благополучной атмосферы для воспитания детей. Каждый желающий может купить готовую квартиру в новостройке ЖК «Микрорайон 6А» или квартиру на стадии строительства в 8 корпусе «Микрорайон 6А» в Реутове от застройщика «Инвестстрой». Сотрудники агентства недвижимости Миэль «В Реутове» помогут вам подобрать квартиру и оформить сделку купли-продажи. </p>
					<p>Агентство недвижимости Миэль «В Реутове» осуществляет поиск и подбор квартир в новостройках города Реутове. Если вы сделали в пользу загородной жизни в тихом и благоустроенном подмосковном районе, мы поможем вам найти и купить квартиру в новостройках Реутове у проверенных застройщиков и по выгодным ценам. Сегодня жилые комплексы в Реутове предлагают своим жильцам комфортные условия для жизни, развитую инфраструктуру рядом с домом, удобный выезд к МКАДу и все необходимое для отдыха, покупок, бизнеса и развлечений. </p>
				</div>
				<?php }elseif($catid==14){?>
					<div class="mkr-text">
						<h3>«Доступные цены на жилье класса «комфорт» в ближайшем Подмосковье</h3>
						<p><b>ЖК «Маяк» на улице Комсомольская 2 в Реутове</b> представляет собой два монолитно-кирпичных дома с отличной тепло и звукоизоляцией. Выбор планировок позволяет <b>купить 1, 2, 3-комнатную квартиру в новостройке Комсомольская 2 в Реутове</b>. Застройка отличается высоким качеством, долговечностью и экологичностью, и все это дополняется выгодным расположением практически на границе с Москвой. <b>Цены на квартиры в жилом комплексе Комсомольская 2 в Реутове</b> стартуют от 5 млн рублей, но у нас всегда есть акции и скидки, поэтому вы сможете прилично сэкономить свои средства.</p>
						<h3>Надежный застройщик и выгодная покупка жилья</h3>
						<p><b>Официальный центр продажи квартир в новостройках в Реутове</b> сотрудничает с застройщиком «ЦентрСтрой», поэтому мы можем предложить выгодные условия покупки квартиры, договоримся о скидках и поможем <b>оформить сделку по ДДУ, ФЗ 214</b>. Новостройка Комсомольская 2 в Реутове представляет собой жилье для молодых семей в тихом подмосковном районе с отличной транспортной доступностью и развитой инфраструктурой. Поэтому, если вы решили <b>купить квартиру в ЖК «Маяк» в Реутове от застройщика</b>, мы позаботимся о том, чтобы вы не столкнулись с проблемами и трудностями при оформлении сделки, а приобретение жилья стало для вас приятной переменой, а не кучей потраченных нервов. </p>
						<h3>Реутов – перспективный район Подмосковья для жизни и инвестирования</h3>
						<p>Сегодня город Реутов является одним из самых перспективных районов Подмосковья для жизни, ведь этот тихий город с хорошей экологией находится непосредственно возле столицы. Мы подобрали лучшие <b>новостройки города Реутов</b>, обеспечив, таким образом, вам отличный выбор вариантов жилья для покупки. Кроме того, <b>новостройки Реутова</b> представляют собой перспективные объекты для инвестирования, ведь жилье в ближайшем Подмосковье всегда пользуется большим спросом, особенно, если регион отличается развитой инфраструктурой. Мы поможем вам купить квартиру в новостройке Реутова от застройщика по выгодной цене, а главное – без переплат и скрытых комиссий, без бумажной волокиты и головной боли.</p>
					</div>
				<?php }?>
			</div>
		</section>
						<form id="object_filter">
							<input type="hidden" name="rooms"			id="rooms" 				value="" />
							<input type="hidden" name="price_b"			id="price_b" 			value="" />
							<input type="hidden" name="price_e"			id="price_e" 			value="" />
							<input type="hidden" name="general_space_b"	id="general_space_b" 	value="" />
							<input type="hidden" name="general_space_e"	id="general_space_e" 	value="" />
							<input type="hidden" name="floor_b"			id="floor_b" 			value="1" />
							<input type="hidden" name="floor_e"			id="floor_e" 			value="33" />
							<input type="hidden" name="catid"				id="catid" 				value="<?php echo $catid; ?>" />
							<input type="hidden" name="order_col"		id="order_col" 			value="price" />
							<input type="hidden" name="order_type"		id="order_type" 		value="asc" />
							<input type="hidden" name="dom"				id="dom" 				value="<?php echo $dom; ?>" />
						</form>
<div id="etazh_info" class="molod" ></div>