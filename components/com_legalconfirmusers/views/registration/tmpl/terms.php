<?php

?>
<script>
$(document).ready(function(){
$('#testingscroll').slimscroll({
                 height: '320px'
               });
});
</script>
<script>
function validateForm(){
	if($('#tccheckval').attr('checked')) {
     return true;
	}
	else{

$.msgBox({
    title:"Required",
    content:"Please accept the terms and conditions"
});
   // alert('Please accept the terms and conditions');
	}
	return false;
}
</script>
<div class="register-box">
<div class="tc">
<form class="form"
	action="<?php echo JRoute::_('index.php'); ?>"
	method="post" name="tcform" id="tcform" onsubmit="return validateForm('tcform');">
<div class="tccheck" id="testingscroll" style="padding-right:10px;">
<!-- get content for terms and conditions -->
<?php 
$term_content = $this->getTermsContent();
echo $term_content;
?>

</div>
<div class="tctext">
<span><label for="tccheckval">Accept Terms and Conditions</label></span><span class="tccheckbox"><input type="checkbox" value="accept" id="tccheckval" name="tccheckval"/></span>
</div>
<div style="clear:both;">
<p><input type="submit" name="submit" value="Register" class="button"/></p>
</div>
<input type="hidden" name="option" value="com_legalconfirmusers" /> 
<input type="hidden" name="task" value="registration.register" /> 
<?php echo JHtml::_( 'form.token' ); ?>
</form>
</div>
</div>
