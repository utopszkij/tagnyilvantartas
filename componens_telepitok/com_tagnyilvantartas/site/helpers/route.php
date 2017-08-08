<?php
/**
 * @version		$Id: route.php 170 2013-11-12 22:44:37Z michel $
 * @package		Tagnyilvantartas
 * @subpackage	Helpers
 * @copyright	Copyright (C) 2015 Open Source Matters, Inc. All rights reserved.
 * @license		
 */

// no direct access
defined('_JEXEC') or die;

// Component Helper
jimport('joomla.application.component.helper');

jimport('joomla.application.categories');
/**
 * Tagnyilvantartas Component Route Helper
 *
 * @static
 * @package		Tagnyilvantartas
 * @subpackage	Helpers

 */
abstract class TagnyilvantartasHelperRoute
{
	protected static $lookup;
	/**
	 * @param	int	The route of the tagnyilvantartas
	 */
	public static function getTagnyilvantartasRoute($id, $catid)
	{
		$needles = array(
			'cimkek'  => array((int) $id)
		);
		//Create the link
		$link = 'index.php?option=com_tagnyilvantartas&view=cimkek&id='. $id;
		if ($catid > 1) {
			$categories = JCategories::getInstance('Cimkek');
			$category = $categories->get($catid);
			if ($category) {
				$needles['category'] = array_reverse($category->getPath());
				$needles['categories'] = $needles['category'];
				$link .= '&catid='.$catid;
			}
		}

		if ($item = TagnyilvantartasHelperRoute::_findItem($needles)) {
			$link .= '&Itemid='.$item;
		};

		return $link;
	}


	/**
	 * Returns link to category view  
	 * @param JCategoryNode $catid
	 * @param number $language
	 * @return string
	 */

	public static function getCategoryRoute($catid, $language = 0)
	{
		
		
		if ($catid instanceof JCategoryNode)
		{
			$id = $catid->id;			
			$category = $catid;			 
		}
		else
		{			
			throw new Exception('First parameter must be JCategoryNode');			
		}
	
		$catviews = TagnyilvantartasHelper::getCategoryViews();
		$extensionviews = array_flip($catviews);
		$view = $extensionviews[$category->extension];
		
		if ($id < 1)
		{
			$link = '';
		}
		else
		{
			//Create the link
			$link = 'index.php?option=com_tagnyilvantartas&view='.$view.'&category='.$category->slug;
			
			$needles = array(
					$view => array($id),
					'category' => array($id)
			);
	
			if ($language && $language != "*" && JLanguageMultilang::isEnabled())
			{
				$db		= JFactory::getDbo();
				$query	= $db->getQuery(true)
				->select('a.sef AS sef')
				->select('a.lang_code AS lang_code')
				->from('#__languages AS a');
	
				$db->setQuery($query);
				$langs = $db->loadObjectList();
				foreach ($langs as $lang)
				{
					if ($language == $lang->lang_code)
					{
						$link .= '&lang='.$lang->sef;
						$needles['language'] = $language;
					}
				}
			}
	
			if ($item = self::_findItem($needles,'category'))
			{

				$link .= '&Itemid='.$item;				
			}
			else
			{
				if ($category)
				{
					$catids = array_reverse($category->getPath());
					$needles = array(
							'category' => $catids
					);
					if ($item = self::_findItem($needles,'category'))
					{
						$link .= '&Itemid='.$item;
					}
					elseif ($item = self::_findItem(null, 'category'))
					{
						$link .= '&Itemid='.$item;
					}
				}
			}
		}
		
		return $link;
	}	
	
	protected static function _findItem($needles = null, $identifier = 'id')
	{
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu('site');
		
		$language	= isset($needles['language']) ? $needles['language'] : '*';

		// Prepare the reverse lookup array.
		if (!isset(self::$lookup[$language]))
		{
			self::$lookup[$language] = array();

			$component	= JComponentHelper::getComponent('com_tagnyilvantartas');

			$attributes = array('component_id');
			$values = array($component->id);

			if ($language != '*')
			{
				$attributes[] = 'language';
				$values[] = array($needles['language'], '*');
			}

			$items = $menus->getItems($attributes, $values);
			
			foreach ($items as $item)
			{
				if (isset($item->query) && isset($item->query['view']))
				{
					$view = $item->query['view'];
					if (!isset(self::$lookup[$language][$view]))
					{
						self::$lookup[$language][$view] = array();
					}
					if (isset($item->query[$identifier]))
					{

						// here it will become a bit tricky
						// language != * can override existing entries
						// language == * cannot override existing entries
						if (!isset(self::$lookup[$language][$view][$item->query[$identifier]]) || $item->language != '*')
						{
							if($item->query[$identifier] != 'all') {
								self::$lookup[$language][$view][$item->query[$identifier]] = $item->id;
							}
						}
					}
				}
			}
		}
		
		
		if ($needles)
		{
			foreach ($needles as $view => $ids)
			{
				if (isset(self::$lookup[$language][$view]))
				{
					foreach ($ids as $id)
					{
						if (isset(self::$lookup[$language][$view][(int) $id]))
						{
							if ($id != 'all') {
								return self::$lookup[$language][$view][(int) $id];
							}
						}
					}
				}
			}
		}

		$active = $menus->getActive();
		if ($active && ($active->language == '*' || !JLanguageMultilang::isEnabled()))
		{
			return $active->id;
		}

		// if not found, return language specific home link
		$default = $menus->getDefault($language);
		return !empty($default->id) ? $default->id : null;
	}
}
