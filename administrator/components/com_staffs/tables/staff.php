<?php

defined('_JEXEC') or die;

class StaffsTableStaff extends JTable
{

	protected $_jsonEncode = array('params', 'metadata', 'images');

	public function __construct(&$db)
	{
		parent::__construct('#__staffs', 'id', $db);

		JTableObserverTags::createObserver($this, array('typeAlias' => 'com_staffs.staff'));
		JTableObserverContenthistory::createObserver($this, array('typeAlias' => 'com_staffs.staff'));
	}

	public function check()
	{
		// Check for valid name.
		if (trim($this->last_name) == '')
		{
			$this->setError(JText::_('COM_STAFFS_WARNING_PROVIDE_VALID_NAME'));
			return false;
		}

		if (empty($this->alias)){
			$this->alias = $this->last_name.'_'.$this->email.'_'.$this->created;
		}
		$this->alias = JApplication::stringURLSafe($this->alias);
		if (trim(str_replace('-', '', $this->alias)) == '')
		{
			$this->alias = JFactory::getDate()->format("Y-m-d-H-i-s");
		}

		// Check the publish down date is not earlier than publish up.
		if ((int) $this->publish_down > 0 && $this->publish_down < $this->publish_up)
		{
			$this->setError(JText::_('JGLOBAL_START_PUBLISH_AFTER_FINISH'));
			return false;
		}

		// clean up keywords -- eliminate extra spaces between phrases
		// and cr (\r) and lf (\n) characters from string
		if (!empty($this->metakey))
		{
			// only process if not empty
			$bad_characters = array("\n", "\r", "\"", "<", ">"); // array of characters to remove
			$after_clean = JString::str_ireplace($bad_characters, "", $this->metakey); // remove bad characters
			$keys = explode(',', $after_clean); // create array using commas as delimiter
			$clean_keys = array();

			foreach ($keys as $key)
			{
				if (trim($key)) {  // ignore blank keywords
					$clean_keys[] = trim($key);
				}
			}
			$this->metakey = implode(", ", $clean_keys); // put array back together delimited by ", "
		}

		// clean up description -- eliminate quotes and <> brackets
		if (!empty($this->metadesc))
		{
			// only process if not empty
			$bad_characters = array("\"", "<", ">");
			$this->metadesc = JString::str_ireplace($bad_characters, "", $this->metadesc);
		}

		return true;
	}


	public function store($updateNulls = false)
	{
		$date	= JFactory::getDate();
		$user	= JFactory::getUser();
		if ($this->id)
		{
			// Existing item
			$this->modified		= $date->toSql();
			$this->modified_by	= $user->get('id');
		}
		else
		{
			// New staff. A feed created and created_by field can be set by the user,
			// so we don't touch either of these if they are set.
			if (!(int) $this->created)
			{
				$this->created = $date->toSql();
			}
			if (empty($this->created_by))
			{
				$this->created_by = $user->get('id');
			}
		}
		// Verify that the alias is unique
		$table = JTable::getInstance('Staff', 'StaffsTable');
		if ($table->load(array('alias' => $this->alias, 'catid' => $this->catid)) && ($table->id != $this->id || $this->id == 0))
		{
			$this->setError(JText::_('COM_STAFFS_ERROR_UNIQUE_ALIAS'));
			return false;
		}

		// Save links as punycode.
		//$this->link = JStringPunycode::urlToPunycode($this->link);

		return parent::store($updateNulls);
	}
}
