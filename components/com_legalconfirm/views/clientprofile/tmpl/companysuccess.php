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
$signerid = JRequest::getVar('resultid');
$message = '';
if($signerid){
	$message = 'success';
}else{
	$message = 'unsuccess'; 
} 
?>
<input type="hidden" name="test" id="test" value="<?php echo $message;?>" >
<script type="text/javascript">
var jq = jQuery.noConflict();
jq(document).ready(function(){
  var test = jq('#test').val();
  window.parent.location = window.top.location.href+'&companyeditmessage='+test;
  window.parent.SqueezeBox.close();
});
</script>

