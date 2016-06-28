<?php
defined('_JEXEC') or die;
require_once __DIR__ . '/helper.php';
$item	= ModStaffsHelper::getStaff($params);
$items	= ModStaffsHelper::getStaffAll($params);

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

require JModuleHelper::getLayoutPath('mod_staffs', $params->get('layout', 'default'));
