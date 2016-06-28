<?php
defined('_JEXEC') or die;
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');


?>
<div class="staff-category">
	<?php if ($this->params->get('show_page_heading')) : ?>
		<div class="page-header">
			<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
		</div>
	<?php endif; ?>
	<div class="items">
		<?php foreach ($this->no_consult as $i => $item) : 
				$images = json_decode($item->images); 
				$image = $thumbGenerator->getThumb($images->image_first, 265, 190); ?>
			<div class="staff_mod">
				<a href="<?php echo JRoute::_(StaffsHelperRoute::getStaffRoute($item->slug, $item->catid)); ?>">
					<img src="<?php echo $image->thumbnail->url; ?>" alt="<?php echo $images->image_first_alt;?>">
				</a>
				<div class="bot_line">
					<div class="name">
						<a href="<?php echo JRoute::_(StaffsHelperRoute::getStaffRoute($item->slug, $item->catid)); ?>">
							<?php echo $item->last_name;?><br/><?php echo $item->first_name;?> <?php echo $item->second_name;?>
						</a>	
					</div>
					<div class="job_title">
						<?php echo $item->job_title;?>
					</div>
				</div>
				<div class="bot_line2">
					<div class="phone">
						<?php echo $item->phone;?>
					</div>
					<div class="buttons">
						<a href="" title="" class="send_message">Написать письмо</a>
					</div>	
				</div>
			</div>
		<?php endforeach; ?>
		<div class="control_quality">
			<div class="red_block">
				<h4>Независимая служба контроля качества</h4>
				<p>Если у вас есть замечания по работе сотрудников - обратитесь к нам.</p>
				<p>Мы <strong>обязательно</strong> решим ваш вопрос. Анонимность гарантируем.</p>
				<a href="#" class="write_us">Написать нам</a>
			</div>
			<div class="white_block">
				<p>Для нашей дружной, сплоченной, проверенной временем команды, без преувеличения, нет неразрешимых жилищных вопросов.</p>
				<p>Каждый из наших специалистов – асс в своем деле, готовый помочь вам во всем. Мы не просто настоящие, квалифицированные, надежные профи, но еще и внимательные, неравнодушные люди. Для нас действительно важно, чтобы вы получили именно то, что хотели и даже больше.</p>
				<p><strong>Обращайтесь – и мы обязательно найдем самое быстрое и выгодное решение вашего вопроса!</strong></p>
			</div>
		</div>
		<?php foreach ($this->consult as $i1 => $item1) :
				$images1 = json_decode($item1->images);
				$image1 = $thumbGenerator->getThumb($images1->image_first, 265, 190); 
		?>
			<div class="staff_mod">
				<a href="<?php echo JRoute::_(StaffsHelperRoute::getStaffRoute($item1->slug, $item1->catid)); ?>">
					<img src="<?php echo $image1->thumbnail->url; ?>" alt="<?php echo $images1->image_first_alt;?>">
				</a>	
				<div class="bot_line">
					<div class="name">
						<a href="<?php echo JRoute::_(StaffsHelperRoute::getStaffRoute($item1->slug, $item1->catid)); ?>">
							<?php echo $item1->last_name;?><br/><?php echo $item1->first_name;?> <?php echo $item1->second_name;?>
						</a>
					</div>
					<div class="job_title">
						<?php echo $item1->job_title;?>
					</div>
				</div>
				<div class="bot_line2">
					<div class="phone">
						<?php echo $item1->phone;?>
					</div>
					<div class="buttons">
						<a href="<?php echo JRoute::_('index.php?option=com_reviews&view=category&id=10&to_staff='.$item1->id); ?>" title="" class="all_reviews">Отзывы</a>
						<a href="" title="" class="ask_questions">Задать вопрос</a>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>
