<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import joomla model library
jimport('joomla.application.component.model');

class FlatsModelCian extends JModelList{
	function getrecords() {
		$db = JFactory::getDBO();
		$type = JRequest::getVar('type', '');
		
		//заголовок
		ob_clean();
		header("Cache-Control: no-cache, must-revalidate");
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("content-type: text/xml");
		echo '<?xml version="1.0" encoding="windows-1251"?>';
		
		if($type=='arenda'){
			//объекты аренда
			$query = $db->getQuery(true);
			$query->select('a.*, m.cian as metro_c');
			$query->from('#__flats AS a');
			$query->join('LEFT', '#__wiki_type_metro as m on m.id = a.metro');
			$query->where('a.published = 1');
			$query->where('a.catid = 17');
			$query->where('a.cian = 1');
			$query->order('a.id DESC');
			$db->setQuery($query);
			$items_arenda = $db->loadObjectList();
			//аренда
			$this->xml_arenda($items_arenda);
		}elseif($type=='vtor'){
			//объекты вторичка
			$query = $db->getQuery(true);
			$query->select('a.*, m.cian as metro_c');
			$query->from('#__flats AS a');
			$query->join('LEFT', '#__wiki_type_metro as m on m.id = a.metro');
			$query->where('a.published = 1');
			$query->where('a.catid = 15');
			$query->where('a.cian = 1');
			$query->order('a.id DESC');
			$db->setQuery($query);
			$items_vtor = $db->loadObjectList();
			//вторичка
			$this->xml_vtor($items_vtor);
		}elseif($type=='comercial'){
			//объекты коммерческая
			$query = $db->getQuery(true);
			$query->select('a.*, m.cian as metro_c');
			$query->from('#__flats AS a');
			$query->join('LEFT', '#__wiki_type_metro as m on m.id = a.metro');
			$query->where('a.published = 1');
			$query->where('a.catid = 18');
			$query->where('a.cian = 1');
			$query->order('a.id DESC');
			$db->setQuery($query);
			$items_comercial = $db->loadObjectList();
			//коммерческая
			$this->xml_comercial($items_comercial);
		}elseif($type=='zagorod'){
			//объекты загородная
			$query = $db->getQuery(true);
			$query->select('a.*, m.cian as metro_c');
			$query->from('#__flats AS a');
			$query->join('LEFT', '#__wiki_type_metro as m on m.id = a.metro');
			$query->where('a.published = 1');
			$query->where('a.catid = 16');
			$query->where('a.cian = 1');
			$query->order('a.id DESC');
			$db->setQuery($query);
			$items_zagorod = $db->loadObjectList();
			//загородная
			$this->xml_zagorod($items_zagorod);
		}
	}
	function get_images($id){
		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__flat_images WHERE object_id = ".$id;
		$db->setQuery($query);
		$items = $db->loadObjectList();
		$html = '';
		foreach($items as $item){
			$html .= '<photo>http://7772977.ru'.$item->path.'</photo>';
		}
		return $html;
	}
	function yes_no($val){
		if($val=='1'){
			$ddd = 'yes';
		}elseif($val=='2'){
			$ddd = 'no';
		}else{
			$ddd = '';
		}
		return $ddd;
	}
	function yes_no_int($val){
		if($val=='1'){
			$ddd = '1';
		}elseif($val=='2'){
			$ddd = '0';
		}else{
			$ddd = '';
		}
		return $ddd;
	}
	function get_commerce_type($id){ 
		switch ($id) {
			case 11:
				$val='O'; // офис
				break;
			case 15:
				$val='W'; //склад
				break;
			case 18:
				$val='T'; // торговая площадь
				break;
			case 12:
				$val='FP'; //помещение свободного назначения
				break;				
			case 2:
				$val='G'; //гараж
				break;
			case 13:
				$val='WP'; //производственное помещение
				break;	
			case 10:
				$val='B'; //отдельно стоящее здание
				break;	
		}
		return $val;
	}
	function get_street_name($id){ //Тип улицы
		switch ($id) {
			case 1:
				$val='улица';
				break;
			case 2:
				$val='шоссе';
				break;
			case 3:
				$val='проспект';
				break;
			case 4:
				$val='проезд';
				break;
			case 5:
				$val='площадь';
				break;				
			case 6:
				$val='переулок';
				break;
			case 7:
				$val='бульвар';
				break;		
		}
		return $val;
	}
	function get_type_walls($i){ 
		switch ($i) {
			case 1:
				$val='1';
				break;
			case 2:
				$val='2';
				break;
			case 3:
				$val='4';
				break;
			case 4:
				$val='5';
				break;
			case 5:
				$val='6';
				break;				
			case 6:
				$val='7';
				break;
			case 7:
				$val='3';
				break;		
		}
		return $val;
	}
	function xml_arenda($items) {
		echo '<flats_rent>';
			foreach ($items as $item) {
				echo '<offer>';
				echo '	<id>'.$item->id.'</id>';
				echo '	<address admin_area ="'.$item->region.'" locality="'.iconv("utf-8", "windows-1251", $item->city).'" street="'.$this->get_street_name($item->street_type).' '.iconv("utf-8", "windows-1251", $item->street).'" flat_str="'.iconv("utf-8", "windows-1251", $item->flat).'" />';
				
				
				
				if(isset($item->time_to_metro_1) && !empty($item->time_to_metro_1)){
					$t_m = 'wtime="'.$item->time_to_metro_1.'"';
				}else{
					if(isset($item->time_to_metro_2) && !empty($item->time_to_metro_2)){
						$t_m = 'ttime="'.$item->time_to_metro_2.'"';
					}else{
						$t_m = '';
					}
				}
				echo '	<metro id="'.$item->metro_c.'" '.$t_m.'/>';
				if($item->object_type==7){
					echo '	<rooms_num>0</rooms_num>';
				}else{
					echo '	<rooms_num>'.$item->rooms.'</rooms_num>';
				}
				switch ($item->trade_period) {
					case 0:
						$for_day = '';
						break;
					case 1:
						$for_day = 'for_day="0"';
						break;
					case 2:
						$for_day = 'for_day="1"';
						break;
					case 3:
						$for_day = 'for_day="2"';
						break;
				}
								
				echo '	<price '.$for_day.' prepay="'.$item->predoplata.'" deposit="'.$this->yes_no_int($item->strah_deposit).'" currency="RUB">'.$item->price.'</price>';
				echo '	<floor total="'.$item->count_floor.'">'.$item->floor.'</floor>';
				
				echo '	<note><![CDATA[ '.iconv("utf-8", "windows-1251", $item->text).' ]]></note>';
				echo '	<area rooms="'.$item->rooms_space.'" living="'.$item->living_space.'" kitchen="'.$item->cook_space.'" total="'.$item->general_space.'"></area>';
				echo '	<options ';
				$phone = $this->yes_no($item->have_phone);
				if($phone!=''){
					echo '	phone="'.$phone.'"  ';
				}	
				$mebel_kitchen = $this->yes_no($item->have_k_furniture);
				if($mebel_kitchen!=''){
					echo '	mebel_kitchen="'.$mebel_kitchen.'"  ';
				}
				$mebel = $this->yes_no($item->have_furniture);
				if($mebel!=''){
					echo '	mebel="'.$mebel.'"  ';
				}	
				$balcon = $this->yes_no($item->loggia);
				if($balcon!=''){
					echo '	balcon="'.$balcon.'" ';
				}
				$wm = $this->yes_no($item->have_washing_machine);
				if($wm!=''){
					echo '	wm="'.$wm.'" ';
				}
				$tv = $this->yes_no($item->have_tv);
				if($tv!=''){
					echo '	tv="'.$tv.'" ';
				}	
				$rfgr = $this->yes_no($item->have_fridge);
				if($rfgr!=''){
					echo '	rfgr="'.$rfgr.'" ';
				}	
				$pets = $this->yes_no($item->possible_animals);
				if($pets!=''){
					echo '	pets="'.$pets.'" ';
				}
				$kids = $this->yes_no($item->possible_kids);
				if($kids!=''){
					echo '	kids="'.$kids.'"';
				}
				echo '></options>';
				echo '	<phone>'.iconv("utf-8", "windows-1251", $item->broker_phone).'</phone>';
				echo '	<com agent="'.$item->broker_commission.'" client="'.$item->owner_commission.'"></com>';
				echo '	<publish cian="yes" rentlist="yes"></publish>';
				$images = $this->get_images($item->id); echo $images;
				if($item->premium=='1'){
					echo '	<premium>1</premium>';
				}else{
					echo '	<premium>0</premium>';
				}
				
				echo '</offer>';
			}
		echo '</flats_rent>';
		exit();
	}
	function xml_vtor($items) {
		echo '<flats_for_sale>';
			foreach ($items as $item) {
				echo '<offer>';
				echo '	<id>'.$item->id.'</id>';
				echo '	<address admin_area="'.$item->region.'" locality="'.iconv("utf-8", "windows-1251", $item->city).'" street="'.$this->get_street_name($item->street_type).' '.iconv("utf-8", "windows-1251", $item->street).'" flat_str="'.iconv("utf-8", "windows-1251", $item->flat).'" />';
				
				if(isset($item->time_to_metro_1) && !empty($item->time_to_metro_1)){
					$t_m = 'wtime="'.$item->time_to_metro_1.'"';
				}else{
					if(isset($item->time_to_metro_2) && !empty($item->time_to_metro_2)){
						$t_m = 'ttime="'.$item->time_to_metro_2.'"';
					}else{
						$t_m = '';
					}
				}
				echo '	<metro id="'.$item->metro_c.'" '.$t_m.'/>';
				if($item->object_type==7){
					echo '	<rooms_num>0</rooms_num>';
				}else{
					echo '	<rooms_num>'.$item->rooms.'</rooms_num>';
				}
				
				echo '	<price currency="RUB">'.$item->price.'</price>';
				if(isset($item->type_walls) && !empty($item->type_walls)){
					$type = 'type="'.$this->get_type_walls($item->type_walls).'"';
				}else{
					$type = 'type="1"';
				}
				echo '	<floor total="'.$item->count_floor.'" '.$type.'>'.$item->floor.'</floor>';
				echo '	<note><![CDATA[ '.iconv("utf-8", "windows-1251", $item->text).' ]]></note>';
				echo '	<area rooms="'.$item->rooms_space.'" living="'.$item->living_space.'" kitchen="'.$item->cook_space.'" total="'.$item->general_space.'"></area>';
				
				if($item->trade_type=='1'){
					$sale_type = 'sale_type="A" ';
				}elseif($item->trade_type=='2'){
					$sale_type = 'sale_type="F" ';
				}else{
					$sale_type = '';
				}
				
				if($item->type_wc==1){ //раздельный
					$ws = 'su_s="0" su_r="1"';
				}elseif($item->type_wc==2){
					$ws = 'su_s="1" su_r="0"';
				}
				
				
				
				echo '	<options ';
				echo '	object_type="1" ';
				echo $sale_type;
				$phone = $this->yes_no($item->have_phone);
				if($phone!=''){
					echo '	phone="'.$phone.'"  ';
				}	
				$lift_p = $this->yes_no_int($item->lift);
				if($lift_p!=''){
					echo '	lift_p="'.$lift_p.'" ';
				}	
				echo '	lift_g = "0" ';
				echo '	balcon = "0" ';
				$lodgia = $this->yes_no_int($item->loggia);
				if($lodgia!=''){
					echo '	lodgia="'.$lodgia.'" ';
				}	
				echo $ws; 
				echo '	windows="'.$item->view_from_window.'" ';
				$ipoteka = $this->yes_no_int($item->hypothec);
				if($ipoteka!=''){
					echo '	ipoteka="'.$ipoteka.'" ';
				}	
				echo '	></options>';
				echo '	<phone>'.iconv("utf-8", "windows-1251", $item->broker_phone).'</phone>';
				$images = $this->get_images($item->id); echo $images;
				if($item->premium=='1'){
					echo '	<premium>1</premium>';
				}else{
					echo '	<premium>0</premium>';
				}
				echo '</offer>';
			}
		echo '</flats_for_sale>';
		exit();
	}
	function xml_comercial($items) {
		echo '<commerce>';
			foreach ($items as $item) {
				echo '<offer>';
				echo '	<id>'.$item->id.'</id>';
				echo '	<contract_type>'.$item->dogovor_type.'</contract_type>';
				echo '	<commerce_type>'.$this->get_commerce_type($item->object_type).'</commerce_type>';
				echo '	<address admin_area ="'.$item->region.'" locality="'.iconv("utf-8", "windows-1251", $item->city).'" street="'.$this->get_street_name($item->street_type).' '.iconv("utf-8", "windows-1251", $item->street).'" flat_str="'.iconv("utf-8", "windows-1251", $item->flat).'" />';
				
				if(isset($item->time_to_metro_1) && !empty($item->time_to_metro_1)){
					$t_m = 'wtime="'.$item->time_to_metro_1.'"';
				}else{
					if(isset($item->time_to_metro_2) && !empty($item->time_to_metro_2)){
						$t_m = 'ttime="'.$item->time_to_metro_2.'"';
					}else{
						$t_m = '';
					}
				}
				echo '	<metro id="'.$item->metro_c.'" '.$t_m.'/>';
				echo '	<price currency="RUB">'.$item->price.'</price>';
				echo '	<area rooms="'.$item->rooms_space.'" rooms_count="'.$item->rooms.'" total="'.$item->general_space.'"/>';
				echo '	<building floor="'.$item->floor.'" floor_total="'.$item->count_floor.'"/>';
				echo '	<options phones="1" />';
				echo '	<note><![CDATA[ '.iconv("utf-8", "windows-1251", $item->text).' ]]></note>';
				echo '	<phone>'.iconv("utf-8", "windows-1251", $item->broker_phone).'</phone>';
				echo '	<com ';
				if($item->broker_commission!=''){
					echo 'agent="'.$item->broker_commission.'" '; 
				}else{
					echo 'agent="0" '; 
				}	
				if($item->owner_commission!=''){
					echo 'client="'.$item->owner_commission.'" ';
				}else{
					echo 'client="0" ';
				}	
				echo '/>';
				$images = $this->get_images($item->id); echo $images;
				if($item->premium=='1'){
					echo '	<premium>1</premium>';
				}else{
					echo '	<premium>0</premium>';
				}
				echo '</offer>';
			}
		echo '</commerce>';
		exit();
	}
	function xml_zagorod($items) {
		echo '<suburbian>';
			foreach ($items as $item) {
				echo '<offer>';
				echo '	<id>'.$item->id.'</id>';
				
				if(($item->object_type=='5')or($item->object_type=='8')){
					$r_t = 'K';
				}elseif($item->object_type=='19'){
					$r_t = 'A';
				}elseif($item->object_type=='17'){
					$r_t = 'T';
				}else{
					$r_t = '';
				}
				if($r_t!=''){							
					echo '	<realty_type>'.$r_t.'</realty_type>';
				}
				
				if($item->bargain_type=='1'){
					echo '	<deal_type>S</deal_type>';
				}elseif($item->bargain_type=='2'){
					echo '	<deal_type>R</deal_type>';
				}				
				
				echo '	<address route="256" mcad="25" admin_area ="'.$item->region.'" locality="Пушкинский район, '.iconv("utf-8", "windows-1251", $item->city).'" street="'.$this->get_street_name($item->street_type).' '.iconv("utf-8", "windows-1251", $item->street).'" flat_str="'.iconv("utf-8", "windows-1251", $item->flat).'" />';
				switch ($item->trade_period) {
					case 0:
						$for_day = '';
						break;
					case 1:
						$for_day = 'for_day="0"';
						break;
					case 2:
						$for_day = 'for_day="1"';
						break;
					case 3:
						$for_day = 'for_day="2"';
						break;
				}
				echo '	<price currency="RUB" '.$for_day.'>'.$item->price.'</price>';
				echo '	<area living="'.$item->general_space.'" region="32"/>';
				echo '	<floor_total>'.$item->count_floor.'</floor_total>';
				echo '	<note><![CDATA[ '.iconv("utf-8", "windows-1251", $item->text).' ]]></note>';
				echo '	<phone>'.iconv("utf-8", "windows-1251", $item->broker_phone).'</phone>';
				echo '	<com ';
				if($item->broker_commission!=''){
					echo 'agent="'.$item->broker_commission.'" '; 
				}else{
					echo 'agent="0" '; 
				}	
				if($item->owner_commission!=''){
					echo 'client="'.$item->owner_commission.'" ';
				}else{
					echo 'client="0" ';
				}	
				echo '/>';
				$images = $this->get_images($item->id); echo $images;
				if($item->premium=='1'){
					echo '	<premium>1</premium>';
				}else{
					echo '	<premium>0</premium>';
				}
				echo '</offer>';
			}
		echo '</suburbian>';
		exit();
	}
}
