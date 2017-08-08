<?php
/**
 * @version		$Id: #component#.php 170 2013-11-12 22:44:37Z michel $
 * @package		Joomla.Framework
 * @subpackage		HTML
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class TagnyilvantartasHelper
{
	
	/*
	 * Submenu for Joomla 3.x
	 */
	public static function addSubmenu($vName = 'cimkeks')
	{
        	if(version_compare(JVERSION,'3','<')){
		JSubMenuHelper::addEntry(
			JText::_('Cimkeks'),
			'index.php?option=com_tagnyilvantartas&view=cimkeks',
			($vName == 'cimkeks')
		);	
	} else {
		JHtmlSidebar::addEntry(
			JText::_('Cimkeks'),
			'index.php?option=com_tagnyilvantartas&view=cimkeks',
			($vName == 'cimkeks')
		);	
	}
	if(version_compare(JVERSION,'3','<')){
		JSubMenuHelper::addEntry(
			JText::_('Felhcsoportoks'),
			'index.php?option=com_tagnyilvantartas&view=felhcsoportoks',
			($vName == 'felhcsoportoks')
		);	
	} else {
		JHtmlSidebar::addEntry(
			JText::_('Felhcsoportoks'),
			'index.php?option=com_tagnyilvantartas&view=felhcsoportoks',
			($vName == 'felhcsoportoks')
		);	
	}
	if(version_compare(JVERSION,'3','<')){
		JSubMenuHelper::addEntry(
			JText::_('Kapcsolatoks'),
			'index.php?option=com_tagnyilvantartas&view=kapcsolatoks',
			($vName == 'kapcsolatoks')
		);	
	} else {
		JHtmlSidebar::addEntry(
			JText::_('Kapcsolatoks'),
			'index.php?option=com_tagnyilvantartas&view=kapcsolatoks',
			($vName == 'kapcsolatoks')
		);	
	}
	if(version_compare(JVERSION,'3','<')){
		JSubMenuHelper::addEntry(
			JText::_('Kategoriaks'),
			'index.php?option=com_tagnyilvantartas&view=kategoriaks',
			($vName == 'kategoriaks')
		);	
	} else {
		JHtmlSidebar::addEntry(
			JText::_('Kategoriaks'),
			'index.php?option=com_tagnyilvantartas&view=kategoriaks',
			($vName == 'kategoriaks')
		);	
	}
	if(version_compare(JVERSION,'3','<')){
		JSubMenuHelper::addEntry(
			JText::_('Teruletiszervezeteks'),
			'index.php?option=com_tagnyilvantartas&view=teruletiszervezeteks',
			($vName == 'teruletiszervezeteks')
		);	
	} else {
		JHtmlSidebar::addEntry(
			JText::_('Teruletiszervezeteks'),
			'index.php?option=com_tagnyilvantartas&view=teruletiszervezeteks',
			($vName == 'teruletiszervezeteks')
		);	
	}

	}
	
	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param   integer  The category ID.
	 *
	 * @return  JObject
	 * @since   1.6
	 */
	public static function getActions($categoryId = 0)
	{
		$user	= JFactory::getUser();
		$result	= new JObject;
	
		if (empty($categoryId))
		{
			$assetName = 'com_tagnyilvantartas';
			$level = 'component';
		}
		else
		{
			$assetName = 'com_tagnyilvantartas.category.'.(int) $categoryId;
			$level = 'category';
		}
	
		$actions = JAccess::getActions('com_tagnyilvantartas', $level);
	
		foreach ($actions as $action)
		{
			$result->set($action->name,	$user->authorise($action->name, $assetName));
		}
	
		return $result;
	}	
	/**
	 * 
	 * Get the Extensions for Categories
	 */
	public static function getExtensions() 
	{
						
		static $extensions;
		
		if(!empty($extensions )) return $extensions;
		
		jimport('joomla.utilities.xmlelement');
		
		$xml = simplexml_load_file(JPATH_ADMINISTRATOR.'/components/com_tagnyilvantartas/elements/extensions.xml', 'JXMLElement');		        
		$elements = $xml->xpath('extensions');
		$extensions = $xml->extensions->xpath('descendant-or-self::extension');
		
		return $extensions;
	} 	

	
	/**
	 *
	 * Returns views that associated with categories
	 */
	public static function getCategoryViews()
	{
	
		$extensions = self::getExtensions();
		$views = array();
		foreach($extensions as $extension ) {
			$views[$extension->listview->__toString()] = 'com_tagnyilvantartas.'.$extension->name->__toString();
		}
		return $views;
	}	
}

/**
 * Utility class for categories
 *
 * @static
 * @package 	Joomla.Framework
 * @subpackage	HTML
 * @since		1.5
 */
abstract class JHtmlTagnyilvantartas
{
	/**
	 * @var	array	Cached array of the category items.
	 */
	protected static $items = array();
	
	/**
	 * Returns the options for extensions list
	 * 
	 * @param string $ext - the extension
	 */
	public static function extensions($ext) 
	{
		$extensions = TagnyilvantartasHelper::getExtensions();
		$options = array();
		
		foreach ($extensions as $extension) {   
		
			$option = new stdClass();
			$option->text = JText::_(ucfirst($extension->name));
			$option->value = 'com_tagnyilvantartas.'.$extension->name;
			$options[] = $option;			
		}		
		return JHtml::_('select.options', $options, 'value', 'text', $ext, true);
	}
	
	/**
	 * Returns an array of categories for the given extension.
	 *
	 * @param	string	The extension option.
	 * @param	array	An array of configuration options. By default, only published and unpulbished categories are returned.
	 *
	 * @return	array
	 */
	public static function categories($extension, $cat_id,$name="categories",$title="Select Category", $config = array('attributes'=>'class="inputbox"','filter.published' => array(0,1)))
	{

			$config	= (array) $config;
			$db		= JFactory::getDbo();

			$query = $db->getQuery(true);

			$query->select('a.id, a.title, a.level');
			$query->from('#__tagnyilvantartas_categories AS a');
			$query->where('a.parent_id > 0');

			// Filter on extension.
			if($extension)
			    $query->where('extension = '.$db->quote($extension));
			
			$attributes = "";
			
			if (isset($config['attributes'])) {
				$attributes = $config['attributes'];
			}
			
			// Filter on the published state
			if (isset($config['filter.published'])) {
				
				if (is_numeric($config['filter.published'])) {
					
					$query->where('a.published = '.(int) $config['filter.published']);
					
				} else if (is_array($config['filter.published'])) {
					
					JArrayHelper::toInteger($config['filter.published']);
					$query->where('a.published IN ('.implode(',', $config['filter.published']).')');
					
				}
			}

			$query->order('a.lft');

			$db->setQuery($query);
			$items = $db->loadObjectList();
			
			// Assemble the list options.
			self::$items = array();
			self::$items[] = JHtml::_('select.option', '', JText::_($title));
			foreach ($items as &$item) {
								
				$item->title = str_repeat('- ', $item->level - 1).$item->title;
				self::$items[] = JHtml::_('select.option', $item->id, $item->title);
			}

		return  JHtml::_('select.genericlist', self::$items, $name, $attributes, 'value', 'text', $cat_id, $name);
		//return self::$items;
	}
}