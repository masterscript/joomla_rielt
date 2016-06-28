<?php
defined('_JEXEC') or die;
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');

if ($this->category->id==13){//портфолио
	echo $this->loadTemplate('default');
}