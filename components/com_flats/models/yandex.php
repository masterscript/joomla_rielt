<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');

class FlatsModelYandex extends JModelList{
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
	function get_images($id){
		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__flat_images WHERE object_id = ".$id;
		$db->setQuery($query);
		$items = $db->loadObjectList();
		$html = '';
		foreach($items as $item){
			$html .= '<image>http://7772977.ru'.$item->path.'</image>';
		}
		return $html;
	}
	function getyandex() {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('a.*, o.name as object_name, r.name as region_name, d.name as district_name, m.name as metro_name, h.name as highway_name, w.name as walls_name');
		$query->from('#__flats AS a');
		$query->join('LEFT', '#__wiki_type_object as o on o.id = a.object_type');
		$query->join('LEFT', '#__wiki_type_regions as r on r.id = a.region');
		$query->join('LEFT', '#__wiki_type_district as d on d.id = a.district');
		$query->join('LEFT', '#__wiki_type_metro as m on m.id = a.metro');
		$query->join('LEFT', '#__wiki_type_highway as h on h.id = a.highway');
		$query->join('LEFT', '#__wiki_type_walls as w on w.id = a.type_walls');
		$query->where('a.published = 1');
		$query->where('a.catid != 18');
		$query->order('a.id DESC');
		$db->setQuery($query);
		$items = $db->loadObjectList();
		
		//заголовок
		ob_clean();
		header("Cache-Control: no-cache, must-revalidate");
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("content-type: text/xml");
		echo '<?xml version="1.0" encoding="UTF-8"?>';
		
		echo '<realty-feed xmlns="http://webmaster.yandex.ru/schemas/feed/realty/2010-06">';
			echo '<generation-date>'.date("c").'</generation-date>';
			foreach($items as $item){
				$slug	= $item->alias ? ($item->id.':'.$item->alias) : $item->id;
				$link = JRoute::_(FlatsHelperRoute::getFlatRoute($slug, $item->catid));
				echo '<offer internal-id="'.$item->id.'">';
					if($item->bargain_type=='1'){ echo '<type>продажа</type>'; }else{ echo '<type>аренда</type>'; }
					echo '<property-type>жилая</property-type>';
					echo '<category>'.$item->object_name.'</category>';
					echo '<url>http://'.$_SERVER['HTTP_HOST'].$link.'</url>';
					echo '<creation-date>'.JHtml::_('date', $item->created, 'c').'</creation-date>';
					//location
					echo '<location>';
						echo '<country>'.$item->country.'</country>';
						if (isset($item->region_name) && !empty($item->region_name)){
							echo '<region>'.$item->region_name.'</region>';
						}
						if (isset($item->district_name) && !empty($item->district_name)){
							echo '<district>'.$item->district_name.'</district>';
						}
						echo '<locality-name>'.$item->city.'</locality-name>';
							//echo '<sub-locality-name>Центральный</sub-locality-name>';
							//echo '<non-admin-sub-locality>Центр</non-admin-sub-locality>';
						echo '<address>'.$this->get_street_name($item->street_type).' '.$item->street.', д.'.$item->flat.'</address>';
						if (isset($item->metro_name) && !empty($item->metro_name)){
							echo '<metro>';
								echo '<name>'.$item->metro_name.'</name>';
								if (isset($item->time_to_metro_1) && !empty($item->time_to_metro_1)){
									echo '<time-on-foot>'.$item->time_to_metro_1.'</time-on-foot>';
								}
								if (isset($item->time_to_metro_2) && !empty($item->time_to_metro_2)){
									echo '<time-on-transport>'.$item->time_to_metro_2.'</time-on-transport>';
								}
							echo '</metro>';
						}
						if (isset($item->highway_name) && !empty($item->highway_name)){
							echo '<direction>'.$item->highway_name.'</direction>';
						}
						if ($item->km_ot_mkad>0){
							echo '<distance>'.$item->km_ot_mkad.'</distance>';
						}	
					echo '</location>';
					echo '<sales-agent>';
						echo '<name>'.$item->broker_fio.'</name>';
						echo '<phone>'.$item->broker_phone.'</phone>';
						echo '<category>агентство</category>';
						echo '<organization>Агентство недвижимости Миэль</organization>';
						echo '<url>http://'.$_SERVER['HTTP_HOST'].'</url>';
						echo '<photo>http://'.$_SERVER['HTTP_HOST'].'/templates/weeb/images/m-l.png</photo>';
					echo '</sales-agent>';
					echo '<price>';
						echo '<value>'.$item->price.'</value>';
						echo '<currency>RUR</currency>';
						if($item->trade_period=='2'){
							echo '<period>день</period>';
						}elseif(($item->trade_period=='3')or($item->trade_period=='3')or($item->catid=='17')){
							echo '<period>месяц</period>';
						}
					echo '</price>';
					if ($item->hypothec>0){
						echo '<mortgage>1</mortgage>';
					}
					$images = $this->get_images($item->id); echo $images;
					echo '<description>'.htmlspecialchars($item->text).'</description>';
					if($item->status=='1'){
						echo '<quality>отличное</quality>';
					}elseif($item->status=='2'){
						echo '<quality>хорошее</quality>';
					}elseif($item->status=='3'){
						echo '<quality>удовлетворительное</quality>';
					}
					echo '<area>';
						echo '<value>'.$item->general_space.'</value>';
						echo '<unit>кв.м</unit>';
					echo '</area>';
					echo '<living-space>';
						echo '<value>'.$item->living_space.'</value>';
						echo '<unit>кв.м</unit>';
					echo '</living-space>';
					echo '<kitchen-space>';
						echo '<value>'.$item->cook_space.'</value>';
						echo '<unit>кв.м</unit>';
					echo '</kitchen-space>';
					if($item->garder_space>0){
						echo '<lot-area>';
							echo '<value>'.$item->garder_space.'</value>';
							echo '<unit>cот.</unit>';
						echo '</lot-area>';
					}
					if($item->designation=='1'){
						echo '<lot-type>садоводство</lot-type>';
					}elseif($item->designation=='2'){
						echo '<lot-type>ИЖС</lot-type>';
					}
					if($item->rooms>0){
						echo '<rooms>'.$item->rooms.'</rooms>';
					}	
					echo '<kitchen-furniture>'.$item->have_k_furniture.'</kitchen-furniture>';
					if($item->loggia){
						echo '<balcony>лоджия</balcony>';
					}
					if($item->type_wc=='1'){
						echo '<bathroom-unit>раздельный</bathroom-unit>';
					}elseif($item->type_wc=='2'){
						echo '<bathroom-unit>совмещенный</bathroom-unit>';
					}	
					echo '<floors-total>'.$item->count_floor.'</floors-total>';
					if (isset($item->have_phone) && !empty($item->have_phone)){
						echo '<phone>'.$item->have_phone.'</phone>';
					}
					if (isset($item->have_furniture) && !empty($item->have_furniture)){
						echo '<room-furniture>'.$item->have_furniture.'</room-furniture>';
					}
					if (isset($item->have_tv) && !empty($item->have_tv)){
						echo '<television>'.$item->have_tv.'</television>';
					}
					if (isset($item->have_washing_machine) && !empty($item->have_washing_machine)){
						echo '<washing-machine>'.$item->have_washing_machine.'</washing-machine>';
					}
					if (isset($item->have_fridge) && !empty($item->have_fridge)){
						echo '<refrigerator>'.$item->have_fridge.'</refrigerator>';
					}
					if (isset($item->flooring) && !empty($item->flooring)){
						echo '<floor-covering>'.$item->flooring.'</floor-covering>';
					}
					if ($item->view_from_window=='1'){
						echo '<window-view>Двор</window-view>';
					}elseif($item->view_from_window=='2'){
						echo '<window-view>Улица</window-view>';
					}elseif($item->view_from_window=='3'){
						echo '<window-view>Двор и улица</window-view>';
					}	
					if ($item->floor>0){
						echo '<floor>'.$item->floor.'</floor>';
					}
					
					if (isset($item->walls_name) && !empty($item->walls_name)){
						echo '<building-type>'.$item->walls_name.'</building-type>';
					}
					if (isset($item->lift) && !empty($item->lift)){
						echo '<lift>'.$item->lift.'</lift>';
					}
					if (isset($item->security) && !empty($item->security)){
						echo '<alarm>'.$item->security.'</alarm>';
					}
					if($item->location_wc=='1'){
						echo '<toilet>на улице</toilet>';
					}elseif($item->location_wc=='2'){
						echo '<toilet>в доме</toilet>';
					}
					if (isset($item->have_electric) && !empty($item->have_electric)){
						echo '<electricity-supply>'.$item->have_electric.'</electricity-supply>';
					}
					if (isset($item->have_sewerage) && !empty($item->have_sewerage)){
						echo '<sewerage-supply>'.$item->have_sewerage.'</sewerage-supply>';
					}
					if (isset($item->have_gas) && !empty($item->have_gas)){					
						echo '<gas-supply>'.$item->have_gas.'</gas-supply>';
					}
					
					
					
				echo '</offer>';
			}
		echo '</realty-feed>';
		
		
		
		
		
		
		
		
		
		
		exit();
		
	}
	

}
