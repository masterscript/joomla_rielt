<?php
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');

$app = JFactory::getApplication();
$input = $app->input;
$assoc = JLanguageAssociations::isEnabled();
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'staff.cancel' || document.formvalidator.isValid(document.id('staff-form'))) {
			Joomla.submitform(task, document.getElementById('staff-form'));
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_staffs&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="staff-form" class="form-validate">

	<?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>

	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', empty($this->item->id) ? JText::_('Добавить сотрудника', true) : JText::_('Редактировать информацию', true)); ?>
		<div class="row-fluid">
			<div class="span9">
				<div class="form-vertical">
					<?php echo $this->form->getControlGroup('last_name'); ?>
					<?php echo $this->form->getControlGroup('first_name'); ?>
					<?php echo $this->form->getControlGroup('second_name'); ?>
					<?php echo $this->form->getControlGroup('experience'); ?>
					<?php echo $this->form->getControlGroup('job_title'); ?>
					<?php echo $this->form->getControlGroup('staff_type'); ?>
					<?php echo $this->form->getControlGroup('phone'); ?>
					<?php echo $this->form->getControlGroup('mobile'); ?>
					<?php echo $this->form->getControlGroup('email'); ?>
					<?php echo $this->form->getControlGroup('skype'); ?>
					<?php echo $this->form->getControlGroup('ordering'); ?>
					<?php echo $this->form->getControlGroup('text'); ?>
				</div>
			</div>
			<div class="span3">
				<?php echo $this->form->getControlGroup('best_staff'); ?>
				<?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
				
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
		
	
		
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'publishing', JText::_('JGLOBAL_FIELDSET_PUBLISHING', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span6">
				<?php echo JLayoutHelper::render('joomla.edit.publishingdata', $this); ?>
				<?php echo $this->form->getControlGroup('staff_layout'); ?>
			</div>
			<div class="span6">
				<?php echo JLayoutHelper::render('joomla.edit.metadata', $this); ?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php $this->set('ignore_fieldsets', array('jbasic')); ?>
		<?php echo JLayoutHelper::render('joomla.edit.params', $this); ?>


		<?php if ($assoc) : ?>
			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'associations', JText::_('JGLOBAL_FIELDSET_ASSOCIATIONS', true)); ?>
			<?php echo $this->loadTemplate('associations'); ?>
			<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php endif; ?>

		<?php echo JHtml::_('bootstrap.endTabSet'); ?>
	</div>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
