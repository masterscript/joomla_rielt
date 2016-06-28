<?php
defined('_JEXEC') or die;
$type = $params->get('type');
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
?>
<?php if(($type=='1')or($type=='2')){ ?>
	<?php $images = json_decode($item->images); ?>
	<img src="<?php echo $images->image_first;?>" alt="<?php echo $images->image_first_alt;?>">
	<div class="bot_line">
		<?php echo $item->last_name;?><br/>
		<?php echo $item->first_name;?> <?php echo $item->second_name;?>
	</div>
<?php }else{ ?>

			<?php $num = 0;
			foreach($items as $staff){ ?>
				<?php $num++;
					if (($num % 2) == 0){
					$otstup = "float: right;";
					}else{$otstup = $num;}
					$images = json_decode($staff->images); 
					$images = $images->image_first;
					$slug = $staff->alias ? ($staff->id . ':' . $staff->alias) : $staff->id;
					
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
					

					   
					   <div class="staff">
							<img src="<?php echo $src; ?>" alt="">
							<h3><?php echo $staff->last_name;?> <?php echo $staff->first_name;?> <?php echo $staff->second_name;?></h3>
							<span><?php echo $staff->job_title;?></span>
							<a href="tel:<?php echo str_replace(" ", "", $staff->phone);?>" class="phone"><?php echo $staff->phone;?></a>
							<a href="tel:<?php echo str_replace(" ", "", $staff->mobile);?>" class="phone"><?php echo $staff->mobile;?></a>
							<a href="#call_me" title="Перезвоните мне" class="fancybox call-me">Перезвоните мне</a>
						</div>
					   
					   
					   
					   
			<?php } ?>
<?php } ?>