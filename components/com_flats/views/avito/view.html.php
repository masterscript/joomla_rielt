<?php
// No direct access to this file
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.view');
class FlatsViewAvito extends JViewLegacy{
	function display($cachable = false, $urlparams = false){
		$model = $this->getModel();
		$detail = $model->getyandex();
	}
}
?>