<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');

class FlatsModelAvito extends JModelList{


	function get_images($id){
		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__flats_images WHERE object_id = ".$id." ORDER BY orders ASC ";
		$db->setQuery($query);
		$items = $db->loadObjectList();
		$html = '';
		foreach($items as $item){
			$html .= '<Image url="http://7772977.ru'.$item->path.'"/>';
		}
		return $html;
	}
	function getyandex() {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('a.*');
		$query->from('#__flats AS a');
		$query->where('a.published = 1');
		$query->where('a.avito = 1');
		$query->order('a.id DESC');
		$db->setQuery($query);
		$items = $db->loadObjectList();
		
		//заголовок
		ob_clean();
		header("Cache-Control: no-cache, must-revalidate");
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("content-type: text/xml");
		echo '<?xml version="1.0"?>';
		echo '<Ads formatVersion="3" target="Avito.ru">';
			foreach($items as $item){				
				echo '<Ad>';
					echo '<Id>'.$item->id.'</Id>';
					echo '<Category>Квартиры</Category>';
					echo '<OperationType>Продам</OperationType>'; 
					echo '<MarketType>Новостройка</MarketType>';					
					echo '<DateBegin>'.JHtml::_('date', $item->created, 'Y-m-d').'</DateBegin>';
					echo '<DateEnd>2017-08-14</DateEnd>';
					echo '<Region>Московская область</Region>';
					if($item->catid==31){ //молодежный
						echo '<City>Красногорск</City>';
						//echo '<Street>ул. Молодежная</Street>';
						//echo '<Subway>Тушинская</Subway>';
						//echo '<DirectionRoad>Волоколамское шоссе</DirectionRoad>';
						echo '<Floors>25</Floors>';
						echo '<ContactPhone>(499)110-36-02</ContactPhone>';
						echo '<ManagerName>Щербакова Елена Викторовна</ManagerName>';
						if($item->dom == 1){
							echo '<NewDevelopmentId>33552</NewDevelopmentId>';
						}elseif($item->dom == 2){
							echo '<NewDevelopmentId>33553</NewDevelopmentId>';
						}elseif($item->dom == 3){
							echo '<NewDevelopmentId>33291</NewDevelopmentId>';
						}elseif($item->dom == 4){
							echo '<NewDevelopmentId>25518</NewDevelopmentId>';
						}
						
						//echo '<NewDevelopmentId>25524</NewDevelopmentId>';
							
					}elseif($item->catid==32){ //квартал 7
						echo '<City>Химки</City>';
						//echo '<Street>мкр-н Сходня, Квартал 7, 2-й Мичуринский тупик корпус 4</Street>';
						//echo '<Subway>Сходненская</Subway>';
						//echo '<DirectionRoad>Ленинградское шоссе</DirectionRoad>';
						echo '<Floors>17</Floors>';
						echo '<ContactPhone>(499)110-36-04</ContactPhone>';
						echo '<ManagerName>Белицкая Анна Игоревна</ManagerName>';
						echo '<NewDevelopmentId>28910</NewDevelopmentId>';
					}elseif($item->catid==34){ //фортис
						echo '<City>Пушкино</City>';
						//echo '<Street>ул. 50 лет Комсомола, д. 28</Street>';
						//echo '<Subway>Бабушкинская</Subway>';
						//echo '<DirectionRoad>Ленинградское шоссе</DirectionRoad>';
						echo '<Floors>15</Floors>';
						echo '<ContactPhone>(499)110-30-64</ContactPhone>';
						echo '<ManagerName>Щербакова Елена Викторовна</ManagerName>';
						echo '<NewDevelopmentId>26074</NewDevelopmentId>';
					}
					
					echo '<Square>'.$item->s_obch.'</Square>';
					echo '<Rooms>'.$item->rooms.'</Rooms>';
					echo '<Floor>'.$item->etazh.'</Floor>';
										
					echo '<HouseType>Монолитный</HouseType>';
				
					$item->text = str_replace('&nbsp;', '', $item->text);
					
					echo '<Description>'.strip_tags($item->text).'</Description>';
					
					echo '<CompanyName>Агентство недвижимости Миэль</CompanyName>';
					
					if($item->price_2>0){
						echo '<Price>'.$item->price_2.'</Price>';
					}else{
						echo '<Price>'.$item->price.'</Price>';
					}
					
					$images = $this->get_images($item->id); 
					if($images!=''){
						echo '<Images>';
						echo $images; 
						echo '<Image url="http://7772977.ru/'.$item->planirovka.'"/>';
						echo '</Images>';
					}	
					echo '<AdStatus>'.$item->avito_type.'</AdStatus>';
					
					
					
				echo '</Ad>';
				
			}
		echo '</Ads>';
		exit();
		
	}
		function dop_get_images($id){
		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__flats_images WHERE object_id = ".$id." ORDER BY orders ASC ";
		$db->setQuery($query);
		$items = $db->loadObjectList();
		$html = '';
		foreach($items as $item){
			$html .= '<Image url="http://ovrazhnaja-4.ru'.$item->path.'"/>';
		}
		return $html;
	}
	/*function dop_flats(){
		
		$option = array(); 
		$option['driver']   = 'mysql';           
		$option['host']     = 'a121465.mysql.mchost.ru';     
		$option['user']     = 'a121465_ovraj';      
		$option['password'] = 'e12K1aq2q9';       
		$option['database'] = 'a121465_ovraj';         
		$option['prefix']   = 'realty_';              
		$db = JDatabase::getInstance($option);
		//$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('a.*');
		$query->from('#__flats AS a');
		$query->where('a.published = 1');
		$query->where('a.avito = 1');
		$query->order('a.id DESC');
		$db->setQuery($query);
		$items = $db->loadObjectList();
		
			foreach($items as $item){				
				echo '<Ad>';
					echo '<Id>'.$item->id.'</Id>';
					echo '<Category>Квартиры</Category>';
					echo '<OperationType>Продам</OperationType>'; 
					echo '<MarketType>Новостройка</MarketType>';					
					echo '<DateBegin>'.JHtml::_('date', $item->created, 'Y-m-d').'</DateBegin>';
					echo '<DateEnd>2014-06-01</DateEnd>';
					echo '<Region>Московская область</Region>';
					if($item->catid==32){ 
						echo '<City>Химки</City>';
						echo '<Street>ул. Овражная, д. 2a</Street>';
						echo '<Subway>Сходненская</Subway>';
						echo '<DirectionRoad>Ленинградское шоссе</DirectionRoad>';
						if($item->sekciya == 5 || $item->sekciya == 6 || $item->sekciya == 7){
							$floors = 8;
						}else{
							$floors = 9;
						}
						echo '<Floors>'.$floors.'</Floors>';
						echo '<ContactPhone>7 (985) 262-5026</ContactPhone>';
						echo '<ManagerName>Белицкая Анна Игоревна</ManagerName>';
						//echo '<NewDevelopmentId>28910</NewDevelopmentId>';
					}
					
					echo '<Square>'.$item->s_obch.'</Square>';
					echo '<Rooms>'.$item->rooms.'</Rooms>';
					echo '<Floor>'.$item->etazh.'</Floor>';
										
					echo '<HouseType>Монолитный</HouseType>';
				
					$item->text = str_replace('&nbsp;', '', $item->text);
					
					echo '<Description>'.strip_tags($item->text).'</Description>';
					
					echo '<CompanyName>Агентство недвижимости Миэль</CompanyName>';
					
					if($item->price_2>0){
						echo '<Price>'.$item->price_2.'</Price>';
					}else{
						echo '<Price>'.$item->price.'</Price>';
					}
					
					$images = $this->dop_get_images($item->id); 
					echo '<Images>';
					if($images!=''){						
						echo $images; 			
					}	
					echo '<Image url="http://ovrazhnaja-4.ru/'.$item->planirovka.'"/>';
					echo '</Images>';
					echo '<AdStatus>'.$item->avito_type.'</AdStatus>';
					
					
					
				echo '</Ad>';
				
			}
	}*/

}
