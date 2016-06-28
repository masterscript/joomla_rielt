<?php
defined('_JEXEC') or die;

class FlatsControllerFlats extends JControllerAdmin
{
	public function getModel($name = 'Flat', $prefix = 'FlatsModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	protected function postDeleteHook(JModelLegacy $model, $ids = null){
		$db	= JFactory::getDbo();
		jimport('joomla.filesystem.folder');
		
		foreach($ids as $key=>$value){
			
		}
	}
}
