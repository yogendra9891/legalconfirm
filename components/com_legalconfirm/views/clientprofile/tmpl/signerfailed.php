<?php
/**
 * @version     1.0.0
 * @package     com_legalconfirm
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Abhishek Gupta <abhishek.gupta@daffodilsw.com> - http://
 */
// no direct access
defined('_JEXEC') or die;
$clientid = JRequest::getVar('id');
$signerfailedurl = JURI::base().'index.php?option=com_legalconfirm&task=clientprofile.redirectsigner&id='.$clientid; 
?>
<input type="hidden" name="test" id="test" value="<?php echo $signerfailedurl;?>" >
<script type="text/javascript">
var jq = jQuery.noConflict();
jq(document).ready(function(){
  var test = jq('#test').val();
  window.parent.location = test;
  window.parent.SqueezeBox.close();
});
</script>

