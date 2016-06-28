<?php 
defined('_JEXEC') or die;

$catid = $this->category->id;

if(isset($_GET['dom'])){
	echo $this->loadTemplate('dom');	
}else{
	echo $this->loadTemplate('flat'.$catid);
}
	
