<?php
defined('_JEXEC') or die;
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
$app = JFactory::getApplication();
//всплывающее окно
JFactory::getDocument()->addScript(JURI::root().'templates/weeb/js/jquery-latest.js');
JFactory::getDocument()->addScript(JURI::root().'templates/weeb/js/jquery.fancybox.js?v=2.1.5');	
JFactory::getDocument()->addStylesheet(JURI::root().'templates/weeb/css/jquery.fancybox.css?v=2.1.5');	
$script = '
	(function( $ ){
		$(".fancybox").fancybox();
	})( jQuery );
/* ------------------------------------------------------ мультизагрузка */
jQuery(document).ready(function () {
	var files;
	jQuery("#image_file").change(function(){
	files = this.files;
	});
	jQuery(".my_submit").click(function( event ){
		event.stopPropagation(); // Остановка происходящего
		event.preventDefault();  // Полная остановка происходящего
		var data = new FormData();
		jQuery.each( files, function( key, value ){
		data.append( key, value );
		});
		jQuery("#overlay").show();
		jQuery.ajax({
			url: "'.JURI::root().'index.php?option=com_flats&task=upload_img_flats&format=raw",
			type: "POST",
			data: data,
			cache: false,
			dataType: "json",
			processData: false, 
			contentType: false, 
			success:function(dat){
				
				if(dat.status == "ok") {
					jQuery(".form-vertical #images_list").append(dat.html);
				}else{
					alert("ошибка");
				}
				jQuery("#overlay").hide();
				if(dat.error_file != ""){
				jQuery("#error_file").html("Файлы которые не были загружены:" + dat.error_file);
				}
			}
		})
	});
});

		/* ------------------------------------------ конец мультизагрузка */
	function numerize(){
		var list = jQuery(".numbers");
		for(var i=0; i<list.length; i++){
			list[i].value = i;
		}
	}
	function del_img(id, el){
		if(confirm("Удалить?")) {
			if(id>0){
				jQuery.ajax({
					url: "'.JURI::root().'index.php?option=com_flats&task=del_img&format=raw", // путь к обработчику
					type: "POST", // метод передачи
					data: {form_data: "id="+id},
					dataType: "json",
					success: function(json){
						if(json.status=="ok"){
							jQuery(el).parent().parent().remove();
						}else{
							alert("Ошибка");
						}
					}
				});
			}else{
				jQuery(el).parent().parent().remove();
			}
		}else{
			
		}	
	}
';
JFactory::getDocument()->addScript(JURI::root().'templates/weeb/js/fileupload.js');
JFactory::getDocument()->addScriptDeclaration($script);
$input = $app->input;
JFactory::getDocument()->addStyleDeclaration('
	.images_list {
		width: 590px;
		margin: 5px;
		display: inline-table;
	}
	.images_list img {
		border: 2px solid #ccc;
		width: 250px;
		height: 180px;
	}
	.images_list div {
		display: inline-table;
		vertical-align: top;
		margin: 0px 5px;
	}
	.images_list textarea {
		width: 300px;
		height: 60px;
	}
	.images_list a.del {
		display: inline-block;
		border: 1px solid gray;
		padding: 3px 10px;
		border-radius: 3px;
		margin-left: 50px;
		cursor: pointer;
		text-decoration: none;
	}
	.images_list label {
		font-size: 10px;
		cursor: default;
		margin: 0;
	}
	/******/
	.form-vertical .images_list {
		width: 590px;
		margin: 5px;
		display: inline-table;
	}
	.form-vertical .images_list img {
		border: 2px solid #ccc;
		width: 250px;
		height: 180px;
	}
	.form-vertical .images_list div {
		display: inline-table;
		vertical-align: top;
		margin: 0px 5px;
	}
	.form-vertical .images_list textarea {
		width: 300px;
		height: 60px;
	}
	.form-vertical .images_list a.del {
		display: inline-block;
		border: 1px solid gray;
		padding: 3px 10px;
		border-radius: 3px;
		margin-left: 50px;
		cursor: pointer;
		text-decoration: none;
	}
	.form-vertical .images_list label {
		font-size: 10px;
		cursor: default;
		margin: 0;
	}
			.my_submit{
	margin-right: 30px;
	color: #fff;
    text-shadow: 0 -1px 0 rgba(0,0,0,0.25);
    background-color: #5bb75b;
	padding: 5px 10px;
    font-size: 12px;
	border-radius: 3px;
	line-height: 18px;
    text-align: center;
	}
	.my_submit:hover{
	background-color: #51a351;
	color: white;
	}
	#overlay {
    z-index: 3;
    position: fixed;
    background-color: #FFF;
    opacity: 0.8;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
	display: none;
	}
	#overlay img {
	position: absolute;
	left: 50%;
	margin-left: -50px;
	top: 50%;
	margin-top: -50px;
	}
	#error_file{
	color: red;
	
	}
	
	
	
');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'flat.cancel' || document.formvalidator.isValid(document.id('flat-form'))) {
			Joomla.submitform(task, document.getElementById('flat-form'));
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_flats&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="flat-form" class="form-validate">	

	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'location')); ?>
			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'location', JText::_('Информация', true)); ?>
			
				<div class="row-fluid form-horizontal-desktop">
					<div class="span6">
						<?php echo $this->form->getControlGroup('published'); ?>
						<?php echo $this->form->getControlGroup('catid'); ?>
						<?php echo $this->form->getControlGroup('dom'); ?>
						<?php echo $this->form->getControlGroup('sekciya'); ?>
						<?php echo $this->form->getControlGroup('etazh'); ?>
						<?php echo $this->form->getControlGroup('num'); ?>
						<?php echo $this->form->getControlGroup('num_pp'); ?>
						<?php echo $this->form->getControlGroup('num_kv'); ?>
						<?php echo $this->form->getControlGroup('rooms'); ?>
						<?php echo $this->form->getControlGroup('s_obch'); ?>
						<?php echo $this->form->getControlGroup('sqm_price'); ?>
						<?php echo $this->form->getControlGroup('price'); ?>
						<?php echo $this->form->getControlGroup('price_2'); ?>	
						<?php echo $this->form->getControlGroup('staff'); ?>	
						
					</div>
					<div class="span6">
						<?php echo $this->form->getControlGroup('planirovka'); ?>
						<?php
							if($this->item->planirovka){
								echo "
									<div id='images_list'>
										<div class='images_list'>
											<div>
												<a href='/".$this->item->planirovka."' target='_blank'>
													<img src='/".$this->item->planirovka."'>
												</a>	
											</div>													
										</div>	
									</div>
								";
							}
						?>
						<?php echo $this->form->getControlGroup('akcia'); ?>
						<?php echo $this->form->getControlGroup('cian'); ?>
						<?php echo $this->form->getControlGroup('metro_cian'); ?>
						
						<?php echo $this->form->getControlGroup('premium'); ?>
						<?php echo $this->form->getControlGroup('avito'); ?>
						<?php echo $this->form->getControlGroup('avito_type'); ?>
						
					</div>
				</div>
				<div class="form-vertical">
					<?php echo $this->form->getControlGroup('text'); ?>
				</div>	
			<?php echo JHtml::_('bootstrap.endTab'); ?>
			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'system', JText::_('Системные', true)); ?>
				<div class="row-fluid form-horizontal-desktop">
					<div class="span6">
						
						<?php echo $this->form->getControlGroup('checked_out'); ?>
						<?php echo $this->form->getControlGroup('created'); ?>
						<?php echo $this->form->getControlGroup('created_by'); ?>
						<?php echo $this->form->getControlGroup('modified'); ?>
						<?php echo $this->form->getControlGroup('modified_by'); ?>
						<?php echo $this->form->getControlGroup('checked_out_time'); ?>
						<?php echo $this->form->getControlGroup('hits'); ?>
						<?php echo $this->form->getControlGroup('language'); ?>
						<?php echo $this->form->getControlGroup('access'); ?>
					</div>
				</div>	
			<?php echo JHtml::_('bootstrap.endTab'); ?>
			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'images', JText::_('Изображения/Видео', true)); ?>
				<div class="row-fluid form-horizontal-desktop">
					<div class="form-vertical">
						<?php echo $this->form->getControlGroup('youtube_id'); ?>
						<table cellpadding="5">
							<tr>
								<td>
									<input type="button" name="rand_num" value="Пронумеровать" onclick="javascript:numerize();" >
								</td>	
								<td>
									
									<!--<input type="file" name="image_file" id="image_file" onclick="javascript:img_loader(this);" >-->
									<input type="file" name="image_file" multiple accept="image/*,image/jpeg" multiple="multiple" id="image_file" >
									<a href="#"  class="my_submit">Загрузить файлы</a>
									<span id="error_file"></span>
								</td>
								
							</tr>
						</table>
							<div id="images_list">
							<?php
								if($this->item->id>0){
									$db = JFactory::getDbo();
									$query	= $db->getQuery(true);	
									$query->select('*');
									$query->from('#__flats_images');
									$query->where('object_id = '.$this->item->id);
									$query->order('orders asc');
									$db->setQuery($query);
									if (!$db->query()) {
										JError::raiseError( 500, $db->stderr());
										return false;
									}
									$images = $db->loadObjectList();
									if(count($images)>0){
										foreach($images as $image){
											echo "
												<div class='images_list'>
													<div>
														<a href='".JURI::root().$image->path."' class='fancybox'>
															<img src='".JURI::root().$image->path."'>
														</a>	
													</div>
													<div>
														<input type='hidden' name='jform[images][]' value='".$image->path."'>
														<label>Порядок</label>
														<input style='width:30px;' type='text' class='numbers' name='jform[images_order][]' value='".$image->orders."'>
														<a class='del' onclick='javascript:del_img(\"".$image->id."\", this);'>Удалить</a><br/>
														
														<label>Название</label>
														<input style='width:300px;' type='text' name='jform[images_title][]' value='".$image->title."'><br/>
														
														<label>Описание</label>
														<textarea name='jform[images_description][]'>".$image->description."</textarea>
													</div>
												</div>	
												";
										}
									}
								}
							?>
						</div>
					</div>
				</div>
			<?php echo JHtml::_('bootstrap.endTab'); ?>		
		<?php echo JHtml::_('bootstrap.endTabSet'); ?>
	</div>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
<div id="overlay"><img src="/images/loading/loading7.gif"></div>
