<?php
/**
 * @version		$Id: extensions.html.php 147 2013-10-06 08:58:34Z michel $
 * @copyright	Copyright (C) 2015, . All rights reserved.
 * @license # 
 */
defined('JPATH_BASE') or die;

require_once (JPATH_ADMINISTRATOR.'/components/com_tagnyilvantartas/helpers/tagnyilvantartas.php' );

jimport('joomla.html.html');

JFormHelper::loadFieldClass('list');

class JFormFieldExtensions extends JFormFieldList
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 'Extensions';
	
	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array		An array of JHtml options.
	 */
	protected function getOptions()
	{
		$class		= ($v = $this->element['class']) ? 'class="'.$v.'"' : 'class="inputbox"';
			
		$extensions = TagnyilvantartasHelper::getExtensions();
		$options = array();
		
		foreach ($extensions as $extension) {			   
			$listview = is_object($extension->listview) ? $extension->listview->__toString() : $extension->listview;
			$name = is_object($extension->name) ? $extension->name->__toString() : $extension->name;
			$option = new stdClass();
			$option->text = JText::_(ucfirst($listview));
			$option->value = 'com_tagnyilvantartas.'.$name ;
			$options[] = $option;
			
		}				
		$options	= array_merge(
				parent::getOptions(),
				$options
		);
		return $options;
	}
}
?>