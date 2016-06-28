<?php

defined('_JEXEC') or die;

class StaffsViewStaff extends JViewLegacy
{
	protected $item;

	protected $form;

	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		if ($this->getLayout() == 'modal')
		{
			$this->form->setFieldAttribute('language', 'readonly', 'true');
			$this->form->setFieldAttribute('catid', 'readonly', 'true');
		}

		$this->addToolbar();
		parent::display($tpl);
	}


	protected function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));

		// Since we don't track these assets at the item level, use the category id.
		$canDo		= JHelperContent::getActions('com_staffs', 'category', $this->item->catid);

		JToolbarHelper::title(JText::_('Информация о сотруднике'), 'feed staffs');

		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit') || count($user->getAuthorisedCategories('com_staffs', 'core.create')) > 0))
		{
			JToolbarHelper::apply('staff.apply');
			JToolbarHelper::save('staff.save');
		}
		if (!$checkedOut && count($user->getAuthorisedCategories('com_staffs', 'core.create')) > 0)
		{
			JToolbarHelper::save2new('staff.save2new');
		}
		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create'))
		{
			JToolbarHelper::save2copy('staff.save2copy');
		}

		if (empty($this->item->id))
		{
			JToolbarHelper::cancel('staff.cancel');
		}
		else
		{
			/*if ($this->state->params->get('save_history', 0) && $user->authorise('core.edit'))
			{
				JToolbarHelper::versions('com_staffs.staff', $this->item->id);
			}*/

			JToolbarHelper::cancel('staff.cancel', 'JTOOLBAR_CLOSE');
		}

		JToolbarHelper::divider();
		JToolbarHelper::help('JHELP_COMPONENTS_STAFFS_FEEDS_EDIT');
	}
}
