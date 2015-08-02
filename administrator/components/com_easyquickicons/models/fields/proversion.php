<?php
/**
 *
 * @package			Easy QuickIcons
 * @version			$Id: proversion.php 58 2012-10-22 16:05:35Z allan $
 *
 * @author			Allan <allan@awynesoft.com>
 * @link			http://www.awynesoft.com
 * @copyright		Copyright (C) 2012 AwyneSoft.com All Rights Reserved
 * @license			GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

class JFormFieldProversion extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Proversion';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		$class = $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';
		$html = '<div '.$class.'>'.JText::_($this->element['value']).'</div>';
		
		return $html;
	}
}
