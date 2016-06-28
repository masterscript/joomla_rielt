<?php
defined('_JEXEC') or die;
class FlatsController extends JControllerLegacy{
public function getIp() {
	if (!empty($_SERVER['HTTP_CLIENT_IP'])){
		$ip=$_SERVER['HTTP_CLIENT_IP'];
	}elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
		$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
	}else{
		$ip=$_SERVER['REMOTE_ADDR'];
	}
	return $ip;
}
public function send_sub_form($b, $t){
		$mailer = JFactory::getMailer();
		$config = JFactory::getConfig();
		$sender = array($config->get('mailfrom'), $config->get('fromname')); //отправителя берем с глобальных настроек
		$mailer->setSender($sender); // добавляем данные об отправителе		

		$mailer->addRecipient('sania.svirov@gmail.com');
		$mailer->addRecipient('oliaer@gmail.com');
		$mailer->addRecipient('netotvitalik@gmail.com');
		$mailer->addRecipient('ulsam@mail.ru');
		$mailer->addRecipient('nikonenko.kirill@gmail.com');
		$mailer->addRecipient('e.zhigalov@miel.ru');

		$mailer->isHTML(true); //сообщение отправляем в формате html
		$mailer->setSubject($t); //добавляем тему сообщения
		$mailer->setBody($b);//добавляем тело сообщения								
		$send = $mailer->send();
		
		return $send;
	
}
	public function sub_form(){
		parse_str($_POST['form_data'], $form_data);	
		

		
		$title = $form_data['title']." (http://".$_SERVER['HTTP_HOST'].")";		
		
		$from = "ulsam@mail.ru";	
		
		$headers = "From: $from\r\nReply-to:$from\r\nContent-type:text/html;charset=utf-8\r\n";
		$ip = $this->getIp();
		$soobshenie = "IP-адрес отправителя - ".$ip."<br />";
		if(isset($form_data['name']) && !empty($form_data['name'])){
			$soobshenie .="Имя - ".$form_data['name']."<br />";
		}
		if(isset($form_data['url']) && !empty($form_data['url'])){
			$soobshenie .="Отправлено со страницы - http://".$_SERVER['HTTP_HOST'].$form_data['url']."<br />";
		}
		if(isset($form_data['phone']) && !empty($form_data['phone'])){
			$soobshenie .="Телефон - +7".$form_data['phone']."<br />";
		}
		
		if(isset($form_data['email']) && !empty($form_data['email'])){
			$soobshenie .="E-mail - ".$form_data['email']."<br />";
		}
		if(isset($form_data['text']) && !empty($form_data['text'])){
			$soobshenie .="Ваше сообщение - ".$form_data['text']."<br />";
		}
		if(isset($form_data['time_today']) && !empty($form_data['time_today'])){
			$time_today = " в ".$form_data['time_today'];
		}else{
			$time_today = "";
		}
		if(isset($form_data['time_to_call']) && !empty($form_data['time_to_call'])){
			$soobshenie .="Удобное время для звонка - ".$form_data['time_to_call'].$time_today."<br />";
		}
		
		
		if(isset($form_data['call_me_now']) && !empty($form_data['call_me_now'])){
			if($form_data['call_me_now'] == 'on'){
				$soobshenie .="Позвоните мне прямо сейчас - да";
			}else{
				$soobshenie .="Позвоните мне прямо сейчас - нет";
			}
		}
		
		
		//	 
		$to = "sania.svirov@gmail.com, oliaer@gmail.com";

		/*if(mail($to, $title, $soobshenie, $headers)){
			$html = "Спасибо, что обратились к нам! Наш менеджер свяжется с Вами.";
		}else{
			$html = "Ошибка";
		}*/
		$send = $this->send_sub_form($soobshenie, $title);
		if($send){
			$html = "Спасибо, что обратились к нам! Наш менеджер свяжется с Вами.";
		}else{
			$html = "Ошибка";
		}
		if(empty($html)){
			$html = "Ошибка";
		}

		echo json_encode(array('status'=>'ok','html'=>$html));
		exit;
		
	}
		public function pagination($all, $lim, $prev, $curr_link ,$curr_css, $link){
		$par = substr_count($link, "?");
		if($par>0){
			$ser = '&';
		}else{
			$ser = '?';
		}
		// осуществляем проверку, чтобы выводимые первая и последняя страницы
		// не вышли за границы нумерации
		$html = '';
		$first = $curr_link - $prev;
		if ($first < 1){
			$first = 1;
		}	
		
		$last = $curr_link + $prev;
		if ($last > ceil($all/$lim)){
			$last = ceil($all/$lim);
		}
		
		
		if($last != $curr_link){
				$l = $curr_link+1;
			}else{
				$l = 1;
		}
			$html .= "<li class='next-page nav-btn dtcell'><a href='".$link.$ser."page=".$l."' title='Дальше'>Дальше ></a></li>
					<li class='pagination dtcell'>
						<ul>";
			
		// начало вывода нумерации
		// выводим первую страницу
		$y = 1;
		if ($first > 1){
			$html .= "<li><a href='".$link.$ser."page=".$y."'>1</a></li>";
		}
		
		// Если текущая страница далеко от 1-й (>10), то часть предыдущих страниц
		// скрываем троеточием
		// Если текущая страница имеет номер до 10, то выводим все номера
		// перед заданным диапазоном без скрытия
		$y = $first - 1;
		if ($first > 3) {
			$html .= "<li class='tochki-zvetochki'><a href='".$link.$ser."page=".$y."'>...</a></li>";
		} else {
			for($i = 2;$i < $first;$i++){
				$html .= "<li><a href='".$link.$ser."page=".$i."'>".$i."</a></li>";
			}
		}
		
		// отображаем заданный диапазон: текущая страница +-$prev
		$p = 1;
		for($i = $first;$i < $last + 1;$i++){
						
			// если выводится текущая страница, то ей назначается особый стиль css
			if($i == $curr_link) {
				$html .= "<li><a href='#'><span>".$i."</span></a></li>";
			} else {
				if($i == 1){
					
					$html .= "<li><a href='".$link.$ser."page=".$i."'>".$i."</a></li>";
				}else{
					
					$html .= "<li><a href='".$link.$ser."page=".$i."'>".$i."</a></li>";
				}
				
				
			}
		}
		
		$y = $last + 1;
		// часть страниц скрываем троеточием
		if ($last < ceil($all / $lim) && ceil($all / $lim) - $last > 2){
			$html .= "<li class='tochki-zvetochki'><a href='".$link.$ser."page=".$y."'>...</a></li>";
		}	
		// выводим последнюю страницу
		$e = ceil($all / $lim);
		if ($last < ceil($all / $lim)){ 
			$html .= "<li><a href='".$link.$ser."page=".$e."'>$e</a></li>";
			//$html .= "<li><a href='".$link.$ser."page=".$e."'>&gt;</a></li>";
					
		}	
		if($curr_link-1 > 0 ){
			$f = $curr_link-1;
		}else{
			$f = ceil($all / $lim);;
		}	
			
			$html .= "</ul>
					</li>
					
					<li class='prew-page nav-btn dtcell'><a  href='".$link.$ser."page=".$f."' title='Назад'>< Назад</a></li>
				</ul>";
		
		
		return $html;
	}
	
	
	public function upload_file1() {
		jimport('joomla.filesystem.folder');	
		$dir = 'tmp/user_files';
		
		if(!JFolder::exists($dir)) JFolder::create($dir, 0777);
        
		$input = fopen("php://input", "r");
		$temp = tmpfile();
		$realSize = stream_copy_to_stream($input, $temp);
		fclose($input);
		if(!isset($_SERVER["CONTENT_LENGTH"]) || $realSize != (int)$_SERVER["CONTENT_LENGTH"]){
			return array('status'=>'err','msg'=>'The actual size of the file does not match the passed');
		}	
		if(is_dir($dir)) { 
			$_GET['qqfile'] = explode('\\',$_GET['qqfile']);
			$_GET['qqfile'] = array_pop($_GET['qqfile']);
			$dir = $dir.'/'.$_GET['qqfile']; 
		}else{
			return false;
		}
		$target = fopen($dir, "w");        
		fseek($temp, 0, SEEK_SET);
		stream_copy_to_stream($temp, $target);
		fclose($target);
		
		echo json_encode(array('status'=>'ok','file'=>$dir));
	}
	public function unzip_file(){
		if(isset($_POST['form_data'])){
			// разбираем строку запроса
			parse_str($_POST['form_data'], $form_data); 
			//загружаем библиотеку для распаковки файла
			if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
			require_once ( JPATH_BASE .DS. 'components' .DS. 'com_flats' .DS. 'pclzip.lib.php' );
			//создаем папку для распаковки
			jimport('joomla.filesystem.folder');
			$path = 'tmp/unzip';
			
			if(JFolder::exists(JPATH_SITE.'/'.$path)){
				JFolder::delete(JPATH_SITE.'/'.$path);
			}
			
			if(!JFolder::exists($path)) JFolder::create($path, 0777);
			//распаковываем архив
			$path_info = pathinfo($form_data['path']);
			if($path_info['extension']!='xlsx'){
				echo json_encode('Это не файл *.xlsx'); // вернем ответ
				unlink($form_data['path']);
				exit;
			}else{
				$archive = new PclZip($form_data['path']);
				if ($archive->extract($path) == 0) {
					echo json_encode($archive->errorInfo(true)); // вернем ответ
					unlink(JPATH_BASE.'/'.$form_data['path']);
					exit;
				}else{
					unlink(JPATH_BASE.'/'.$form_data['path']);
					echo json_encode('1'); // вернем ответ
					exit;
				}
			}
		}
	}
	public function get_flats(){
		jimport('mavik.thumb.generator');
		$thumbGenerator = new MavikThumbGenerator(array(
					'thumbDir' => 'cache/thumbnails/flats', // Директория для превьюшек
					'quality' => 100, // Качество jpg. От 1 до 100.
					'subDirs' => false,
				));
		$html = '';
		
		
		
		$max_dom = 1;	
		$db	= JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__categories');
		$query->where('published = 1');
		$query->where("extension = 'com_flats'");		
		$db->setQuery($query);
		$flatcat = $db->loadObjectList();
		$blocks = '';		
		$maps = array();
		$banners = array();
		$mkr = '';	
		//$html .='<a href="/'.$cat->alias.'"><img class="banner_one_cat" src="/images/banner_cat'.$cat->id.'.png"></a>';		
		foreach($flatcat as $cat){	
		if($cat->id == 10){
			$banners[$cat->id]['color'] = 'blue';
		}else{
			$banners[$cat->id]['color'] = 'purple';
		}
		$banners[$cat->id]['title'] = $cat->title;
		$banners[$cat->id]['alias'] = $cat->alias;

			$long = 0;
				$min_price = 9999999999;
				$rooms = array();
				$dom = array();
				$mprice = 0;
				$query2 = $db->getQuery(true);
				$query2->select('*');
				$query2->from('#__flats');
				$query2->where('published = 1');
				$query2->where("catid = ".$cat->id);		
				
				$db->setQuery($query2);
				$flats = $db->loadObjectList();			
				foreach($flats as $flat){
					if($min_price>$flat->price_2){
						$min_price = $flat->price_2;
					}
					if(!in_array($flat->rooms, $rooms)){
						$rooms[] = $flat->rooms;
					}
					
					if(isset($dom[$flat->dom])){
						if($dom[$flat->dom]['m_sqm_price'] > (int)$flat->sqm_price){
							$p = (int)$flat->sqm_price;
						}else{
							$p = $dom[$flat->dom]['m_sqm_price'];
						}
						
						$dom[$flat->dom] = array('m_sqm_price' => $p, 'num' => $dom[$flat->dom]['num']+1);
					}else{
						$dom[$flat->dom] = array('m_sqm_price' => (int)$flat->sqm_price, 'num' => 1);
					}					
				}
				$banners[$cat->id]['price'] = number_format($min_price, 0, ' ', ' ');
				
				$src = '/images/Prev.png';
				$n = 0;
			if($cat->id == 10){
				foreach($dom as $kay=>$d){
				
				
						
					if($max_dom%4 == 0){
						$color = 'purple';
					}elseif($max_dom%3 == 0){
						$color = 'yellow';
					}elseif($max_dom%2 == 0){
						$color = 'red';
					}elseif($max_dom%1 == 0){
						$color = 'blue';
					}
					if($n%2 == 0){
						$dop_class = "nov-box-margin";
					}else{
						$dop_class = "";
					}
					$price = number_format($d['m_sqm_price'], 0, " ", " ");
				$html .= '
					<div class="nov-box '.$color.' '.$dop_class.'">
						<div class="nov-title">
							<p>'.$cat->title.', корпус '.$kay.'</p><span>'.$cat->description.'</span>
						</div>
						<a href="'.$cat->alias.'?dom='.$kay.'"><img src="/images/'.$cat->id.'_dom'.$kay.'.jpg" alt=""></a>
						<div class="nov-footer">
							<p>Квартир: <b>'.$d['num'].'</b> Цена от: <b>'.$price.'</b> руб./м<sup>2</sup></p>
							<a href="'.$cat->alias.'?dom='.$kay.'">Подробнее</a>
						</div>
					</div>
				';
				
				$max_dom++;
				$n++;
				}
				$html .= '<div class="nov-box-call">
				<p>Не нашли подходящую квартиру?<br><b>Мы поможем вам подобрать</b></p>
				<a class="open-modal" onclick="javascript:open_modal();"  title="Бесплатная консультация">Бесплатная консультация</a>
				</div>';
			}
				
				$long = $long + count($flats);						
			
			if(!empty($cat->note)){
					$geo = explode(',', $cat->note);
					$geo1 = $geo[0];
					$geo2 = $geo[1];
				}else{
					$geo1 = '';
					$geo2 = '';
				}
			$images  = json_decode($cat->params);
			$img = $images->image;
				
				
			
			try { 
				$image = $thumbGenerator->getThumb(JURI::root().$img, 139, 139);
				$src = $image->thumbnail->url;
			}catch(Exception $exc){
				$src = JURI::root().$img;
			}	
			$src = str_replace('/cache', 'cache', $src);
			$maps[] = $cat->alias.'&&'.$cat->title.'&&'.$long.'&&'.$geo1.'&&'.$geo2.'&&'.number_format($min_price, 0, ' ', ' ').'&&'.implode(',', $rooms).'&&'.$cat->description.'&&'.$src.'&&'.$cat->id;
			if(empty($mkr)){
				$mkr .= '<option selected value="'.$cat->alias.'">'.$cat->title.'</option>';
			}else{
				$mkr .= '<option value="'.$cat->alias.'">'.$cat->title.'</option>';
			}
			
		}
		$banners = array_reverse($banners);
		$new_html = '';
		foreach($banners as $banner){
			$new_html .='
				<a href="/'.$banner['alias'].'"><div class="ban-'.$banner['color'].'">
					<div class="center-box">
						
							<div class="wrap">
								<h2>'.$banner['title'].'</h2>
								<p>от <b>'.$banner['price'].'</b> руб.</p>
							</div>
						
					</div>
				</div></a>';
		}

		$new_html .= $html;
			echo json_encode(array('status'=>'ok','html'=>$new_html, 'items'=>implode('//', $maps), 'mkr'=>$mkr));
			exit;
	}
	public function get_html_order($type, $col, $col_now){ 
		if(($type=='ASC')and($col_now==$col)){
			$cl_asc = 'hid';
			$cl_desc = '';
		}elseif(($type=='DESC')and($col_now==$col)){
			$cl_asc = '';
			$cl_desc = 'hid';	
		}else{
			$cl_asc = '';
			$cl_desc = '';
		}
		$html = '
			<div class="order_types">
				<a href="#ch_direction" data-type="ASC" data-col="'.$col.'" class="order_asc '.$cl_asc.'"></a>
				<a href="#ch_direction" data-type="DESC" data-col="'.$col.'" class="order_desc '.$cl_desc.'"></a>
			</div>';
		return $html;	
	}
	
	
	
	
	public function get_flats_cat(){
		jimport('mavik.thumb.generator');
		$thumbGenerator = new MavikThumbGenerator(array(
					'thumbDir' => 'cache/thumbnails/flats', // Директория для превьюшек
					'quality' => 100, // Качество jpg. От 1 до 100.
					'subDirs' => false,
				));
		$db	= JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__categories');
		$query->where('published = 1');
		$query->where("extension = 'com_flats'");		
		$db->setQuery($query);
		$flatcat = $db->loadObjectList();
		$html = '';
		$info = array();
	
				
		foreach($flatcat as $cat){				
			$ise = $db->getQuery(true);
			$ise->select('sqm_price');
			$ise->from('#__flats');
			$ise->where('catid = '.$cat->id);								
			$ise->where("published = 1 ORDER BY 'sqm_price' DESC LIMIT 1");							
			$db->setQuery($ise);		
			$resise = $db->loadResult();
			
			$images  = json_decode($cat->params);
			$img = $images->image;
			try { 
				$image = $thumbGenerator->getThumb(JURI::root().$img, 446, 220);
				$src = $image->thumbnail->url;
			}catch(Exception $exc){
				$src = JURI::root().$img;
			}
			if($cat->id == 10){
				$color = 'red';
			}else{
				$color = 'blue';
			}
			$price = (int)$resise;
			$price = number_format($price, 0, ' ', ' ');
			$html .= '
			<div class="nov-one '.$color.'">
							<div class="title">'.$cat->title.'</div>
							<a href="/'.$cat->path.'"><img src="'.$src.'" alt=""></a>
							<div class="down">
								<div class="text">
									<p>'.$cat->description.'</p>
									<p>от <b>'.$price.'</b> руб./м²</p>
								</div>
								<a href="/'.$cat->path.'" title="Подробнее">Подробнее</a>
							</div>
			</div>';
			
			
		}
		echo json_encode(array('status'=>'ok', 'html'=>$html));
		exit;
	}

	public function get_flats_dom(){
		if(isset($_POST['form_data'])){
			parse_str($_POST['form_data'], $form_data);
			
			$rooms 				= trim($form_data['rooms']);
			$rooms				= str_replace(" ", ",", $rooms);
			$price_b 			= $form_data['price_b'];
			$price_e 			= $form_data['price_e'];
			$general_space_b 	= $form_data['general_space_b'];
			$general_space_e 	= $form_data['general_space_e'];
			$floor_b 			= $form_data['floor_b'];
			$floor_e 			= $form_data['floor_e'];
			$catid		 			= $form_data['catid'];
			$dom	 			= $form_data['dom'];
			$order_col	 			= $form_data['order_col'];
			$order_type	 			= $form_data['order_type'];
			$db	= JFactory::getDbo();
			$query	= $db->getQuery(true);
			$query->select('*');
			$query->from('#__flats');
			$query->where('catid = '.$catid);
			if (isset($rooms) && !empty($rooms)){ 		$query->where('rooms in ('.$rooms.') '); }
			
			if (isset($dom) && !empty($dom)){ 			$query->where('dom = '.$dom); }		
			
			if (isset($price_b) && !empty($price_b)){ 	$query->where('price_2 >= '.$price_b); }
			if (isset($price_e) && !empty($price_e)){ 	$query->where('price_2 <= '.$price_e); }
			if (isset($general_space_b) && !empty($general_space_b)){ 		$query->where('s_obch >= '.$general_space_b); }
			if (isset($general_space_e) && !empty($general_space_e)){ 		$query->where('s_obch <= '.$general_space_e); }
			if (isset($floor_b) && !empty($floor_b)){ 						$query->where('etazh >= '.$floor_b); }
			if (isset($floor_e) && !empty($floor_e)){ 						$query->where('etazh <= '.$floor_e); }	
			$query->order($order_col.' '.$order_type);
			$db->setQuery($query);
			$res = $db->loadObjectList();
			$html = '
			<table class="table-result-head">
							<tr>
								<th>Корпус'.$this->get_html_order($order_type, "dom", $order_col).'</th>
								<th>Комнат'.$this->get_html_order($order_type, "rooms", $order_col).'</th>
								<th>Этаж'.$this->get_html_order($order_type, "etazh", $order_col).'</th>
								<th>№ кв.'.$this->get_html_order($order_type, "num", $order_col).'</th>
								<th>Площадь<br>(м²)'.$this->get_html_order($order_type, "sqm_price", $order_col).'</th>
								<th>Стоимость<br>(Руб.)'.$this->get_html_order($order_type, "price_2", $order_col).'</th>
							</tr>
			</table>	
			<div class="some-content-related">
				<div id="inner-content">
					<table>';
					foreach($res as $item){
						$price = number_format($item->price_2, 0, " ", " ");
						$html .='
							<tr>
										<td class="w_td1">
											<a href="'.JRoute::_(FlatsHelperRoute::getArticleRoute($item->id, $catid)).'"><span>'.$item->dom.'</span></a>
										</td>
										<td class="w_td2">
											<a href="'.JRoute::_(FlatsHelperRoute::getArticleRoute($item->id, $catid)).'"><span>'.$item->rooms.'</span></a>
										</td>
										<td class="w_td3">
											<a href="'.JRoute::_(FlatsHelperRoute::getArticleRoute($item->id, $catid)).'"><span>'.$item->etazh.'</span></a>
										</td>
										<td class="w_td4">
											<a href="'.JRoute::_(FlatsHelperRoute::getArticleRoute($item->id, $catid)).'"><span>'.$item->num.'</span></a>
										</td>
										<td class="w_td5">
											<a href="'.JRoute::_(FlatsHelperRoute::getArticleRoute($item->id, $catid)).'"><span>'.$item->s_obch.'</span></a>
										</td>
										<td class="w_td6">
											<a href="'.JRoute::_(FlatsHelperRoute::getArticleRoute($item->id, $catid)).'"><span>'.$price.'</span></a>
										</td>
							</tr>';
					}
					$html .='</table>
				</div>
			</div>';
			
			/* ********* */
			echo json_encode(array('status'=>'ok','html'=>$html));
			exit;
		}
	}
	public function coordinates($catid, $dom, $sekciya, $etazh, $s_etazh){
		if($catid == 10){
			if($dom >= 1 && $dom <= 2){
				if($s_etazh == 2){
					$coordinates = array(
						1 => '225,363,226,484,222,484,222,501,195,497,166,486,140,470,154,470,155,405,177,404,178,364',
						2 => '193,362,177,363,176,404,155,405,154,468,86,468,85,408,68,369,84,367,86,362,137,363,138,340,193,340',
						3 => '193,338,137,339,136,362,79,363,78,367,68,368,61,336,64,323,77,316,77,286,193,286',
						4 => '193,285,76,283,76,252,68,250,60,240,67,200,84,161,84,170,144,172,143,187,159,190,158,204,192,207',
						5 => '194,206,158,206,159,190,144,189,144,173,86,172,85,100,133,99,193,74,216,69,224,73,229,83,233,83,234,211,194,211',
						6 => '308,211,264,212,263,84,269,83,272,73,280,68,292,69,364,96,415,97,414,170,358,171,357,187,343,187,342,205,308,206',
						7 => '422,283,307,283,307,206,341,204,342,190,356,189,356,172,414,169,415,161,420,166,434,207,439,229,438,241,431,249,423,251',
						8 => '363,361,363,338,307,336,307,283,423,284,422,315,432,318,437,323,439,332,437,344,432,366,412,366,412,362',
						9 => '416,470,345,468,345,405,323,403,324,364,324,362,306,361,307,337,363,338,364,362,423,361,422,365,431,365,417,405',
						10 => '275,482,275,364,323,362,324,403,345,405,346,466,345,469,362,471,341,482,320,491,296,497,279,500,279,482');
				}elseif($s_etazh == 22){
					$coordinates = array(
						1 => '225,503,210,500,194,497,179,492,164,486,151,479,143,472,157,470,159,408,180,407,181,366,229,366,230,485,225,485',
						2 => '156,469,87,472,86,411,81,398,74,382,71,370,80,369,80,367,139,366,141,342,197,342,197,364,181,366,181,406,158,407',
						3 => '197,340,139,339,140,364,84,364,83,369,71,369,67,352,63,331,60,311,60,288,197,287',
						4 => '197,287,59,287,62,264,64,246,68,223,73,202,78,186,84,173,146,175,146,190,162,191,162,207,195,207',
						5 => '148,174,86,174,86,99,137,98,153,90,171,83,189,77,209,72,219,71,228,76,232,84,238,85,237,213,196,213,196,208,162,208,161,193,147,191',
						6 => '310,212,267,213,267,84,273,83,274,76,278,72,285,70,292,71,301,72,336,84,364,97,420,99,421,172,361,173,360,189,345,190,344,205,310,206',
						7 => '446,285,311,285,311,207,345,207,344,191,359,190,360,174,419,173,419,167,426,181,434,201,439,224,446,259',
						8 => '435,368,416,368,415,363,367,363,366,339,311,339,311,286,446,286,446,307,443,329,440,349',
						9 => '420,472,350,472,348,406,326,405,327,365,309,363,310,340,367,339,366,364,415,364,415,367,434,368,431,381,421,406',
						10 => '282,502,282,485,278,483,278,364,327,365,327,405,349,406,350,467,365,468,365,473,349,482,334,490,312,497');
				}elseif($s_etazh == 23){
					$coordinates = array(
						1 => '222,497,198,493,174,485,156,477,140,465,155,465,156,401,177,400,178,360,227,360,227,479,223,479',
						2 => '155,463,86,464,84,405,76,385,69,364,87,364,87,360,137,361,138,335,194,336,194,357,179,360,178,400,156,402',
						3 => '137,359,84,359,82,362,69,363,64,346,61,323,57,295,58,282,195,281,194,334,138,334',
						4 => '194,280,57,280,59,254,63,225,70,194,84,161,83,168,144,169,144,185,159,186,159,201,193,203',
						5 => '235,208,194,208,194,202,160,202,160,186,145,186,144,169,84,168,84,94,136,92,153,83,176,74,196,69,214,67,233,64',
						6 => '265,207,266,64,285,65,306,69,329,77,346,85,362,93,417,93,419,166,359,168,358,184,343,185,342,200,310,203,309,207',
						7 => '446,281,309,281,309,202,343,202,342,186,356,184,357,167,416,167,419,158,428,179,436,206,443,243',
						8 => '434,362,414,361,414,358,365,358,364,333,309,334,308,280,445,281,444,307,441,335',
						9 => '418,467,348,464,346,400,325,400,325,360,309,358,308,335,364,335,365,359,424,357,424,360,433,361,427,383,417,403',
						10 => '280,497,280,476,276,477,276,358,325,360,324,399,346,400,347,463,365,463,351,474,324,487');
				}elseif($s_etazh == 24){
					$coordinates = array(
						1 => '223,501,202,498,182,492,164,485,141,471,155,469,156,406,175,404,179,364,227,364,227,482,224,482',
						2 => '154,470,84,468,84,410,76,389,69,368,87,367,87,363,137,364,139,339,194,338,195,361,178,362,178,403,157,405',
						3 => '138,362,80,362,80,367,69,367,62,336,58,312,58,284,194,285,194,338,138,338',
						4 => '194,285,57,284,59,254,64,227,71,195,84,161,83,172,145,174,144,189,160,190,160,205,193,207',
						5 => '194,206,160,205,160,190,145,189,145,172,84,170,84,97,135,97,153,87,171,80,188,74,209,71,234,66,234,211,194,211',
						6 => '309,213,266,212,265,67,283,68,303,72,322,78,340,85,363,97,418,97,417,170,359,172,358,189,343,188,343,204,309,205',
						7 => '445,283,308,283,309,206,342,206,343,189,357,188,358,172,416,170,417,164,426,179,435,207,443,244',
						8 => '433,365,416,365,416,362,364,362,364,338,309,337,309,284,445,285,444,313,440,343',
						9 => '347,467,347,404,324,403,325,362,308,361,309,338,364,338,365,361,417,361,417,365,432,366,428,383,417,407,418,470',
						10 => '280,500,280,483,277,483,277,362,324,363,325,403,347,405,347,467,366,469,344,482,319,492');
				}
			}elseif($dom >= 5 && $dom <= 6){
				if($s_etazh == 2){
					$coordinates = array(
						1 => '230,484,229,494,223,498,214,501,179,492,137,473,153,471,154,417,175,417,177,364,235,365,235,483',
						2 => '153,469,83,468,83,410,67,368,87,367,87,363,136,363,137,338,195,339,193,362,178,363,177,416,154,417',
						3 => '136,363,82,362,81,367,67,367,60,343,60,327,64,319,76,315,76,284,194,284,193,337,136,337',
						4 => '194,284,76,282,76,253,66,249,61,242,62,227,67,202,82,202,81,205,167,206,167,231,193,232',
						5 => '193,231,166,230,167,205,82,205,68,202,82,161,83,97,137,97,158,85,182,76,201,72,222,67,221,84,225,84,224,211,193,213',
						6 => '330,230,305,231,305,213,274,213,274,85,277,85,277,68,299,71,328,81,361,96,415,98,417,162,432,202,416,202,416,206,332,207',
						7 => '422,283,306,285,305,231,331,232,331,207,417,208,417,204,431,203,438,227,440,236,438,245,433,251,424,253',
						8 => '362,339,306,339,306,286,422,285,423,317,432,318,440,325,439,341,436,355,434,366,419,367,417,363,363,363',
						9 => '414,470,344,470,345,418,323,417,322,364,305,363,306,340,362,339,362,364,417,363,417,367,433,368,417,411',
						10 => '264,484,265,364,322,363,322,416,345,419,345,468,364,468,364,473,330,488,296,500,281,500,272,495');
				}elseif($s_etazh == 22){
					$coordinates = array(
						1 => '241,478,211,473,183,463,163,452,176,451,177,409,194,408,196,367,240,368',
						2 => '177,452,121,452,121,403,108,370,123,370,123,367,163,367,162,347,208,348,208,366,195,366,195,407,176,410',
						3 => '208,347,163,347,162,366,121,367,109,370,104,353,101,329,100,305,208,304',
						4 => '208,304,100,305,100,282,104,259,109,240,120,240,121,243,188,244,187,262,207,263',
						5 => '208,262,188,262,187,244,110,240,113,226,121,209,122,156,164,156,183,145,207,138,233,134,232,246,209,248',
						6 => '296,247,272,247,272,133,296,138,319,145,339,156,382,157,383,206,397,240,384,240,384,243,317,243,317,261,297,263',
						7 => '405,305,295,305,296,262,316,261,316,242,385,243,396,241,401,260,405,284',
						8 => '397,369,380,366,341,368,341,347,297,346,297,305,405,305,403,341',
						9 => '382,450,327,449,327,409,310,409,309,368,296,367,297,347,341,348,342,366,384,366,383,369,397,369,383,404',
						10 => '264,477,264,366,310,368,310,408,327,409,328,449,343,449,335,456,318,465,291,474');
				}
			}elseif($dom == 7){
				if($sekciya == 1){
					$coordinates = array(
						1 => '320,445,290,458,250,468,206,466,170,459,137,446,111,431,94,416,79,397,139,339,156,355,196,318',
						2 => '78,397,62,372,46,330,38,289,37,255,43,223,57,190,95,229,99,225,174,298',
						3 => '59,191,96,230,101,226,175,298,250,223,142,108,114,124,83,153',
						4 => '250,222,141,107,166,97,200,89,243,87,352,85,350,201,272,202',
						5 => '306,238,307,202,351,201,351,86,401,86,400,253,451,254,452,352,414,351,397,377,328,306,328,239');
				}elseif($sekciya == 2){
					$coordinates = array(
						1 => '219,413,106,414,105,310,53,310,53,134,107,133,106,264,203,262,203,310,220,311',
						2 => '269,261,106,262,107,133,119,134,162,124,224,118,270,115',
						3 => '270,262,270,116,435,108,435,264',
						4 => '318,412,317,309,335,310,336,265,433,264,433,412');
				}elseif($sekciya == 3){
					$coordinates = array(
						1 => '198,424,79,425,81,254,179,256,180,301,199,302',
						2 => '249,254,81,255,80,107,201,106,250,106',
						3 => '249,254,249,106,299,105,353,104,418,107,418,254',
						4 => '304,424,302,304,320,303,319,254,419,255,418,425');
				}elseif($sekciya == 4){
					$coordinates = array(
						1 => '164,424,48,425,48,271,147,270,147,317,165,318',
						2 => '214,270,48,271,48,113,98,114,163,118,215,121',
						3 => '214,270,214,122,262,124,339,133,377,141,385,140,385,270',
						4 => '264,421,264,320,283,320,283,271,385,269,385,140,443,140,443,304,462,305,461,413,448,427,445,421');
				}elseif($sekciya == 5){
					$coordinates = array(
						1 => '97,358,97,259,79,259,79,110,263,109,293,115,328,127,330,129,225,232,207,218,176,249,173,246,149,247,149,308',
						2 => '224,234,328,129,362,149,390,178,411,210,427,253,428,300,428,310,423,335,413,361,401,384,391,396,291,301,293,298',
						3 => '150,436,291,302,391,397,383,409,364,426,334,445,309,455,282,462,257,465,218,463,180,452');
				}
			}elseif($dom == 8 || $dom == 9){
				if($sekciya == 1){
					$coordinates = array(
						1 => '340,450,313,463,268,472,238,472,212,469,181,460,156,451,123,430,104,413,94,399,155,340,174,360,213,323',
						2 => '94,398,84,385,70,358,61,333,56,306,54,253,63,216,74,193,189,303',
						3 => '74,193,101,152,159,108,195,150,266,225,188,302',
						4 => '267,225,161,110,204,93,252,89,426,89,426,250,404,251,404,368,345,310,345,242,321,241,314,246,281,211');
						
				}elseif($sekciya == 2){
					$coordinates = array(
						1 => '206,406,26,408,21,403,21,289,40,287,41,130,95,130,94,256,191,257,191,298,205,298',
						2 => '94,255,95,129,252,129,252,254',
						3 => '252,254,252,130,411,130,411,254',
						4 => '297,406,297,302,314,302,315,256,411,254,411,130,465,131,465,287,481,288,481,406');
				}elseif($sekciya == 3){
					$coordinates = array(
						1 => '88,360,89,257,87,358,88,257,69,256,69,95,249,95,290,101,335,116,299,155,224,229,205,213,171,246,168,243,142,242,142,306',
						2 => '223,228,333,117,376,145,407,179,430,223,441,284,439,312,432,338,422,367,410,390,400,405,293,303,296,299',
						3 => '142,447,294,302,400,405,391,418,371,435,339,455,313,466,283,474,256,476,217,475,180,467');
				}
			}elseif($dom == 3){
				$coordinates = array(
						1 => '222,324,221,465,205,462,188,459,173,454,160,449,145,442,135,436,149,435,151,367,172,366,175,324',
						2 => '191,299,190,300,190,299,190,323,172,323,171,365,150,366,150,431,77,432,77,371,62,329,79,330,81,325,132,325,133,299',
						3 => '71,243,189,243,189,299,132,300,131,324,77,324,76,328,62,328,54,292,56,284,61,280,69,277,71,277',
						4 => '190,190,190,244,71,243,71,210,67,210,58,206,55,199,55,187,59,174,62,160,78,160,78,164,138,165,138,191',
						5 => '191,136,189,190,138,190,138,164,63,159,68,141,77,117,78,95,127,95,127,113,158,113,159,135',
						6 => '199,28,193,29,206,26,215,25,223,29,225,39,229,39,230,168,190,170,190,137,159,135,157,114,124,113,125,95,78,95,78,54,133,54,149,44,171,35',
						7 => '303,171,261,170,262,37,267,37,269,31,275,26,281,25,289,25,297,28,308,31,328,37,359,53,415,54,412,95,371,96,369,113,335,113,335,133,304,135',
						8 => '411,95,411,95,412,115,416,116,432,160,416,159,415,163,356,165,354,191,303,191,303,134,334,135,335,114,369,115,369,96',
						9 => '303,191,354,191,355,164,421,164,421,160,432,160,439,193,439,198,435,204,431,208,423,211,422,243,303,243',
						10 => '304,245,421,245,423,277,430,278,436,282,439,288,439,299,432,326,414,327,413,324,361,324,361,299,304,299',
						11 => '303,298,361,300,361,324,422,323,423,328,430,328,414,370,413,433,343,433,342,367,321,367,320,324,303,323',
						12 => '272,324,321,324,321,366,343,367,343,431,360,434,351,440,328,451,304,459,288,463,272,464');
			}elseif($dom == 4){
				$coordinates = array(
						1 => '223,463,187,456,156,445,135,433,134,428,150,426,151,363,173,361,174,320,223,320',
						2 => '192,319,175,319,173,362,152,363,150,429,76,430,77,366,62,324,77,324,78,320,132,320,133,294,191,294',
						3 => '191,294,134,295,133,318,77,319,76,324,61,323,55,299,54,286,54,280,58,276,62,272,71,270,71,237,190,238',
						4 => '191,237,71,238,69,204,62,201,56,197,52,192,54,183,60,152,76,152,76,155,138,157,138,184,190,184',
						5 => '192,183,138,183,138,157,77,156,76,152,62,151,66,132,75,111,77,85,127,86,127,103,158,104,159,125,191,126',
						6 => '191,161,192,127,159,126,159,105,126,104,127,86,76,84,76,44,133,42,155,30,176,22,200,16,216,15,222,16,227,20,227,27,233,27,234,163',
						7 => '265,163,265,26,272,26,273,19,280,15,286,14,295,15,308,18,332,26,365,42,423,43,419,85,376,86,375,104,341,104,339,125,308,126,307,161',
						8 => '308,183,307,126,338,125,340,106,375,104,377,86,423,85,422,109,440,151,425,152,424,156,361,156,360,183',
						9 => '361,183,361,157,422,156,421,152,438,151,447,187,446,194,441,198,436,202,429,203,426,237,308,237,309,184',
						10 => '308,237,428,236,430,271,436,271,443,277,448,286,446,295,443,310,438,322,422,322,421,319,368,320,367,294,309,294',
						11 => '309,295,367,294,367,319,424,318,424,322,438,323,424,365,421,354,423,423,417,424,418,431,349,430,347,364,327,363,325,318,307,318',
						12 => '276,319,326,318,326,362,348,364,348,427,366,430,347,442,325,451,299,459,275,462');
				
			}
		}elseif($catid == 14){
			$coordinates = array(
						1 => '262,34,412,34,411,51,476,51,477,117,461,117,460,143,347,143,346,205,263,205',
						2 => '299,206,346,205,347,143,459,143,461,190,446,190,446,274,299,273',
						3 => '300,275,446,275,446,364,364,363,364,336,299,336',
						4 => '299,335,364,336,365,364,446,364,446,411,430,412,429,497,345,494,344,420,314,418,314,368,299,368',
						5 => '253,368,313,368,313,418,344,420,344,510,255,510',
						6 => '215,367,214,512,125,511,124,419,146,419,146,385,169,384,170,368',
						7 => '21,363,103,364,103,336,168,335,168,383,146,384,145,419,122,420,121,597,46,597,47,498,39,495,39,410,22,409',
						8 => '21,274,167,274,168,335,103,336,103,364,21,363',
						9 => '22,183,152,183,153,203,166,205,167,204,167,273,21,274',
						10 => '204,33,203,204,152,202,152,182,21,181,22,135,38,133,38,114,33,106,28,98,27,82,30,66,39,53,51,44,60,39,77,38,92,44,100,51,121,51,122,34');
		}
			$db	= JFactory::getDbo();
			$query1 = $db->getQuery(true);
			$query1->select('*');
			$query1->from('#__flats');
			$query1->where('published = 1');
			$query1->where('catid IN ('.$catid.')');
			$query1->where('etazh = '.$etazh);
			$query1->where('dom = '.$dom);
			$db->setQuery($query1);
			$flats = $db->loadObjectList();
			$maps = '';
			$kv_info = array();
			$et_num = '';
			foreach($flats as $flat){
				if($catid == 10){					
					if($dom == 1 || $dom == 2 || $dom == 5 || $dom == 6){
						$et_num = $flat->num - (($etazh-2)*10); //определяем квартиру на этаже
					}elseif($dom == 3){
						$et_num = $flat->num - (($etazh-2)*12); //определяем квартиру на этаже
						
					}elseif($dom == 4){
						$et_num = $flat->num - (($etazh-2)*12+280); //определяем квартиру на этаже
						
					}elseif($dom == 7){
						if($sekciya == 1){
							$et_num = $flat->num - (($etazh-2)*5); //определяем квартиру на этаже
						}elseif($sekciya == 2){
							$et_num = $flat->num - (($etazh-2)*4+120); //определяем квартиру на этаже
						}elseif($sekciya == 3){
							$et_num = $flat->num - (($etazh-2)*4+120+96); //определяем квартиру на этаже
						}elseif($sekciya == 4){
							$et_num = $flat->num - (($etazh-2)*4+120+96+96); //определяем квартиру на этаже
						}elseif($sekciya == 5){
							$et_num = $flat->num - (($etazh-2)*3+120+96+96+96); //определяем квартиру на этаже
						}						
					}elseif($dom == 8 || $dom == 9){
						if($sekciya == 1){
							$et_num = $flat->num - (($etazh-2)*4); //определяем квартиру на этаже
						}elseif($sekciya == 2){
							$et_num = $flat->num - (($etazh-2)*4+92); //определяем квартиру на этаже
						}elseif($sekciya == 3){
							$et_num = $flat->num - (($etazh-2)*3+92+92); //определяем квартиру на этаже
						}
					}
											
					
				}elseif($catid == 14){
					if($flat->num > 238){
						
					}else{
						if($flat->etazh > 2){
							$et_num = $flat->num - (($etazh-2)*10-2);
						}else{
							$et_num = $flat->num;
						}
					}
				}	
					$kv_info[$et_num] = array(
							'id' => $flat->id,
							'num' => $flat->num,
							'rooms' => $flat->rooms,
							's_obch' => $flat->s_obch,
							'price' => $flat->price_2,
							'bron' => $flat->status);				
			}
			foreach($coordinates as $kay => $coordinate){
				if(isset($kv_info[$kay])){
					if($kv_info[$kay]['bron'] == 2){
						$bron = 'bron';
					}else{
						$bron = '';
					}
					$maps .= '<area href="'.JRoute::_(FlatsHelperRoute::getArticleRoute($kv_info[$kay]['id'], $catid)).'" class="area-kv kv-ok '.$bron.'" onmouseover="kvHover(this);" onmouseout="kvLeave();" date-num="'.$kv_info[$kay]['num'].'" date-rooms="'.$kv_info[$kay]['rooms'].'" date-s-obch="'.$kv_info[$kay]['s_obch'].'" date-price="'.$kv_info[$kay]['price'].'" shape="poly" coords="'.$coordinates[$kay].'">';										
				}else{
					
					$maps .= '<area class="area-kv kv-no '.$et_num.'" onmouseover="kvHover(this);" onmouseout="kvLeave();" shape="poly" coords="'.$coordinates[$kay].'">';					
				}
			}
			return $maps;
	}
	public function etazh_info(){
		if(isset($_POST['form_data'])){
			parse_str($_POST['form_data'], $data);
			$catid = $data['catid'];
			$dom = $data['dom'];
			$sekciya = $data['sekciya'];
			$etazh = $data['etazh'];
			$svobodnux = $data['svobodnux'];
			$dom_shema = $dom;
			$s_etazh = 0;
			if($catid == 10){
				if($dom >= 1 && $dom <= 2){
					if($etazh >= 2 && $etazh <= 21){
						$s_etazh = 2;
					}elseif($etazh == 25 || $etazh == 24){
						$s_etazh = 23;
					}else{
						$s_etazh = $etazh;
					}
					$dom_shema = 1;
				}elseif($dom == 5 || $dom == 6){
					if($etazh >= 2 && $etazh <= 21){
						$s_etazh = 2;
					}elseif($etazh >= 22 && $etazh <= 25){
						$s_etazh = 22;
					}else{
						$s_etazh = $etazh;
					}
					$dom_shema = 5;
				}elseif($dom == 9){
					$dom_shema = 8;
				}elseif($dom == 3 || $dom == 4){
					$s_etazh = 2;
				}
			}
			
			if($sekciya > 0){
				$img_name = "plan".$catid."-".$dom_shema."-".$sekciya."s.jpg";
			}else{
				$img_name = "plan".$catid."-".$dom_shema."-".$s_etazh.".jpg";
			}
			$maps = $this->coordinates($catid, $dom, $sekciya, $etazh, $s_etazh);
							
			$etazh_prev = $etazh-1;
			$etazh_next = $etazh+1;
			if($catid == 10 && $dom == 8 || $dom == 9){
				if($etazh_prev < 2){
					$etazh_prev = 24;
				}
				if($etazh_next > 24){
					$etazh_next = 2;
				}
			}else{
				if($etazh_prev < 2){
					$etazh_prev = 25;
				}
				if($etazh_next > 25){
					$etazh_next = 2;
				}
			}
			
			$db	= JFactory::getDbo();
			$prev = $db->getQuery(true);
			$prev->select('count(*)');
			$prev->from('#__flats');
			$prev->where('published = 1');
			$prev->where('catid = '.$catid);	
			$prev->where('dom = '.$dom);	
			$prev->where('etazh = '.$etazh_prev);
			$db->setQuery($prev);
			$counter_prev = $db->loadResult();
			
			$next = $db->getQuery(true);
			$next->select('count(*)');
			$next->from('#__flats');
			$next->where('published = 1');
			$next->where('catid = '.$catid);	
			$next->where('dom = '.$dom);	
			$next->where('etazh = '.$etazh_next);
			$db->setQuery($next);
			$counter_next = $db->loadResult();
			
			$modal = "<div><h2>Свободных квартир ".$svobodnux."</h2>
						<p class='info-color'><span class='info-red'>Продано</span><span class='info-green'>Свободно </span><span class='info-yellow'>Забронировано</span></p>

			<canvas id='etazhCanvas'></canvas>
			<canvas id='etazhCanvasred'></canvas>
			<div class='container-img'>
			<div class='vspluvashka'>
				<p>Цена: <span class='info-kv-price'></span> руб.</p>
				<p>Площадь: <span class='info-kv-s'></span> м<sup>2</sup></p>
				<p>Комнат: <span class='info-kv-rooms'></span></p>
			</div>
			<img usemap='#mapetazh' id='mymapetazh' src='/templates/weeb/images/flat".$catid."/".$img_name."'></div>
			<p><map name='mapetazh'>".$maps."</map></p>
			<p>
				<span class='prev_etazh' onclick='javascript:etazh_info(".$catid.", ".$dom.", ".$sekciya.", ".$etazh_prev.", ".$counter_prev.", \"Этаж ".$etazh_prev."\", 1);' >Предыдущий этаж</span>
				<span class='next_etazh' onclick='javascript:etazh_info(".$catid.", ".$dom.", ".$sekciya.", ".$etazh_next.", ".$counter_next.", \"Этаж ".$etazh_next."\", 1);'>Следующий этаж</span>
			</p>
			</div>";
			echo json_encode(array('status'=>'ok','html'=>$modal));
			exit;
		}
	}
	
	
	
	public function get_flats_category(){
		if(isset($_POST['form_data'])){
			parse_str($_POST['form_data'], $data);			
			$catid = $data['catid'];
			$order_col	 			= $data['order_col'];
			$order_type	 			= $data['order_type'];
			if(isset($data['rooms']) && !empty($data['rooms'])){
				$rooms = trim($data['rooms']);
				$rooms = explode(" ", $rooms);
			}
			if(isset($data['price_b']) && !empty($data['price_b'])){
				$price_b = $data['price_b'];
			}
			if(isset($data['price_e']) && !empty($data['price_e'])){
				$price_e = $data['price_e'];
			}
			if(isset($data['general_space_b']) && !empty($data['general_space_b'])){
				$general_space_b = $data['general_space_b'];
			}
			if(isset($data['general_space_e']) && !empty($data['general_space_e'])){
				$general_space_e = $data['general_space_e'];
			}
			if(isset($data['floor_b']) && !empty($data['floor_b'])){
				$floor_b = $data['floor_b'];
			}
			if(isset($data['floor_e']) && !empty($data['floor_e'])){
				$floor_e = $data['floor_e'];
			}
			if(isset($data['start_limit']) && !empty($data['start_limit'])){
				$start_limit = $data['start_limit'];
			}
			if(isset($data['limit'])){
				$limit = $data['limit'];
			}
			$db	= JFactory::getDbo();
			$query1 = $db->getQuery(true);
			$query1->select('*');
			$query1->from('#__flats');
			$query1->where('published = 1');
			$query1->where('catid IN ('.$catid.')');
			if(isset($rooms)){
				$query1->where('rooms IN ('.implode(',',$rooms).')');
			}	
			if(isset($price_b)){
				$query1->where('price_2 >= '.$price_b);
			}	
			if(isset($price_e)){
				$query1->where('price_2 <= '.$price_e);
			}	
			if(isset($general_space_b)){
				$query1->where('s_obch >= '.$general_space_b);
			}	
			if(isset($general_space_e)){
				$query1->where('s_obch <= '.$general_space_e);
			}
			if(isset($floor_b)){
				$query1->where('etazh >='.$floor_b);
			}	
			if(isset($floor_e)){
				$query1->where('etazh <='.$floor_e);
			}	
			$query1->order($order_col.' '.$order_type);
			
			$db->setQuery($query1, $start_limit, 20);
			$flats = $db->loadObjectList();
			$html = '<table>
						<tr>
							<th>Фото</th>
							<th>Корпус'.$this->get_html_order($order_type, "dom", $order_col).'</th>
							<th>Этаж'.$this->get_html_order($order_type, "etazh", $order_col).'</th>
							<th>№ квартиры'.$this->get_html_order($order_type, "num", $order_col).'</th>
							<th>Комнат'.$this->get_html_order($order_type, "rooms", $order_col).'</th>
							<th>Площадь'.$this->get_html_order($order_type, "s_obch", $order_col).'</th>
							<th>Цена, Руб.'.$this->get_html_order($order_type, "price_2", $order_col).'</th>
							<th>Статус'.$this->get_html_order($order_type, "status", $order_col).'</th>
						</tr>';
				$maps = array();
				jimport('mavik.thumb.generator');
				$thumbGenerator = new MavikThumbGenerator(array(
					'thumbDir' => 'cache/thumbnails/houses', // Директория для превьюшек
					'quality' => 100, // Качество jpg. От 1 до 100.
					'subDirs' => false,
				));
			foreach($flats as $flat){
				
				$img = $flat->planirovka;
				try { 
						$image = $thumbGenerator->getThumb(JURI::root().$img, 80, 80);
						$src = $image->thumbnail->url;
					}catch(Exception $exc){
						$src = JURI::root().$flat->planirovka;
					}
				
				$price = number_format($flat->price_2, 0, " ", " ");
				$status = $flat->status;
				if($status == 2){
					$status = 'бронь';
				}else{
					$status = 'свободна';
				}
				$html .= '
						<tr>
							<td class="td_img">
								<a href="'.JRoute::_(FlatsHelperRoute::getArticleRoute($flat->id, $catid)).'"><img src="'.$src.'" alt=""></a>
							</td>
							<td>
								<a href="'.JRoute::_(FlatsHelperRoute::getArticleRoute($flat->id, $catid)).'"><span>'.$flat->dom.'</span></a>
							</td>
							<td>
								<a href="'.JRoute::_(FlatsHelperRoute::getArticleRoute($flat->id, $catid)).'"><span>'.$flat->etazh.'</span></a>
							</td>
							<td>
								<a href="'.JRoute::_(FlatsHelperRoute::getArticleRoute($flat->id, $catid)).'"><span>'.$flat->num.'</span></a>
							</td>
							<td>
								<a href="'.JRoute::_(FlatsHelperRoute::getArticleRoute($flat->id, $catid)).'"><span>'.$flat->rooms.'</span></a>
							</td>
							<td>
								<a href="'.JRoute::_(FlatsHelperRoute::getArticleRoute($flat->id, $catid)).'"><span>'.$flat->s_obch.'</span></a>
							</td>
							<td>
								<a href="'.JRoute::_(FlatsHelperRoute::getArticleRoute($flat->id, $catid)).'"><span>'.$price.'</span></a>
							</td>
							<td>
								<a href="'.JRoute::_(FlatsHelperRoute::getArticleRoute($flat->id, $catid)).'"><span>'.$status.'</span></a>
							</td>
						</tr>
				';							
			}
			
			
			$html .= '</table>';
			


			
			/*pagination*/
			$pg = $db->getQuery(true);
			$pg->select('count(*)');
			$pg->from('#__flats');
			$pg->where('published = 1');
			$pg->where('catid IN ('.$catid.')');
			if(isset($rooms)){
				$pg->where('rooms IN ('.implode(',',$rooms).')');
			}	
			if(isset($price_b)){
				$pg->where('price_2 >= '.$price_b);
			}	
			if(isset($price_e)){
				$pg->where('price_2 <= '.$price_e);
			}	
			if(isset($general_space_b)){
				$pg->where('s_obch >= '.$general_space_b);
			}	
			if(isset($general_space_e)){
				$pg->where('s_obch <= '.$general_space_e);
			}
			if(isset($floor_b)){
				$pg->where('etazh >='.$floor_b);
			}	
			if(isset($floor_e)){
				$pg->where('etazh <='.$floor_e);
			}	
			$db->setQuery($pg);
			$counter = $db->loadResult();
			//$j = ceil($counter / $limit);
			if($counter>20){
				$paging = '<ul class="wrap dt">';
				$lim = 20; // количество постов, размещаемых на одной странице
				$prev = 2; // количество отображаемых ссылок до и после номера текущей страницы
				$paging .= $this->pagination($counter, $lim, $prev, $limit, '123', '');
				$paging .= '</ul>';
			}else{
				$paging = '';
			}
			
			/*end-pagination*/




					
			echo json_encode(array('status'=>'ok','html'=>$html,'paging'=>$paging, 'counter'=>$counter));
			
		exit;
		}
		echo json_encode(array('status'=>'no'));
		exit;
		
	}
	
	
	
public function text_from_all(){
	parse_str($_POST['form_data'], $form_data); 			
	$text = $form_data['text'];
	$my_id = $_GET['my_id'];
	$my_id = explode(',', $my_id);
	$db	= JFactory::getDbo();
	foreach($my_id as $value){
					$upt = $db->getQuery(true);
					$upt->update($db->quoteName('#__flats'))
					->set('text = "'.$text.'"')
					->where('id = "'.$value.'"');
					$db->setQuery($upt);
					$db->execute();
	}
	echo json_encode(array('status'=>'ok'));
}

		public function upload_file(){
		if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
		if (!extension_loaded('zlib')) {
			JError::raiseWarning('', JText::_('COM_INSTALLER_MSG_INSTALL_WARNINSTALLZLIB'));
			return false;
		}
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		$dir = 'tmp/user_files';
		
		if(!JFolder::exists($dir)) JFolder::create($dir, 0777);
        
		$input = fopen("php://input", "r");
		$temp = tmpfile();
		$realSize = stream_copy_to_stream($input, $temp);
		fclose($input);
		if(!isset($_SERVER["CONTENT_LENGTH"]) || $realSize != (int)$_SERVER["CONTENT_LENGTH"]){
			return array('status'=>'err','msg'=>'The actual size of the file does not match the passed');
		}	
		if(is_dir($dir)) { 
			$_GET['qqfile'] = explode('\\',$_GET['qqfile']);
			$_GET['qqfile'] = array_pop($_GET['qqfile']);
			$dir = $dir.'/'.$_GET['qqfile']; 
		}else{
			return false;
		}
		
		$target = fopen($dir, "w");        
		fseek($temp, 0, SEEK_SET);
		stream_copy_to_stream($temp, $target);
		fclose($target);
		return $dir;
	}
	
	public function upload_archive_images(){
	$my_id = $_GET['my_id'];
	$my_id = explode(',', $my_id);
		$dir = $this->upload_file();
		$ext = JFile::getExt($dir);
		if($ext=='zip'){
			$config		= JFactory::getConfig();
			$tmp_dest	= $path = 'tmp/user_files/'.uniqid('archive_');;
			if(!JFolder::exists($tmp_dest)) JFolder::create($tmp_dest, 0777);
			jimport('joomla.filesystem.archive');
			$tmp_dest = JPath::clean($tmp_dest);
			$result = JArchive::extract($dir, $tmp_dest);
			if ( $result === false ) {
				echo json_encode(array('status'=>'error'));
			}else{
				JFile::delete($dir);
				$images = scandir($tmp_dest);
				$html = 0;
				if(count($images)>0){
					
						foreach($images as $key_i=>$img){
										if (($img != ".") && ($img != "..")) {
											//
											//$dir = JPATH_ROOT.'/images/flats/';
											$new_file = rand(0, 99999999).'.'.JFile::getExt($tmp_dest.'/'.$img);
											
												
											$db	= JFactory::getDbo();
											foreach($my_id as $id_obj){
												$dirid =  JPATH_ROOT.'/images/flats/'.$id_obj;
												if (!is_dir($dirid)){
													mkdir($dirid, 0777);
												}
											if(!copy(JPATH_ROOT.'/'.$tmp_dest.'/'.$img, $dirid.'/'.$new_file)){ return false; }
											$columns = array( 
												'object_id', 
												'path', 
												'orders',
												'title',
												'description'
											);
											$values = array(
												$db->quote($id_obj), 
												$db->quote('/images/flats/'.$id_obj.'/'.$new_file),
												$db->quote('NULL'),
												$db->quote(''),
												$db->quote('')
											);
											$query = $db->getQuery(true);
											$query->insert($db->quoteName('#__flats_images'))
												->columns($db->quoteName($columns))
												->values(implode(',', $values));
											$db->setQuery($query);
											$db->execute();
											}
											
											
											JFile::delete(JPATH_ROOT.'/'.$tmp_dest.'/flats/'.$img); 
											
											//
											$html ++;
										}
										
									}
									
					JFolder::delete(JPATH_ROOT.'/'.$tmp_dest);
					
				}
				echo json_encode(array('status'=>'ok','html'=>'Добавлено изображений '.$html));
			
			}
		}
		
	}
	public function action_price(){
		if(isset($_POST['form_data'])){
			parse_str($_POST['form_data'], $data); 
			$catid = $data['catid'];
			$skidka = $data['skidka'];
			$type = $data['type'];
			$rooms = $data['rooms'];
			
			if($type=='plus'){
				$index = (100 + $skidka)/100;
			}elseif($type=='minus'){
				$index = (100 - $skidka)/100;
			}elseif($type=='del'){
				$index = 0;	
			}else{
				echo json_encode(array('status'=>'ok','html'=>'Выберите знак'));
				exit;
			}	
			
			$db	= JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from('#__flats');
			$query->where('catid = '.$catid);
			$query->where('published = 1');
			if((int)$rooms>0){
				$query->where('rooms = '.$rooms);
			}
			$db->setQuery($query);
			$items = $db->loadObjectList();
					
			foreach($items as $item){
				$object = new stdClass();
				$object->id = $item->id;
				
				$object->price_2 = $item->price*$index;
				$result = $db->updateObject('#__flats', $object, 'id');
			}
			
			echo json_encode(array('status'=>'ok','html'=>'Готово'));
			exit;
		}	
	}
	public function action_cian(){
		if(isset($_POST['form_data'])){
			parse_str($_POST['form_data'], $data); 
			$catid 	= $data['catid'];
			$type 	= $data['type'];
			$rooms 	= $data['rooms'];
			$value 	= $data['value'];
									
			$db	= JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from('#__flats');
			$query->where('catid = '.$catid);
			if((int)$rooms>0){
				$query->where('rooms = '.$rooms);
			}
			$db->setQuery($query);
			$items = $db->loadObjectList();
					
			foreach($items as $item){
				$object = new stdClass();
				$object->id = $item->id;
				if($type=='cian'){
					$object->cian = $value;
				}elseif($type=='premium'){
					$object->premium = $value;
				}
				
				$result = $db->updateObject('#__flats', $object, 'id');
			}
			
			echo json_encode(array('status'=>'ok','html'=>'Готово'));
			exit;
		}	
	}
		public function staffs_add(){
		if(isset($_POST['form_data'])){
			parse_str($_POST['form_data'], $data); 
			$dom 	= $data['dom'];
			$zk 	= $data['zk'];
			$staffid = $data['staff'];
			if(!empty($dom) && !empty($zk) && !empty($staffid)){

				$db		= JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->update($db->quoteName('#__flats'))
				->set('staff = '.$staffid)
				->where('dom = '.$dom.' AND catid = '.$zk);							
				$db->setQuery($query);
				$db->execute();
				
				echo json_encode(array('status'=>'ok','html'=>'Сотрудники заменены'));	
				
				
			}else{
				echo json_encode(array('status'=>'no','html'=>'ошибка'));
			}

		}else{
			echo json_encode(array('status'=>'no','html'=>'ошибка'));
		}	
		
		}
	public function load_shah(){
		if(isset($_POST['form_data'])){
			parse_str($_POST['form_data'], $data); 
			$dom 	= $data['dom'];
			$zk 	= $data['zk'];
			if($zk == 14){
				$dom = 1;
			}
			$upt = 0;
			$ins = 0;
			
			$db		= JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->update($db->quoteName('#__flats'))
			->set('published = 0')
			->where('dom = '.$dom.' AND catid = '.$zk);							
			$db->setQuery($query);
			$db->execute();


			
		//обработка распакованного файла
				$path = JPATH_BASE.'/tmp/unzip/';
				$file = file_get_contents($path.'xl/sharedStrings.xml');
				$xml=(array)simplexml_load_string($file);
				$sst=array();
				foreach ($xml['si'] as $item=>$val)$sst[]=(string)$val->t;
				
				$file0=file_get_contents($path.'xl/worksheets/sheet1.xml');
				$xml0=simplexml_load_string($file0);
				$len0 = count($xml0->sheetData->row);
				if($len0>0){
					$file = $file0;
					$xml = $xml0;
					$len = $len0;
				}else{
					$file1=file_get_contents($path.'xl/worksheets/sheet2.xml');
					$xml1=simplexml_load_string($file1);
					$len1 = count($xml1->sheetData->row);
					if($len1>0){
						$file = $file1;
						$xml = $xml1;
						$len = $len1;
					}else{
						$file=file_get_contents($path.'xl/worksheets/sheet3.xml');
						$xml=simplexml_load_string($file);
						$len = count($xml->sheetData->row);
					}					
				}
				
				for($i=0;$i<$len;$i++){
					if($i > 2){
					$img = '';
					$row = $xml->sheetData->row[$i];																									
						$currow=array();
						$td = 0;
						
						$object = new stdClass();
						$object->catid = $zk;
						$object->published = 1;
						$object->dom = $dom;
						$etaz = 0;
						$num = 0;
						$price = 0;
						$price_2 = 0;
						$s = 0;
						$name_pl = '';
						$num_pp = '';
						$et_num = '';
						foreach ($row->c as $c){								
								$value=(string)$c->v;								
								$attrs=$c->attributes();
								//получение значения ячейки
								if ($attrs['t']=='s'){
									$data = (string)$sst[$value];
									
								}else{
									$data = $value;
								}
								if($data==''){
									$data = '0'; //тестовое значение для проверки работы парсера
								}	
																
									if($zk == 10 && ($dom == 7 || $dom == 8 || $dom == 9)){
										if($td == 0){
											$st = trim($data);
											if($st == 'свободна'){
												$object->status = 1;	
											}else{
												$object->status = 2;
											}																				
										}elseif($td == 1){
											$object->num =  trim($data);
											$num = $object->num;												
										}elseif($td == 2){
											$object->sekciya =  trim($data);	
											$etaz = trim($data);										
										}elseif($td == 3){
											$object->etazh =  trim($data);	
											$etaz = trim($data);										
										}elseif($td == 4){
											$object->rooms = trim($data);											
										}elseif($td == 5){
											$object->s_obch = round(trim($data), 2);
											$s = round(trim($data), 2);										
										}elseif($td == 6){
											$object->s_dop = round(trim($data), 2);											
										}elseif($td == 7){
											$object->price = round(trim($data), 2);	
											$price = round(trim($data), 2);									
										}elseif($td == 8){
											$object->price_2 = round(trim($data), 2);
											$price_2 = round(trim($data), 2);											
										}elseif($td == 9){
											$object->avans = round(trim($data), 2);											
										}elseif($td == 10){
											$object->oformlenie = round(trim($data), 2);											
										}elseif($td == 11){
											$object->itog = round(trim($data), 2);											
										}
									}else{
										if($td == 0){
											$st = trim($data);
											if($st == 'свободна'){
												$object->status = 1;	
											}else{
												$object->status = 2;
											}																				
										}elseif($td == 1){
											$object->num =  trim($data);	
											$num = $object->num;											
										}elseif($td == 2){
											$object->etazh =  trim($data);	
											$etaz = trim($data);										
										}elseif($td == 3){
											$object->rooms = trim($data);											
										}elseif($td == 4){
											$object->s_obch = round(trim($data), 2);
											$s = round(trim($data), 2);										
										}elseif($td == 5){
											$object->s_dop = round(trim($data), 2);											
										}elseif($td == 6){
											$object->price = round(trim($data), 2);	
											$price = round(trim($data), 2);									
										}elseif($td == 7){
											$object->price_2 = round(trim($data), 2);
											$price_2 = round(trim($data), 2);	
										}elseif($td == 8){
											$object->avans = round(trim($data), 2);											
										}elseif($td == 9){
											$object->oformlenie = round(trim($data), 2);											
										}elseif($td == 10){
											$object->itog = round(trim($data), 2);											
										}
									}
									
								$td++;
						}	
						
							if(isset($object->price_2) && !empty($object->price_2)){
								$object->sqm_price = $price_2/$s;
							}else{
								$object->price_2 = $price;
								$object->sqm_price = $price/$s;
							}
							$name_pl = $etaz;
							if($zk == 10){
								if($dom == 1 || $dom == 2){
									if($etaz >= 2 && $etaz <= 21){
										$name_pl = '2-21';
									}elseif($etaz == 24 || $etaz == 25){
										$name_pl = '24-25';
									}
								}elseif($dom == 3 || $dom == 4){									
									$name_pl = '2-25';									
								}elseif($dom == 5 || $dom == 6){
									if($etaz >= 2 && $etaz <= 21){
										$name_pl = '2-21';
									}elseif($etaz >= 22 || $etaz <= 24){
										$name_pl = '22-24';
									}
								}elseif($dom == 7){
									$name_pl = $object->sekciya.'s_2-25';
								}elseif($dom == 8 || $dom == 9){
									$name_pl = $object->sekciya.'s_2-24';
								}
							}elseif($zk == 14){
								if($num <= 238){
									$object->sekciya = 1;
								}else{
									$object->sekciya = 2;
								}
								$name_pl = $object->sekciya.'s_2-25';
							}

							
							
							if($zk == 10){					
								if($dom == 1 || $dom == 2 || $dom == 5 || $dom == 6){
									$et_num = $object->num - (($etaz-2)*10); //определяем квартиру на этаже
								}elseif($dom == 3){
									$et_num = $object->num - (($etaz-2)*12); //определяем квартиру на этаже
									
								}elseif($dom == 4){
									$et_num = $object->num - (($etaz-2)*12+280); //определяем квартиру на этаже
									
								}elseif($dom == 7){
									if($object->sekciya == 1){
										$et_num = $object->num - (($etaz-2)*5); //определяем квартиру на этаже
									}elseif($object->sekciya == 2){
										$et_num = $object->num - (($etaz-2)*4+120); //определяем квартиру на этаже
									}elseif($object->sekciya == 3){
										$et_num = $object->num - (($etaz-2)*4+120+96); //определяем квартиру на этаже
									}elseif($object->sekciya == 4){
										$et_num = $object->num - (($etaz-2)*4+120+96+96); //определяем квартиру на этаже
									}elseif($object->sekciya == 5){
										$et_num = $object->num - (($etaz-2)*3+120+96+96+96); //определяем квартиру на этаже
									}						
								}elseif($dom == 8 || $dom == 9){
									if($object->sekciya == 1){
										$et_num = $object->num - (($etaz-2)*4); //определяем квартиру на этаже
									}elseif($object->sekciya == 2){
										$et_num = $object->num - (($etaz-2)*4+92); //определяем квартиру на этаже
									}elseif($object->sekciya == 3){
										$et_num = $object->num - (($etaz-2)*3+92+92); //определяем квартиру на этаже
									}
								}
														
								
							}elseif($zk == 14){
								if($object->num > 238){
									
								}else{
									if($etaz > 2){
										$et_num = $object->num - (($etaz-2)*10-2);  //определяем квартиру на этаже
									}else{
										$et_num = $object->num; //определяем квартиру на этаже
									}
								}
							}			
							
							
							$object->planirovka = '/templates/weeb/images/flat'.$zk.'/'.$dom.'/'.$name_pl.'_'.$et_num.'.jpg';
						
						
						
							$ise = $db->getQuery(true);
							$ise->select('id');
							$ise->from('#__flats');
							$ise->where('catid = '.$zk);								
							$ise->where('dom = '.$dom);							
							$ise->where('num = '.$object->num.' LIMIT 1');
							$db->setQuery($ise);
							$resise = $db->loadResult();
							if($resise > 0){
								$object->id = $resise;															
								$result = $db->updateObject('#__flats', $object, 'id');
								$upt++;
							}else{
								$result = $db->insertObject('#__flats', $object);
								$ins++;
							}																									
					}	
				}
				
			echo json_encode(array('status'=>'ok','html'=>'Готово! Обновлено '.$upt.' объектов, добавлено '.$ins.' объектов'));
				exit;		
					
				}						
			}

	/*public function load_shah(){
		if(isset($_POST['form_data'])){
			parse_str($_POST['form_data'], $data); 
			$types 	= $data['types'];
			$zk 	= $data['zk'];
			$dom 	= $data['dom'];
			$db		= JFactory::getDbo();
			//обработка распакованного файла
				$path = JPATH_BASE.'/tmp/unzip/';
				$file = file_get_contents($path.'xl/sharedStrings.xml');
				$xml=(array)simplexml_load_string($file);
				$sst=array();
				foreach ($xml['si'] as $item=>$val)$sst[]=(string)$val->t;
				
				$file0=file_get_contents($path.'xl/worksheets/sheet1.xml');
				$xml0=simplexml_load_string($file0);
				$len0 = count($xml0->sheetData->row);
				if($len0>0){
					$file = $file0;
					$xml = $xml0;
					$len = $len0;
				}else{
					$file1=file_get_contents($path.'xl/worksheets/sheet2.xml');
					$xml1=simplexml_load_string($file1);
					$len1 = count($xml1->sheetData->row);
					if($len1>0){
						$file = $file1;
						$xml = $xml1;
						$len = $len1;
					}else{
						$file=file_get_contents($path.'xl/worksheets/sheet3.xml');
						$xml=simplexml_load_string($file);
						$len = count($xml->sheetData->row);
					}					
				}				
				$data1=array();
				$stroka = 1;
				
				$etag_row = '';
				$index_etag = 1;
				$mass = array();
				if($types=='kv'){
					$sec_3 = 10;
					$sec_4 = 11;
					if($zk=='kvartal'){
						$sec_1 = 10;
						$sec_2 = 10;
					}elseif($zk=='fortis'){
						$sec_1 = 10;
						$sec_2 = 10;							
					}else{
						if($dom=='2'){
							$sec_1 = 11;
							$sec_2 = 9;
						}
						if($dom=='3'){
							$sec_1 = 11;
							$sec_2 = 9;
						}
						if($dom=='4'){
							$sec_1 = 11;
							$sec_2 = 10;
						}
					}
				}else{
					$sec_1 = 2;
					$sec_2 = 4;
					$sec_3 = 2;
					$sec_4 = 11;
				}
				
				for($i=0;$i<$len;$i++){
					$row = $xml->sheetData->row[$i];					
					if($etag_row==''){
						$etag_row = $xml->sheetData->row[3]->c[0]->v; //ячейка с указанием номера этажа
					}else{
						$res = ($i+1)%3;
						if($res==0){
							if($i<($len-1)){
								$etag_row = (string)$xml->sheetData->row[$i+1]->c[0]->v; //ячейка с указанием номера этажа							
							}	
							$index_etag = 1;
						}else{
							$index_etag++;
						}
					}
					
					$index_num_pp = 1;
					if($stroka>2){
						$currow=array();
						$stolbez = 1;
						$num_pp = 0;
						foreach ($row->c as $c){
							if($stolbez>1){
								$value=(string)$c->v;
								$attrs=$c->attributes();
								//получение значения ячейки
								if ($attrs['t']=='s'){
									$data = (string)$sst[$value];
								}else{
									$data = $value;
								}
								if($data==''){
									$data = '_|_'; //тестовое значение для проверки работы парсера
								}
								
								//первая секция
								if(($stolbez>=2)and($stolbez<=($sec_1*2 + 1))){
									if($stolbez<4){
										$num_pp = 1;
										if($stolbez==3){
											$index_num_pp = 2;
										}
									}else{
										if($stolbez%2==0){
											$num_pp++;
											$index_num_pp = 1;
										}else{
											$index_num_pp = 2;
										}
									}
									$sekcia = 1;
								}elseif($stolbez==($sec_1*2 + 2)){
									$num_pp = 1;
									$index_num_pp = 1;
								//вторая секция	
								}elseif(($stolbez>=($sec_1*2 + 3))and($stolbez<=($sec_2*2+$sec_1*2 + 2))){
									if($stolbez<($sec_1*2 + 4)){
										$num_pp = 1;
									}else{
										if($stolbez%2!=0){
											$num_pp++;
											$index_num_pp = 1;
										}else{
											$index_num_pp = 2;
										}
									}
									$sekcia = 2;
								}elseif($stolbez==($sec_2*2+$sec_1*2 + 3)){
									$num_pp = 1;	
									$index_num_pp = 1;
								//третья секция	
								}elseif(($stolbez>=($sec_2*2+$sec_1*2 + 4))and($stolbez<=($sec_2*2+$sec_1*2 + $sec_3*2 + 3))){
									if($stolbez<($sec_2*2+$sec_1*2 + 5)){
										$num_pp = 1;
									}else{
										if($stolbez%2==0){
											$num_pp++;
											$index_num_pp = 1;
										}else{
											$index_num_pp = 2;
										}
									}
									$sekcia = 3;
								}elseif($stolbez==($sec_2*2+$sec_1*2 + $sec_3*2 + 4)){
									$num_pp = 1;	
									$index_num_pp = 1;
								//четвертая секция	
								}elseif(($stolbez>=($sec_2*2+$sec_1*2 + $sec_3*2 + 5))and($stolbez<=($sec_2*2+$sec_1*2 + $sec_3*2 + $sec_4*2 + 4))){
									if($stolbez<($sec_2*2+$sec_1*2 + $sec_3*2 + 5)){
										$num_pp = 1;
									}else{
										if($stolbez%2!=0){
											$num_pp++;
											$index_num_pp = 1;
										}else{
											$index_num_pp = 2;
										}
									}
									//$num_pp = $num_pp - 1;
									$sekcia = 4;	
								}else{
									$sekcia = 0;
								}
								//формирование массива с данными
								if(($stolbez!=($sec_2*2+$sec_1*2 + 3))and($stolbez!=($sec_1*2 + 2))and($stolbez!=($sec_2*2+$sec_1*2 + $sec_3*2 + 4))){
									if($sekcia==4){
										$new_num_pp = $num_pp - 1;
									}else{
										$new_num_pp = $num_pp;
									}
									$mass[] = array('etag' => $etag_row, 'etag_i' => $index_etag, 'section' => $sekcia, 'num_pp' => $new_num_pp, 'num_pp_i' =>$index_num_pp, 'cod' => (string)$attrs['r'], 'data' => $data, 'stolbez' => $stolbez);
								}	
							}
							$stolbez++;
						}
					}
					$stroka++;
				}
				//удаляем данные по квартирам в доме, для которого загружаем шахматку
					$cat_price = "";
					if($types=='kv'){
						if($zk=='molod'){
							
							$query = $db->getQuery(true);
							$query->update($db->quoteName('#__flats'))
							->set('published = 0')
							->where('dom = '.$dom.' AND catid = 31');							
							$db->setQuery($query);
							$db->execute();
							
							$cat_price = "kv_m";
						}elseif($zk=='fortis'){
							$query = $db->getQuery(true);
							$query->update($db->quoteName('#__flats'))
							->set('published = 0')
							->where('catid = 34');
							$db->setQuery($query);
							$db->execute();
							$cat_price = "kv_m";
						}elseif($zk=='kvartal'){
							$query = $db->getQuery(true);
							$query->update($db->quoteName('#__flats'))
							->set('published = 0')
							->where('catid = 32');
							$db->setQuery($query);
							$db->execute();
							$cat_price = "kv_kvar";
						}
					}else{
						//$query = $db->getQuery(true);
						//$query->delete('#__flats');
						//$query->where('dom = 4');						
						//$query->where('catid = 31');
						//$db->setQuery($query);
						//$db->execute();
					}
					$html = '';
					//вывод таблицы для проверки загруженных данных
					$html .= "<h2 align='center'>Были добавлены следующие квартиры</h2>";
					$html .= "<table style='color: #565656; font-size:10px;font-family:Verdana;' border='1' align='center' cellpadding='5' cellspacing='0'>";
					$html .= "<tr>
							<th>№ п/п</th>
							<th>Номер<br/>в доме</th>
							<th>Номер<br/>на секции</th>";
					if($zk=='molod'){
						$html .= "<th>Дом</th>"; 
					}
					$html .= "<th>Секция</th>
							<th>Этаж</th>
							<th>Цена<br/>общая</th>
							<th>Цена за м<sup>2</sup></th>
							<th>К-ство<br/>комнат</th>
							<th>Площадь, м<sup>2</sup></th>
							
						</tr>";		
					$i = 1;	
					
					foreach($mass as $item){
						// за начальную точку берем все непустые ячейки, которые содержат номер квартиры
						if(($item['data']!='_|_')and($item['etag_i']=='1')and($item['num_pp_i']=='1')){
							$price2 = '0';
							foreach($mass as $item2){
								//получение значения ячейки с ценой за квадратный метр - string
								if(
									($item2['data']!='_|_')and($item2['etag_i']=='1')and($item2['num_pp_i']=='2')and
									($item2['num_pp']==$item['num_pp'])and($item2['section']==$item['section'])and
									($item2['etag']==$item['etag'])
								){
									if($item2['data']!='_|_'){
										$sqm_price = $item2['data'];//."-".$item2['cod']."-".$item['cod'];
									}else{
										$sqm_price = '0';
									}	
								}
								//получение значения ячейки с ценой общей - string
								if(
									($item2['data']!='_|_')and($item2['etag_i']=='2')and($item2['num_pp_i']=='1')and
									($item2['num_pp']==$item['num_pp'])and($item2['section']==$item['section'])and
									($item2['etag']==$item['etag'])
								){
									if($item2['data']!='_|_'){
										$price = $item2['data'];
									}else{
										$price = '0';
									}	
								}
								
								//получаем значение акционной цены
								
								if(
									($item2['data']!='_|_')and($item2['etag_i']=='2')and($item2['num_pp_i']=='2')and
									($item2['num_pp']==$item['num_pp'])and($item2['section']==$item['section'])and
									($item2['etag']==$item['etag'])
								){
									if($item2['data']!='_|_'){
										$price2 = $item2['data'];
									}else{
										$price2 = '0';
									}
								}
								
								//получение значения ячейки с количеством комнат - string
								if(
									($item2['data']!='_|_')and($item2['etag_i']=='3')and($item2['num_pp_i']=='1')and
									($item2['num_pp']==$item['num_pp'])and($item2['section']==$item['section'])and
									($item2['etag']==$item['etag'])
								){
									if($item2['data']!='_|_'){
										$rooms = $item2['data'];
									}else{
										$rooms = '0';
									}	
								}
								//получение значения ячейки с площадью квартиры - string
								if(
									($item2['data']!='_|_')and($item2['etag_i']=='3')and($item2['num_pp_i']=='2')and
									($item2['num_pp']==$item['num_pp'])and($item2['section']==$item['section'])and
									($item2['etag']==$item['etag'])
								){
									if($item2['data']!='_|_'){
										$s_obch = $item2['data'];
									}else{
										$s_obch = '0';
									}	
								}
							}
							// получение номера квартиры на секции - выбор значения, которое в скобках
							if (preg_match_all('/(\([^\)]+\))/i',$item['data'],$m)){
								$num_kv = $m[1][0];
							}
							// получение номера квартиры в доме - выбор значения, которое до скобок
							if (preg_match_all('/([^\)]+)\((.*)\)/',$item['data'],$m)){
								$num = $m[1][0];
							}
							// удаляем скобки из значения
							$num_kv = str_replace(array(")","("),'',$num_kv);
														
							$price1 = str_replace(",", ".", $price); // замены запятой на точку, чтобы можно было перевести из string в float
							$price1=preg_replace("/[^x\d|*\.]/","",$price1);
							$price1 = floatval(str_replace(" ", "", $price1)); // переводим из string в float
							
							$price2 = str_replace(",", ".", $price2); // замены запятой на точку, чтобы можно было перевести из string в float
							$price2 = preg_replace("/[^x\d|*\.]/","",$price2);
							$price2 = floatval(str_replace(" ", "", $price2)); // переводим из string в float
							
							$sqm_price1 = str_replace(",", ".", $sqm_price); // замены запятой на точку, чтобы можно было перевести из string в float
							$sqm_price1=preg_replace("/[^x\d|*\.]/","",$sqm_price1);
							$sqm_price1 = floatval(str_replace(" ", "", $sqm_price1));// переводим из string в float
							
							$s_obch1 = str_replace(",", ".", $s_obch); // замены запятой на точку, чтобы можно было перевести из string в float
							$s_obch1=preg_replace("/[^x\d|*\.]/","",$s_obch1);
							$s_obch1 = floatval(str_replace(" ", "", $s_obch1));// переводим из string в float
							
							$section = (int)$item['section']; // номер секции
							$etag = (int)$item['etag']; // номер этажа
							$html .= "<tr>
									<td align='center'>".$i."</td>
									<td>".$num."</td>
									<td>".$num_kv."</td>";
							if($zk=='molod'){		
								$html .= "<td align='center'>".$dom."</td>";
							}		
							$html .=	"<td align='center'>".$section."</td>
									<td align='center'>".$etag."</td>
									<td align='right'>".$price1." руб</td>
									<td align='right'>".$sqm_price1." руб</td>
									<td align='center'>".(int)$rooms."</td>
									<td align='right'>".$s_obch1."</td>
									
								</tr>";
							if($types=='kv'){	
								if($zk!='kvartal'){
									$bukva = 'M';
								}else{
									$bukva = 'K';
								}
							}else{
								$bukva = 'M';
							}	
							///добавление в базу
							$today = date("Y-m-d H:i:s");
							//$cat_price = "kv_m";
							if($price2=='0'){
								if(!empty($cat_price)){
									if($cat_price == "kv_m"){										
										if($dom == 4){
											$price_2 = $price1 * 0.9;
											
											
										}else{
											$price_2 = 0;
										}
									}elseif($cat_price == "kv_kvar"){
										$price_2 = $price1 * 0.9;
									}	
								}else{
									$price_2 = 0;
								}
							}else{
								$price_2 = $price2;
							}
							$object = new stdClass();
							$object->num 		= $num;
							$object->num_pp		= (int)$item['num_pp'];
							if($zk=='molod'){	
								$object->dom	= $dom;
							}
							if($zk=='fortis'){
								$object->sekciya = 1;
							}else{
								$object->sekciya	= $section;
							}
							$object->etazh		= $etag;
							$object->price		= $price1;
							$object->sqm_price	= $sqm_price1;
							$object->rooms		= (int)$rooms;
							$object->s_obch		= $s_obch1;
							$object->num_kv		= $num_kv;
							$object->published	= 1;
							$object->price_2	= $price_2;
						
							
							$object->created	= $today;
							if($zk=='molod'){	
								$object->catid	= 31;
							}elseif($zk=='kvartal'){
								$object->catid	= 32;
							}elseif($zk=='fortis'){
								$object->catid	= 34;
							}
							
								
							if($zk=='molod'){	
								if(($dom=='3')and($section=='1')){
									$item['num_pp'] = (int)$item['num_pp'];
									if($item['num_pp']=='5'){
										$object->planirovka	= "images/planirovki/".$bukva.$dom."_S".$section."_L".$etag."_A4a.png";
									}else{
										if($item['num_pp']>5){
											$n_p = $item['num_pp'] - 1;
										}else{
											$n_p = $item['num_pp'];
										}	
										$object->planirovka	= "images/planirovki/".$bukva.$dom."_S".$section."_L".$etag."_A".$n_p.".png";
									}	
								}else{
									$object->planirovka	= "images/planirovki/".$bukva.$dom."_S".$section."_L".$etag."_A".$item['num_pp'].".png";
								}	
							}elseif($zk=='kvartal'){
								if($etag>=14){
									$object->planirovka	= "images/planirovki/K4_S1_L14_A".$item['num_pp'].".png";
								}else{
									$object->planirovka	= "images/planirovki/K4_S1_L3_A".$item['num_pp'].".png";
								}	
							}elseif($zk=='fortis'){
								if($etag>=12){
									$object->planirovka	= "images/planirovki/K4_S1_L14_A".$item['num_pp'].".png";
								}else{
									$object->planirovka	= "images/planirovki/K4_S1_L3_A".$item['num_pp'].".png";
								}	
							}
							
							$db	= JFactory::getDbo();
							$ise = $db->getQuery(true);
							$ise->select('id');
							$ise->from('#__flats');
							$ise->where('catid = '.$object->catid);
							if($zk=='molod'){	
								$ise->where('dom = '.$object->dom);
							}
							$ise->where('sekciya = '.$object->sekciya);
							$ise->where('num = '.$object->num);
							$db->setQuery($ise);
							$resise = $db->loadResult();
							if($resise > 0){
								$object->id = $resise;															
								$result = $db->updateObject('#__flats', $object, 'id');
							}else{
								$result = $db->insertObject('#__flats', $object);
							}
															
							$i++;	
						}
						
					}
					$html .= "</table>";
					
			echo json_encode(array('status'=>'ok','html'=>'Готово', 'res'=>$html));
			exit;		
		}		
	}	*/
	public function del_img(){
		if(isset($_POST['form_data'])){
			parse_str($_POST['form_data'], $form_data); // разбираем строку запроса
			$id	= $form_data['id'];
			
			$db = JFactory::getDbo();
			/* удаление файлов */
			$query	= $db->getQuery(true);	
			$query->select('path');
			$query->from('#__flats_images');
			$query->where('id = '.$id);
			$db->setQuery($query);
			if (!$db->query()) {
				JError::raiseError( 500, $db->stderr());
				return false;
			}
			$img = $db->loadObject();
			unlink(JPATH_SITE.$img->path);
			
			/*удаление записей*/
			$query = $db->getQuery(true);
			$query->delete('#__flats_images');
			$query->where("id = ".$id);
			$db->setQuery($query);
			$db->execute();
					
			echo json_encode(array('status'=>'ok'));
		}else{
			echo json_encode(array('status'=>'error'));
		}
	}
	
	public function upload_img_flats() {
		$data = array();
		$files = array();
		$html = "";
		$type_try = array("jpg", "JPG", "JPEG", "jpeg", "png", "PNG", "gif", "GIF");
		$error_file = "";
		if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
		if (!extension_loaded('zlib')) {
			JError::raiseWarning('', JText::_('COM_INSTALLER_MSG_INSTALL_WARNINSTALLZLIB'));
			return false;
		}
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		$dir = 'tmp/user_files';
		
		if(!JFolder::exists($dir)) JFolder::create($dir, 0777);    
		jimport('mavik.thumb.generator');
		$thumbGenerator = new MavikThumbGenerator(array(
			'thumbDir' => 'cache/thumbnails/flats', // Директория для превьюшек
			'quality' => 100, // Качество jpg. От 1 до 100.
			'subDirs' => false,
		));		
		foreach( $_FILES as $file ){
		$type = substr($file['name'], strrpos($file['name'], '.') + 1);
			if(in_array($type, $type_try)){
			$number_file = rand(0, 999999) . "." . $type;
				move_uploaded_file( $file['tmp_name'], $dir . "/" . $number_file);
				
				$file_name = $dir. "/" . $number_file;
				//$file_name = $this->wotermark($file_name, $type, $dir);
				
				
				try { 
					$image = $thumbGenerator->getThumb(JURI::root().$file_name, 200, 150); 
					$src = $image->thumbnail->url;
				}catch(Exception $exc){
					$src = JURI::root().$file_name;
				}
			
			
			$html .= "
							<div class='images_list'>
								<div>
									<a href='/". $file_name ."' class='fancybox'>
										<img src='".$src."'>
									</a>	
								</div>
								<div>
									<input type='hidden' name='jform[images][]' value='/".$file_name."'>
									<label>Порядок</label>
									<input style='width:30px;' type='text' class='numbers' name='jform[images_order][]' value='0'>
									<a class='del' onclick='javascript:del_img(\"0\", this);'>Удалить</a><br/>
													
									<label>Название</label>
									<input style='width:300px;' class='inpresize' type='text' name='jform[images_title][]' value=''><br/>
													
									<label>Описание</label>
									<textarea name='jform[images_description][]'></textarea>
								</div>
			</div>";
			}else{
			$error_file .= $file['name'];
			}
		}	
	echo json_encode(array('status'=>'ok','html'=>$html, 'error_file'=>$error_file));
}
	public function display($cachable = false, $urlparams = false){
		$cachable = true;

		// Set the default view name and format from the Request.
		$vName = $this->input->get('view', 'categories');
		$this->input->set('view', $vName);

		$user = JFactory::getUser();

		if ($user->get('id') || ($this->input->getMethod() == 'POST' && $vName = 'category' ))
		{
			$cachable = false;
		}

		$safeurlparams = array('id' => 'INT', 'limit' => 'UINT', 'limitstart' => 'UINT', 'filter_order' => 'CMD', 'filter_order_Dir' => 'CMD', 'lang' => 'CMD');

		parent::display($cachable, $safeurlparams);
	}
}
