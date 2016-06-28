<?php
defined('_JEXEC') or die;
$type = $params->get('type');
jimport('mavik.thumb.generator');
$thumbGenerator = new MavikThumbGenerator(array(
	'thumbDir' => 'cache/thumbnails/staff', // Директория для превьюшек
	'subDirs' => false, // Создавать поддиректории для повторения оригинальной структуры директорий
	'quality' => 90, // Качество jpg. От 1 до 100.
	'resizeType' => 'fill', // Метод ресайзинга. Доступные значения: fill, fit, streach, area
	'defaultSize' => '', // Когда использовать размер по умолчанию. Возможные значения: '', 'all', 'not_resized' - никогда, всегда, если размер не изменен (совпадает с оригиналом)
	'defaultWidth' => null, // Ширина по умолчанию
	'defaultHeight' => null, // Высота по умолчанию
));
?>
<?php if(($type=='1')or($type=='2')){ ?>
	<?php 
		$images = json_decode($item->images); 
		try { 
			$image = $thumbGenerator->getThumb($images->image_first, 230, 160);
			$src = $image->thumbnail->url;
		}catch(Exception $exc) {
			$src = $images->image_first;
		} 	
		$slug	= $item->alias ? ($item->id.':'.$item->alias) : $item->id;
	?>
	
	<a href="<?php echo JRoute::_(StaffsHelperRoute::getStaffRoute($slug, $item->catid)); ?>">
		<img src="<?php echo $src;?>" alt="<?php echo $images->image_first_alt;?>">
		<div class="bot_line">
			<?php echo $item->last_name;?><br/>
			<?php echo $item->first_name;?> <?php echo $item->second_name;?>
		</div>
	</a>
<?php }else{ ?>
	<?php foreach($items as $staff){ ?>
		<?php 
			$images	= json_decode($staff->images); 
			try { 
				$image	= $thumbGenerator->getThumb($images->image_first, 230, 160);
				$src = $image->thumbnail->url;
			}catch(Exception $exc){
				$src = $images->image_first;
			} 		
			$slug	= $staff->alias ? ($staff->id.':'.$staff->alias) : $staff->id;
		?>
		<div class="staff_mod">
			<a href="<?php echo JRoute::_(StaffsHelperRoute::getStaffRoute($slug, $staff->catid)); ?>">
				<img src="<?php echo $src;?>" alt="<?php echo $images->image_first_alt;?>">
			</a>	
			<div class="bot_line">
				<a href="<?php echo JRoute::_(StaffsHelperRoute::getStaffRoute($slug, $staff->catid)); ?>">
					<div class="name">
						<?php echo $staff->last_name;?><br/>
						<?php echo $staff->first_name;?> <?php echo $staff->second_name;?>
					</div>
					<div class="job_title">
						<?php echo $staff->job_title;?>
					</div>
				</a>
			</div>
		</div>
	<?php } ?>
<?php } ?>