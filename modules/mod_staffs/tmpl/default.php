<?php
defined('_JEXEC') or die;
$type = $params->get('type');
?>
<?php if(($type=='1')or($type=='2')){ ?>
	<?php $images = json_decode($item->images); ?>
	<img src="<?php echo $images->image_first;?>" alt="<?php echo $images->image_first_alt;?>">
	<div class="bot_line">
		<?php echo $item->last_name;?><br/>
		<?php echo $item->first_name;?> <?php echo $item->second_name;?>
	</div>
<?php }else{ ?>
	<?php foreach($items as $staff){ ?>
		<?php $images = json_decode($staff->images); ?>
		<div class="staff_mod">
			<img src="<?php echo $images->image_first;?>" alt="<?php echo $images->image_first_alt;?>">
			<div class="bot_line">
				<div class="name">
					<?php echo $staff->last_name;?><br/>
					<?php echo $staff->first_name;?> <?php echo $staff->second_name;?>
				</div>
				<div class="job_title">
					<?php echo $staff->job_title;?>
				</div>
			</div>
		</div>
	<?php } ?>
<?php } ?>