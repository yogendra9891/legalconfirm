<link
	rel="stylesheet"
	href="administrator/templates/bluestork/css/template.css"
	type="text/css" />
<!-- edit externally because in the editor we need the default css -->
<link
	rel="stylesheet" href="templates/legalconfirm/css/style.css"
	type="text/css" />
<div class="register-box">
<?php
//echo "<pre>";
//print_r($this->item);
//die;
echo html_entity_decode($this->item->template);

$date = date("d/M/Y h:i:s", strtotime($this->item->responsedate)) ;

?>
<div style="margin-top:10px;">
<p><b>Signer Info</b></p>
<p>Title: <?php echo $this->item->signertitle;?></p>
<p>Name: <?php echo $this->item->fname." ".$this->item->lname;?></p>
<p>Email: <?php echo $this->item->email;?></p>
<p>Company Name: <?php echo $this->item->company;?></p>
<p>Date and time accepted by signer: <?php echo $date;?></p>
</div>

</div>
<div class="save-pdf-wrapper">
<a id="save-pdf-button" class="button" href="<?php echo JRoute::_('index.php?option=com_lawfirm&task=lawfirmemp.generatepdf&id=' . (int)$this->item->id); ?>" target="_blank"><?php echo "Save as PDF";?></a>
</div>
