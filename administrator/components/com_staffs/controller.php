<?php
defined('_JEXEC') or die;
class StaffsController extends JControllerLegacy
{
	public function display($cachable = false, $urlparams = false)
	{
		require_once JPATH_COMPONENT.'/helpers/staffs.php';

		$view   = $this->input->get('view', 'staffs');
		$layout = $this->input->get('layout', 'default');
		$id     = $this->input->getInt('id');

		// Check for edit form.
		if ($view == 'staff' && $layout == 'edit' && !$this->checkEditId('com_staffs.edit.staff', $id))
		{
			// Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_staffs&view=staffs', false));

			return false;
		}

		parent::display();
	}
}
