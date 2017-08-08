<?php
/**
 * @version		$Id:extensions.php 1 2015-05-30Z  $
 * @author	   	
 * @package    Tagnyilvantartas
 * @subpackage Controllers
 * @copyright  	Copyright (C) 2015, . All rights reserved.
 * @license 
 */
defined('_JEXEC') or die;


require_once (JPATH_ADMINISTRATOR.'/components/com_tagnyilvantartas/helpers/tagnyilvantartas.php' );

class JElementExtensions extends JElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	var	$_name = 'Extensions';

	function fetchElement($name, $value, &$node, $control_name)
	{
	
		$extensions = TagnyilvantartasHelper::getExtensions();
		$options = array();
		foreach ($extensions as $extension) {   
		
			$option = new stdClass();
			$option->text = JText::_(ucfirst((string) $extension->name));
			$option->value = (string) $extension->name;
			$options[] = $option;
			
		}		
		
		return JHTML::_('select.genericlist', $options, ''.$control_name.'['.$name.']', 'class="inputbox"', 'value', 'text', $value, $control_name.$name );
	}
}