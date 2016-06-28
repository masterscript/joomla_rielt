<?php
defined('_JEXEC') or die;
class FlatsViewFlats extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
	public function display($tpl = null)
	{
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

		FlatsHelper::addSubmenu('flats');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
			
		$this->addToolbar();
		$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}
	
	
	protected function addToolbar()
	{
		$state	= $this->get('State');
		$canDo	= JHelperContent::getActions('com_flats', 'category', $state->get('filter.category_id'));
		$user	= JFactory::getUser();

		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');
		JToolbarHelper::title(JText::_('Недвижимость в новостройках'), 'feed flats');

		//if (count($user->getAuthorisedCategories('com_flats', 'core.create')) > 0)
		if ($canDo->get('core.create')){ JToolbarHelper::addNew('flat.add'); }
		if ($canDo->get('core.edit')) { JToolbarHelper::editList('flat.edit'); }
		if ($canDo->get('core.edit.state'))	{
			JToolbarHelper::publish('flats.publish', 'JTOOLBAR_PUBLISH', true);
			JToolbarHelper::unpublish('flats.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			JToolbarHelper::archiveList('flats.archive');
		}
		if ($canDo->get('core.admin')) { JToolbarHelper::checkin('flats.checkin'); }
		if ($state->get('filter.published') == -2 && $canDo->get('core.delete')){
			JToolbarHelper::deleteList('', 'flats.delete', 'JTOOLBAR_EMPTY_TRASH');
		} elseif ($canDo->get('core.edit.state')){
			JToolbarHelper::trash('flats.trash');
		}
		

		JHtmlSidebar::setAction('index.php?option=com_flats&view=flats');

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_PUBLISHED'),
			'filter_published',
			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true)
		);

		JHtmlSidebar::addFilter(
			JText::_('Выбор новостройки'),
			'filter_category_id',
			JHtml::_('select.options', JHtml::_('category.options', 'com_flats'), 'value', 'text', $this->state->get('filter.category_id'))
		);
		
		JHtmlSidebar::addFilter(
			JText::_('Выбор акции'),
			'filter_akcia',
			JHtml::_('select.options', array(JHtml::_('select.option', 1, JText::_('Да')), JHtml::_('select.option', 0, JText::_('Нет'))), 'value', 'text', $this->state->get('filter.akcia'))
		);
		
		JHtmlSidebar::addFilter(
			JText::_('Выбор секции'),
			'filter_sekciya',
			JHtml::_('select.options', array(
				JHtml::_('select.option', 1, '1'), 
				JHtml::_('select.option', 2, '2'),
				JHtml::_('select.option', 3, '3'),
				JHtml::_('select.option', 4, '4')
			), 'value', 'text', $this->state->get('filter.sekciya'))
		);
		
		JHtmlSidebar::addFilter(
			JText::_('К-ство комнат'),
			'filter_rooms',
			JHtml::_('select.options', array(
				JHtml::_('select.option', 1, '1'), 
				JHtml::_('select.option', 2, '2'),
				JHtml::_('select.option', 3, '3'),
				JHtml::_('select.option', 4, '4'),
				JHtml::_('select.option', 5, '5'),
				JHtml::_('select.option', 6, '6')				
			), 'value', 'text', $this->state->get('filter.rooms'))
		);
		
		$et = array();
		for($i=1;$i<=30;$i++){
			$et[] = JHtml::_('select.option', $i, $i);
		}
		JHtmlSidebar::addFilter(
			JText::_('Выбор этажа'),
			'filter_etazh',
			JHtml::_('select.options', $et, 'value', 'text', $this->state->get('filter.etazh'))
		);
		
	}

	
	protected function getSortFields()
	{
		return array(			
			'a.published' => JText::_('JSTATUS'),
			'category_title' => JText::_('JCATEGORY'),
			'a.access' => JText::_('JGRID_HEADING_ACCESS'),
			'a.language' => JText::_('JGRID_HEADING_LANGUAGE'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}
