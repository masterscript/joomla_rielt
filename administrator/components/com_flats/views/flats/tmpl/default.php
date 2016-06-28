<?php
defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');


$app		= JFactory::getApplication();
$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$archived	= $this->state->get('filter.published') == 2 ? true : false;
$trashed	= $this->state->get('filter.published') == -2 ? true : false;
$canOrder	= $user->authorise('core.edit.state', 'com_flats.category');
$saveOrder	= $listOrder == 'a.id';
if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_flats&task=flats.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'articleList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
$sortFields = $this->getSortFields();
$assoc		= JLanguageAssociations::isEnabled();
JFactory::getDocument()->addScript('http://code.jquery.com/jquery-latest.js');
JFactory::getDocument()->addScript(JURI::root().'templates/weeb/js/fileupload.js');
JFactory::getDocument()->addScriptDeclaration('
	
	function act(type){
		var form = jQuery("#"+type);
		var data = form.serialize();
		
		jQuery.ajax({
			url: "'.JURI::root().'index.php?option=com_flats&task="+type+"&format=raw", // путь к обработчику
			type: "POST", // метод передачи
			data: {form_data: data},
			dataType: "json",
			success: function(json){
				alert(json.html);
				jQuery("#table_shah").append(json.res);				
			}
		});
	}
	
	function file_loader(el){
		jQuery("#image_file").fileupload({
			url:"'.JURI::root().'/index.php?option=com_flats&task=upload_file1&format=raw",
			success:function(dat){
				if(dat.status == "ok") {
					jQuery.ajax({
						url: "'.JURI::root().'index.php?option=com_flats&task=unzip_file&format=raw", // путь к обработчику
						type: "POST", // метод передачи
						data: {form_data: "path="+dat.file},
						dataType: "json",
						success: function(json){
							if(json=="1"){
								jQuery("#h_div").removeClass("hid_div");
								jQuery("#h_div").addClass("inline");
							}else{
								alert(json);
							}
						}
					});
				}else{
					alert("ошибка");
				}
		},
		dataType:"json"
	  });
	}
	function archive_loader(el){ 
	var inp_name = "cid[]";
	var checkid = "";	
	var i = 0;
	var dlina = jQuery("input[name=\'"+inp_name+"\']:checked");
	dlina.each(function (i){
		i = i+1;
		if(i != 1){
			checkid += "," + jQuery(this).val();
		}else{
			checkid += jQuery(this).val();
		}
			
	});	
	if(dlina.length == 0){
			alert ("Вы не выбрали объекты");
	}else{
		
		jQuery("#archive_file").fileupload({		
			url:"'.JURI::root().'/index.php?option=com_flats&task=upload_archive_images&format=raw&my_id="+checkid,		
			success:function(dat){
				if(dat.status == "ok") {					
				alert(dat.html);
				}
			},
			dataType:"json" 
		});
	  }
	}	
	
function textfromall(el){
	var text = jQuery("#text-from-all").val();
	var inp_name = "cid[]";
	var checkid = "";	
	var i = 0;
	var dlina = jQuery("input[name=\'"+inp_name+"\']:checked");
	dlina.each(function (i){
		i = i+1;
		if(i != 1){
			checkid += "," + jQuery(this).val();
		}else{
			checkid += jQuery(this).val();
		}
			
	})	
	if(dlina.length == 0){
			alert ("Вы не выбрали объекты");
	}else{
	jQuery.ajax({
						url: "'.JURI::root().'index.php?option=com_flats&task=text_from_all&format=raw&my_id="+checkid, // путь к обработчику
						type: "POST", // метод передачи
						data: {form_data: "text="+text},
						dataType: "json",
						success: function(json){
						if(json.status == "ok"){
							alert ("Текст добавлен в отмеченые объекты");	
						}							
						}
					});
	  }
	};');
?>
<script type="text/javascript">
	Joomla.orderTable = function()
	{
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrder; ?>')
		{
			dirn = 'asc';
		}
		else
		{
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}
	
</script>
<style>
	.inline {
		display: inline-table;
		vertical-align: top;
	}
	span.file {
		position: absolute;
		left: 0px;
		top: 0px;
		border-radius: 3px;
		display: block;
		width: 150px;
		height: 26px;
		z-index: 1;
		border: 1px solid #cecece;
		text-align: center;
		line-height: 26px;		
	}
	#image_file {
		position: relative;
		display: block;
		width: 150px;
		height: 26px;
		text-indent: -99999px;
		border: none;
		z-index: 999;
		background: transparent;
		color: transparent;
		cursor: pointer;
	}
	.hid_div {
		display: none;
	}
</style>
<div>
	<ol>
		<!--<li>
			<p>Установка акционной цены</p>
			<form method="POST" action="javascript:void(null);" id="action_price" name="action_price" onsubmit="act('action_price');">
				<div class="inline">
					<select name="catid" class="input-medium" >
						<option value="">Выберите новостройку</option>
						<?php echo JHtml::_('select.options', JHtml::_('category.options', 'com_flats'), 'value', 'text', '');?>
					</select>
				</div>
				<div class="inline">
					<input type="text" name="rooms" placeholder="К-ство комнат">
				</div>
				<div class="inline">
					<input type="text" name="skidka" placeholder="Укажите процент">
				</div>
				<div class="inline">
					<input type="radio" name="type" value="plus">+
					<input type="radio" name="type" value="minus">-
					<input type="radio" name="type" value="del">del
				</div>
				<div class="inline">
					<input type="submit" value="ОК">							
				</div>
			</form>
		</li>
		<li>
			<p>Установка значения отметки Циан/премиум</p>
			<form method="POST" action="javascript:void(null);" id="action_cian" name="action_cian" onsubmit="act('action_cian');">
				<div class="inline">
					<select name="catid" class="input-medium" >
						<option value="">Выберите новостройку</option>
						<?php echo JHtml::_('select.options', JHtml::_('category.options', 'com_flats'), 'value', 'text', '');?>
					</select>
				</div>
				<div class="inline">
					<input type="text" name="rooms" placeholder="К-ство комнат">
				</div>
				<div class="inline">
					<select name="type" class="input-medium" >
						<option value="">Выберите тип</option>
						<option value="cian">Циан</option>
						<option value="premium">Премимум</option>
					</select>
				</div>
				<div class="inline">
					<select name="value" class="input-medium" >
						<option value="0" selected="selected">?</option>
						<option value="2">Нет</option>
						<option value="1">Да</option>
					</select>
				</div>
				<div class="inline">
					<input type="submit" value="ОК">							
				</div>
			</form>
		</li>-->
		<li>
			<p>Загрузка шахматки</p>
			<form  method="POST" action="javascript:void(null);" id="load_shah" name="load_shah" onsubmit="act('load_shah');" >
				<div class="inline" style="position:relative;">
					<span class="file">Выберите файл</span>
					<input type="file" name="image_file" id="image_file" onclick="javascript:file_loader(this);" >
				</div>
				<div id="h_div" class="hid_div">
					<div class="inline">
						<select name="zk" class="input-medium" >
							<option value="">Выберите ЖК</option>
							<option value="10">Микрорайон 6а</option>
							<option value="14">Комсомольская</option>
						</select>
					</div>
					<div class="inline">
						<input type="text" name="dom" placeholder="номер дома" value=""/>
					</div>	
					<div class="inline">
						<input type="submit" value="Загрузить"/>
					</div>
				</div>
			</form>
			<div id="table_shah"></div>
		</li>
		<li>
			<p>Заменить сотрудников</p>
			<form  method="POST" action="javascript:void(null);" id="staffs_add" name="staffs_add" onsubmit="act('staffs_add');" >
				<div class="inline">
						<select name="zk" class="input-medium" >
							<option value="">Выберите ЖК</option>
							<option value="10">Микрорайон 6а</option>
							<option value="14">Комсомольская</option>
						</select>
					</div>
				<div class="inline">
						<input type="text" name="dom" placeholder="номер дома" value="" required/>
				</div>
				<div class="inline">
						<input type="text" name="staff" placeholder="id сотрудника" value="" required/>
				</div>					
				
					<div class="inline">
						<input type="submit" value="Загрузить"/>
					</div>
				</div>
			</form>
			<div id="table_shah"></div>
		</li>
		<!--<li>
			<p>Выберите архив изображений</p>
			<input type="file" name="archive_file" id="archive_file" onclick="javascript:archive_loader(this);" >
		</li>
		<li>
			<p>Введите текст (текст для отмеченных объектов)</p>
			<textarea id="text-from-all"></textarea>
			<input type="button" onclick="javascript:textfromall();" value="Загрузить"/>
		</li>-->
	</ol>	
</div>


<form action="<?php echo JRoute::_('index.php?option=com_flats&view=flats'); ?>" method="post" name="adminForm" id="adminForm">
<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>
		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo JText::_('COM_FLATS_FILTER_SEARCH_DESC');?></label>
				<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" class="hasTooltip" title="<?php echo JHtml::tooltipText('COM_FLATS_SEARCH_IN_TITLE'); ?>" />
			</div>
			<div class="btn-group pull-left hidden-phone">
				<button type="submit" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
				<button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
				<?php echo $this->pagination->getLimitBox(); ?>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
				<select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
					<option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
					<option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING');?></option>
					<option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING');?></option>
				</select>
			</div>
			<div class="btn-group pull-right">
				<label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?></label>
				<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
					<option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
					<?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder);?>
				</select>
			</div>
		</div>
		<div class="clearfix"> </div>
		<table class="table table-striped" id="articleList" style="font-size:12px;">
			<thead>
				<tr>
					<th width="1%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.id', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
					</th>
					<th width="1%" class="hidden-phone">
						<?php echo JHtml::_('grid.checkall'); ?>
					</th>				
					<th width="1%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
					</th>
					<th width="1%" class="nowrap center">
						<?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
					</th>
					<th width="1%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('grid.sort', 'Новостройка', 'a.catid', $listDirn, $listOrder); ?>
					</th>
					<th width="1%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('grid.sort', 'Дом', 'a.dom', $listDirn, $listOrder); ?>
					</th>
					<th width="7%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('grid.sort', 'Цена руб', 'a.price', $listDirn, $listOrder); ?>
					</th>
					<th width="5%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('grid.sort', 'Акционная<br/>цена, руб', 'a.price_2', $listDirn, $listOrder); ?>
					</th>
					<th width="5%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('grid.sort', 'Цена<br/>руб за м2', 'a.sqm_price', $listDirn, $listOrder); ?>
					</th>					
					<th width="1%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('grid.sort', 'Этаж', 'a.etazh', $listDirn, $listOrder); ?>
					</th>
					<th width="1%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('grid.sort', 'Секция', 'a.sekciya', $listDirn, $listOrder); ?>
					</th>					
					<th width="1%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('grid.sort', 'Комнат', 'a.rooms', $listDirn, $listOrder); ?>
					</th>	
					<th width="1%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('grid.sort', 'Номер<br/>квартиры', 'a.num', $listDirn, $listOrder); ?>
					</th>					
					<th width="1%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('grid.sort', 'Площадь<br/>м2', 'a.s_obch', $listDirn, $listOrder); ?>
					</th>
					<th width="1%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('grid.sort', 'Акция', 'a.akcia', $listDirn, $listOrder); ?>
					</th>
					<th width="1%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('grid.sort', 'Циан', 'a.cian', $listDirn, $listOrder); ?><br/>/<br/><?php echo JHtml::_('grid.sort', 'Премиум', 'a.premium', $listDirn, $listOrder); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="11">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
			<?php foreach ($this->items as $i => $item) :
				$ordering   = ($listOrder == 'a.id');
				$canCreate  = $user->authorise('core.create',     'com_houses.category.' . $item->catid);
				$canEdit    = $user->authorise('core.edit',       'com_houses.category.' . $item->catid);
				$canCheckin = $user->authorise('core.manage',     'com_checkin') || $item->checked_out == $user->get('id') || $item->checked_out == 0;
				$canChange  = $user->authorise('core.edit.state', 'com_houses.category.' . $item->catid) && $canCheckin;
			?>
				<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->catid?>">
					<td class="order nowrap center hidden-phone">
						<?php
						$iconClass = '';						
						?>
						<span class="sortable-handler<?php echo $iconClass ?>">
							<i class="icon-menu"></i>
						</span>						
							<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->id;?>" class="width-20 text-area-order" />						
					</td>
					<td class="center hidden-phone">
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					</td>
					<td class="center hidden-phone">
					<a href="<?php echo JRoute::_('index.php?option=com_flats&task=flat.edit&id='.(int) $item->id); ?>">
									<?php echo (int) $item->id; ?>
					</a>
					</td>
					<td class="center">
						<div class="btn-group">
							<?php echo JHtml::_('jgrid.published', $item->published, $i, 'flats.', $canChange, 'cb', '', ''); ?>
							<?php
							// Create dropdown items
							$action = $archived ? 'unarchive' : 'archive';
							JHtml::_('actionsdropdown.' . $action, 'cb' . $i, 'flats');

							$action = $trashed ? 'untrash' : 'trash';
							JHtml::_('actionsdropdown.' . $action, 'cb' . $i, 'flats');

							// Render dropdown list
							echo JHtml::_('actionsdropdown.render', $this->escape($item->num));
							?>
						</div>
					</td>
					<td class="center hidden-phone" style="width:150px;">
						<?php echo $this->escape($item->category_title); ?>
					</td>
					<td class="center hidden-phone" style="width:150px;">
						<?php echo $this->escape($item->dom); ?>
					</td>
					<td class="center hidden-phone">
						<?php echo number_format($item->price, 0, " ", " "); ?>
					</td>
					<td class="center hidden-phone">
						<?php echo number_format($item->price_2, 0, " ", " "); ?>
					</td>					
					<td class="center hidden-phone">
						<?php echo number_format($item->sqm_price, 0, " ", " "); ?>
					</td>					
					<td class="center hidden-phone">
						<?php echo $item->etazh; ?>
					</td>
					<td class="center hidden-phone">
						<?php echo $item->sekciya; ?>
					</td>					
					<td class="center hidden-phone">
						<?php echo $item->rooms; ?>
					</td>
					<td class="center hidden-phone">
						<?php echo $item->num; ?>
					</td>						
					<td class="center hidden-phone">
						<?php echo $item->s_obch; ?>
					</td>
					<td class="center hidden-phone">
						<?php if($item->akcia=='0'){
							echo "нет";
						}elseif($item->akcia=='1'){ 
							echo "да";
						}
						?>
					</td>
					<td class="center hidden-phone">
						<?php if($item->cian=='0'){
							echo "?";
						}elseif($item->cian=='1'){ 
							echo "да";
						}elseif($item->cian=='2'){ 
							echo "нет";
						}	
						?>/<?php if($item->premium=='0'){
							echo "?";
						}elseif($item->premium=='1'){ 
							echo "да";
						}elseif($item->premium=='2'){ 
							echo "нет";
						}	
						?>
							
					</td>						
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<?php //Load the batch processing form. ?>
		<?php echo $this->loadTemplate('batch'); ?>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
