<?php
defined('_JEXEC') or die;

class StaffsControllerStaffs extends JControllerAdmin
{
	public function getModel($name = 'Staff', $prefix = 'StaffsModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	protected function postDeleteHook(JModelLegacy $model, $ids = null)
	{
	}
}
