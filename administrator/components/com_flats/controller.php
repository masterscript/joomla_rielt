<?php
defined('_JEXEC') or die;
class FlatsController extends JControllerLegacy
{
	public function display($cachable = false, $urlparams = false)
	{
		require_once JPATH_COMPONENT.'/helpers/flats.php';

		$view   = $this->input->get('view', 'flats');
		$layout = $this->input->get('layout', 'default');
		$id     = $this->input->getInt('id');

		// Check for edit form.
		if ($view == 'flat' && $layout == 'edit' && !$this->checkEditId('com_flats.edit.flat', $id))
		{
			// Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_flats&view=flats', false));

			return false;
		}
		if ($view == 'answer' && $layout == 'edit' && !$this->checkEditId('com_flats.edit.answer', $id))
		{
			// Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_flats&view=answers', false));

			return false;
		}

		parent::display();
	}
}
