 <?php
/**
* @version		$Id:kapcsolatok.php  1 2015-05-30 06:28:16Z  $
* @package		Tagnyilvantartas
* @subpackage 	Tables
* @copyright	Copyright (C) 2015, . All rights reserved.
* @license #
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
* Jimtawl TableKapcsolatok class
*
* @package		Tagnyilvantartas
* @subpackage	Tables
*/
class TableKapcsolatok extends JTable
{

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 * @since 1.0
	 */
	public function __construct(& $db) 
	{
		parent::__construct('#__tny_kapcsolatok', 'kapcs_id', $db);
	}

	/**
	* Overloaded bind function
	*
	* @acces public
	* @param array $hash named array
	* @return null|string	null is operation was satisfactory, otherwise returns an error
	* @see JTable:bind
	* @since 1.5
	*/
	public function bind($array, $ignore = '')
	{ 
		
		return parent::bind($array, $ignore);		
	}

	/**
	 * Overloaded check method to ensure data integrity
	 *
	 * @access public
	 * @return boolean True on success
	 * @since 1.0
	 */
	public function check()
	{



		/** check for valid name */
		/**
		if (trim($this->lastaction) == '') {
			$this->setError(JText::_('Your Kapcsolatok must contain a lastaction.')); 
			return false;
		}
		**/		

		return true;
	}
}
 