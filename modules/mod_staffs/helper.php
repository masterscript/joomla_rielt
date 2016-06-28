<?php
defined('_JEXEC') or die;
$com_path = JPATH_SITE . '/components/com_staffs/';
require_once $com_path . 'helpers/route.php';
class ModStaffsHelper
{
	public static function getStaff($params)
	{
		$db	= JFactory::getDbo();

		$type = $params->get('type', 0);
		$query	= $db->getQuery(true)
			->select('*')
			->from('#__staffs')							
			->where('best_staff = '.$type);
		$db->setQuery($query, 0, 1);
		$staff = $db->loadObject();
		return $staff;
	}
	public static function getStaffAll($params)
	{
		$db	= JFactory::getDbo();
		$type = $params->get('type', 0);
		$staffs = $params->get('staffs', array());
		$dop = "";
		if($type=='3'){
			//$dop = 'and staff_type=1';
			if(count($staffs)>0){
				$dop = 'and staff_type=1 and staff_type=1 and id in ('.implode(',', $staffs).')';
				
			}else{
				$dop = 'and staff_type=1';
			}
		}
		if($type=='4'){
			//$dop = 'and staff_type=1';
			if(count($staffs)>0){
				$dop = 'and id in ('.implode(',', $staffs).')';
				
			}else{
				$dop = '';
			}
		}
		$query	= $db->getQuery(true)
			->select('*')
			->from('#__staffs')			
			->where('published = 1 '.$dop);
		$db->setQuery($query);
		$staffs = $db->loadObjectList();
		return $staffs;
	}
	
}
