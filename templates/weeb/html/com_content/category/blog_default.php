<?php
defined('_JEXEC') or die;
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
jimport('mavik.thumb.generator');
$thumbGenerator = new MavikThumbGenerator(array(
	'thumbDir' => 'cache/thumbnails/blog', // Директория для превьюшек
	'quality' => 90, // Качество jpg. От 1 до 100.
	'subDirs' => false,
));

function cutting_text5($text, $length, $word=false){
	if ($word) {
		return preg_match('/((\S+[\s-]+){'.$length.'})/s', $text, $m)? rtrim($m[1]): $text;
	}else{
		return preg_replace('/\s[^\s]+$/', '', mb_substr($text, 0, $length, 'UTF-8'));
	}	
}

?>

<!-- ----------------------------------------------------- -->	
	<section class="news-page <?php echo $this->pageclass_sfx;?>">
			<div class="middle-block">
				<div class="title-all">
					<h1>Новости</h1>
				</div>
				<div class="news-page-wrap">
				<?php if (!empty($this->lead_items)) : ?>
					<ul>
					<?php foreach ($this->lead_items as &$item) : ?>
					<?php 
								$domen = 'http://'.$_SERVER['HTTP_HOST'].'/';
								$images  = json_decode($item->images);
								if (isset($images->image_intro) && !empty($images->image_intro)){
									$image = $thumbGenerator->getThumb($domen.$images->image_intro, 230, 230);
									$img = '<img src="'.$image->thumbnail->url.'" title="'.$this->escape($item->title).'" alt="'.$this->escape($item->title).'">';							
								}else{
									$img = '<img src="/templates/weeb/images/m-hed-icon.png" title="'.$this->escape($item->title).'" alt="'.$this->escape($item->title).'">';
								}
							?>
						<li>
							<?php echo $img; ?>
							<div class="news-page-info">
								<h2><?php echo $this->escape($item->title); ?></h2>
								<small class="date"><?php echo JHtml::_('date', $item->created, 'd.m.Y'); ?></small>
								<p><?php echo cutting_text5(strip_tags($item->introtext), 50, true); ?>...</p>
								<a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catid)); ?>" title="Подробнее">Подробнее</a>
							</div>
						</li>	
					<?php endforeach; ?>						
					</ul>
				<?php endif; ?>
				</div>
			<?php if (($this->params->def('show_pagination', 1) == 1  || ($this->params->get('show_pagination') == 2)) && ($this->pagination->get('pages.total') > 1)) : ?>	
				<div class="blog-pagination navigation-list">
					<?php echo $this->pagination->getPagesLinks(); ?>
				</div>
			<?php  endif; ?>
				
			</div>
			
		</section>
	
	

