<?php

defined('_JEXEC') or die;

class FlatsViewFlat extends JViewLegacy
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
		$canDo		= JHelperContent::getActions('com_flats', 'category', $this->item->catid);

		JToolbarHelper::title(JText::_('Вопрос'), 'feed flats');

		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit') || count($user->getAuthorisedCategories('com_flats', 'core.create')) > 0))
		{
			JToolbarHelper::apply('flat.apply');
			JToolbarHelper::save('flat.save');
		}
		if (!$checkedOut && count($user->getAuthorisedCategories('com_flats', 'core.create')) > 0)
		{
			JToolbarHelper::save2new('flat.save2new');
		}
		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create'))
		{
			JToolbarHelper::save2copy('flat.save2copy');
		}

		if (empty($this->item->id))
		{
			JToolbarHelper::cancel('flat.cancel');
		}
		else
		{
			/*if ($this->state->params->get('save_history', 0) && $user->authorise('core.edit'))
			{
				JToolbarHelper::versions('com_flats.flat', $this->item->id);
			}*/

			JToolbarHelper::cancel('flat.cancel', 'JTOOLBAR_CLOSE');
		}

		JToolbarHelper::divider();
		JToolbarHelper::help('JHELP_COMPONENTS_FLATS_FEEDS_EDIT');
	}
}
