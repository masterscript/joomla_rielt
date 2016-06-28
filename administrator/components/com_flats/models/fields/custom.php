<?php
defined('JPATH_PLATFORM') or die;

class JFormFieldCustom extends JFormField
{
	protected $type = 'Custom';
	protected static $initialised = false;

        public function __construct($form = null)        
        {
            
            parent::__construct($form);
        }
       
	protected function getInput()
	{
		$field = $this->element['name'];
		return $this->form->getValue($field); 
	}
        
        /**
         * Сформировать список опций для списка изображений
         * 
         * @return array
         */
        public function getOptions()
        {
            $options = array();
            foreach ($this->value as $value) {
                $value = explode('|', $value, 2);
                $options[] = JHtml::_('select.option', $value[0], @$value[1] ? $value[1] : $value[0]);
            }
            return $options;
        }        
}
