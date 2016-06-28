<?php
defined('_JEXEC') or die;
require_once JPATH_ADMINISTRATOR.'/components/com_flats/models/flat.php';
class FlatsModelForm extends FlatsModelFlat{
	public $typeAlias = 'com_flats.flat';
	protected function populateState(){
		$app = JFactory::getApplication();

		// Load state from the request.
		$pk = $app->input->getInt('a_id');
		$this->setState('flat.id', $pk);

		$this->setState('flat.catid', $app->input->getInt('catid'));

		$return = $app->input->get('return', null, 'base64');
		$this->setState('return_page', base64_decode($return));

		// Load the parameters.
		$params	= $app->getParams();
		$this->setState('params', $params);

		$this->setState('layout', $app->input->getString('layout'));
	}

	public function getItem($itemId = null)	{
		$itemId = (int) (!empty($itemId)) ? $itemId : $this->getState('flat.id');
		$table = $this->getTable();
		$return = $table->load($itemId);
		if ($return === false && $table->getError())		{
			$this->setError($table->getError());
			return false;
		}

		$properties = $table->getProperties(1);
		$value = JArrayHelper::toObject($properties, 'JObject');

		

		// Compute selected asset permissions.
		$user	= JFactory::getUser();
		$userId	= $user->get('id');
		$asset	= 'com_flats.flat.' . $value->id;

		

		

		// Convert the metadata field to an array.
		$registry = new JRegistry;
		$registry->loadString($value->metadata);
		$value->metadata = $registry->toArray();
		
		return $value;
	}
	public function getReturnPage()
	{
		return base64_encode($this->getState('return_page'));
	}

	public function save($data)
	{
		// Associations are not edited in frontend ATM so we have to inherit them
		if (JLanguageAssociations::isEnabled() && !empty($data['id']))
		{
			if ($associations = JLanguageAssociations::getAssociations('com_flats', '#__flats', 'com_flats.flat', $data['id']))
			{
				foreach ($associations as $tag => $associated)
				{
					$associations[$tag] = (int) $associated->id;
				}

				$data['associations'] = $associations;
			}
		}

		return parent::save($data);
	}
}
