<?php
/**
 *
 * @author			Allan <allan@awynesoft.com>
 * @link			http://www.awynesoft.com
 * @copyright		Copyright (C) 2012 AwyneSoft.com All Rights Reserved
 * @license			GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version			$Id: default.php 37 2012-09-25 15:00:56Z allan $
 */
// No direct access.
defined('_JEXEC') or die;
$plugins = modEasyQuickIconsHelper::plugins();

$html = JHtml::_('icons.buttons', $buttons);

$categories = EasyquickiconsHelper::eqiCategory();
$categoryCnt = count($categories);

?>
<div class="cpanel">
<?php 
if($categoryCnt > 1){
	echo JHtml::_('tabs.start', 'Tabs');
	for($i = 0; $i < $categoryCnt; $i++){
		echo JHtml::_('tabs.panel', $categories[$i]->category, $i);
		$category[$i] = array();
		$category2[$i] = array();
		
		foreach($buttons as $pos => $button){

			if($button['category'] == $categories[$i]->category){
				$category[$i][] = $button;
				
			}
		}

		foreach($plugins as $a => $icon){
			if($icon['category'] == $categories[$i]->category){
				$category2[$i][] = $icon;	
			}		
		}
		$allIcons = array_merge($category[$i], $category2[$i]);
		
		$html = JHtml::_('icons.buttons', $allIcons);
		
		if(!empty($html)){
			
			echo $html;
			
		} else {
			echo JText::_('MOD_EASYQUICKICONS_NO_ASSIGNED');
		}
	}
	echo JHtml::_('tabs.end');
} else {

	echo $html;

} ?>
</div>
