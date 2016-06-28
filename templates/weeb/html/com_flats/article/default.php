<?php
defined('_JEXEC') or die;
$doc = JFactory::getDocument();

$back = $_SERVER['REQUEST_URI'];
$back = explode("/", $back);
$length = count($back);
$link = '';

foreach($back as $kay=>$val){
if($kay+1 < $length)
$link .= $val."/";	
}

?>
<?php 
				if($this->item->rooms=='1'){
					$rooms = 'однокомнатн';
				}elseif($this->item->rooms=='2'){
					$rooms = 'двухкомнатн';
				}elseif($this->item->rooms=='3'){
					$rooms = 'трехкомнатн';
				}elseif($this->item->rooms=='4'){
					$rooms = 'четырехкомнатн';
				}
					$catid = $this->item->catid;
					if($catid == 10){
						$name = 'Микрорайон 6а';
						$address = 'ул. Некрасова';
					}elseif($catid == 14){
						$name = 'ЖК «Маяк»';
						$address = 'ул. Комсомольская';
					}
				?>
		<section class="plan-kv">
			<div class="middle-block">
				<div class="plan-kv-wrap">
					<div class="title-all">
						<h1>Купить трехкомнатную квартиру в <span><?php echo $name; ?></span></h1> 
						<p>- новостройке в Реутове, корпус <span><?php echo $this->item->dom;?></span>, квартира <span><?php echo $this->item->num;?></span></p>
					</div>
					<div class="back-btn">
						<a href="<?php echo $link; ?>" title="Вернуться к выбору">Вернуться к выбору</a>
					</div>
					<div class="plan-kv-box">
						<div class="left">
							<div class="img">
								<img src="<?php echo $this->item->planirovka; ?>" alt="">
							</div>
						</div>
						<div class="right">
							<div class="description">
								<ul>
								<?php
								$price = $this->item->price_2;
								$price = number_format($price, 0, " ", " ");
								?>
									<li><span>Квартира</span>№<?php echo $this->item->num;?></li>
									<li><span>Корпус:</span><?php echo $this->item->dom;?></li>
									<li><span>Этаж:</span><?php echo $this->item->etazh;?></li>
									<li><span>Кол-во комнат:</span><?php echo $this->item->rooms;?></li>
									<li><span>Общая площадь:</span><?php echo $this->item->s_obch;?> м²</li>
									<li><span>Цена:</span><b><?php echo $price;?> руб.</b></li>
								</ul>
								<div class="description-btn">
									<a href="#call_me" title="Заявка на просмотр" class="viewing open-modal">Заявка на просмотр</a>
									<a href="#call_me" title="Заявка на бронь" class="reservation open-modal">Заявка на бронь</a>
								</div>
							</div>
							<?php
							
								$staff_id = $this->item->staff;

								
							$db	= JFactory::getDbo();
							$ise = $db->getQuery(true);
							$ise->select('*');
							$ise->from('#__staffs');
							$ise->where('id = '.$staff_id);								
							$db->setQuery($ise);		
							$resise = $db->loadAssoc() ;
							$images = $resise['images'];
							$images = json_decode($images); 
							$images = $images->image_first;
							jimport('mavik.thumb.generator');
							$thumbGenerator = new MavikThumbGenerator(array(
								'thumbDir' => 'cache/thumbnails/houses', // Директория для превьюшек
								'subDirs' => false, // Создавать поддиректории для повторения оригинальной структуры директорий
								'quality' => 90, // Качество jpg. От 1 до 100.
								'resizeType' => 'fill', // Метод ресайзинга. Доступные значения: fill, fit, streach, area
								'defaultSize' => '', // Когда использовать размер по умолчанию. Возможные значения: '', 'all', 'not_resized' - никогда, всегда, если размер не изменен (совпадает с оригиналом)
								'defaultWidth' => null, // Ширина по умолчанию
								'defaultHeight' => null, // Высота по умолчанию
							));
							try { 
								$image = $thumbGenerator->getThumb(JPATH_BASE.'/'.$images, 221, 272);  
								$src = $image->thumbnail->url;
							} catch (Exception $exc) {
								$src = '/'.$images;
							} 
							if($src == '/' || empty($src)){
								$src = $images;
							}
							?>
							<div class="staff-wrap">
								<div class="staff-text">
									<h3>Есть вопросы?</h3>
									<p class="name"><?php echo $resise['last_name'].' '.$resise['first_name'].' '.$resise['second_name']; ?></p>
									<small><?php echo $resise['job_title']; ?></small>
									<a href="tel:<?php echo $resise['phone']; ?>" class="tel"><?php echo $resise['phone']; ?></a>
									<a href="tel:<?php echo $resise['mobile']; ?>" class="tel"><?php echo $resise['mobile']; ?></a>
									<a  href="#call_me" title="Перезвоните мне" class="call-me open-modal">Перезвоните мне</a>
								</div>
								<div class="staff-img">
									<img src="<?php echo $src; ?>" alt="">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
		<section class="choose-another mw">
			<div class="middle-block">
				<div class="choose-another-ban">
					<h2>Не нравится квартира? Подберем другую</h2>
					<div>
						<a href="tel:+74955041595)" class="choose-tel"><span>+7 (495)</span> 504 15 95</a>
						<a href="#call_me" title="Бесплатная консультация" class="choose-call-me open-modal">Бесплатная консультация</a>
					</div>
				</div>
				
				<p>Купить <?php echo $rooms; ?>ую квартиру в <?php echo $name; ?>, <?php echo $address; ?> в Реутове площадью <?php echo $this->item->s_obch;?> м².
				Стоимость <?php echo $price; ?> млн. рублей. Комфортное жилье на 24-м этаже монолитно-кирпичного дома.
				Ипотека на квартиры в новостройке <?php echo $name; ?> в Реутове от 11,5%. Помощь в оформлении
				документов и получении ипотеки, юридическое сопровождение сделки.</p>

				<p>Специалисты Официального центра продажи квартир в новостройках в Реутове помогут вам
				<b>найти, подобрать и купить квартиру в новостройке Реутова</b>, которая будет максимально
				соответствовать вашим пожеланиям и бюджету. Мы осуществляем <b>прямые продажи квартир от
				застройщиков</b>, поэтому договоримся о скидке и поможет оформить сделку на условиях, которые
				в первую очередь будут выгодны вам. Сегодня <b>новостройки города Реутов</b> представляют собой
				современное жилье в благополучном районе с хорошей экологией и развитой инфраструктурой – что
				и нужно для комфортной жизни. Вы сможете совершать покупки и водить детей в детский сад возле
				дома, ведь все необходимое будет под рукой. </p>
			</div>
		</section>	
	