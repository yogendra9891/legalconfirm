<?php
/**
 * @author			Allan <allan@awynesoft.com>
 * @link			http://www.awynesoft.com
 * @copyright		Copyright (C) 2012 AwyneSoft.com All Rights Reserved
 * @license			GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version			$Id: helper.php 98 2012-10-27 14:08:51Z allan $
 */

// No direct access.
defined('_JEXEC') or die;

JLoader::register('EasyquickiconsHelper', '../administrator/components/com_easyquickicons/helpers/easyquickicons.php');

/**
 * @package		Joomla.Administrator
 * @subpackage	mod_easyquickicons
 * @since		1.6
 */
abstract class modEasyQuickIconsHelper
{
	/**
	 * Stack to hold buttons
	 *
	 * @since	1.6
	 */
	protected static $buttons = array();
	
	protected static $plugins = array();
	/**
	 * Helper method to return button list.
	 *
	 * This method returns the array by reference so it can be
	 * used to add custom buttons or remove default ones.
	 *
	 * @param	JRegistry	The module parameters.
	 *
	 * @return	array	An array of buttons
	 * @since	1.6
	 */
	public static function &getButtons($params)
	{
	
		self::$buttons = array();
		
		$context = $params->get('context', 'mod_easyquickicons');
		if ($context == 'mod_easyquickicons'){
			// Load mod_easyquickicons language file in case this method is called before rendering the module
			JFactory::getLanguage()->load('mod_easyquickicons');
			
			$template = JFactory::getApplication()->getTemplate();
			//load the icons from the db
			$rows = EasyquickiconsHelper::eqiItems();
			
			$quickicons = array();
			foreach($rows as $i => $row){
				
				// check layout and task links 
				$link = EasyquickiconsHelper::eqiCheckLink($row->link);
				
				$getAccess = EasyquickiconsHelper::eqiComponentName($row->id);

				$quickicons[$i]['category'] = $row->category;
				
				if( $row->name == 'Edit Profile' AND $row->category == EasyQuickIconsHelper::standardCategory()){
					$quickicons[$i]['link'] = JRoute::_('index.php?option=com_admin&task=profile.edit&id='.JFactory::getUser()->id); 
					$quickicons[$i]['access'] = true;
				} else {
					
					$quickicons[$i]['link'] = $link;
					
					if(!is_numeric($getAccess)){

						$quickicons[$i]['access'] = array('core.manage', $getAccess);
					}
				}

				$quickicons[$i]['image'] = EasyquickiconsHelper::eqiImage($row->id, 1);
				$quickicons[$i]['text'] = JText::_($row->name);
				$quickicons[$i]['target'] = JText::_(trim($row->target));
								
				self::$buttons[$i] = $quickicons[$i];
			}
		} else {
			self::$buttons = array();
		}
		return self::$buttons;
	}
	/*Load Joomla quickicon plugins*/
	public static function plugins(){

		JPluginHelper::importPlugin('quickicon');
		$app = JFactory::getApplication();
		$pluginIcons = array();	
		
		//set context to "mod_quickicon" to render plugin icons
		
		$pluginArray = (array) $app->triggerEvent('onGetIcons', array('mod_quickicon'));

		if (!empty($pluginArray)) {

			foreach ($pluginArray as $plugin) {

				foreach ($plugin as $icon) {

					$pluginIcon['id'] = $icon['id'];
					$pluginIcon['link'] = $icon['link'];
					$pluginIcon['image'] = $icon['image'];
					$pluginIcon['text'] = $icon['text'];
					$pluginIcon['category'] = EasyQuickIconsHelper::standardCategory();
					$pluginIcon['target'] = '_self';
		
					$pluginIcons[] = $pluginIcon;
		
				}
		
			}
			self::$plugins = $pluginIcons;

		} else {

			self::$plugins = array();

		}
		return self::$plugins;
	}
	/**
	 * Get the alternate title for the module
	 *
	 * @param	JRegistry	The module parameters.
	 * @param	object		The module.
	 *
	 * @return	string	The alternate title for the module.
	 */
	public static function getTitle($params, $module)
	{
		$key = $params->get('context', 'mod_easyquickicons') . '_title';
		if (JFactory::getLanguage()->hasKey($key))
		{
			return JText::_($key);
		}
		else
		{
			return $module->title;
		}
	}
}