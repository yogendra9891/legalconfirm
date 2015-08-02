<?php
JHtml::_('behavior.modal');
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
jimport( 'zest.html.grid' );
//getting in view.html file of same view
$lastrequestlawfirms = array();
foreach (@$this->alreadySelectedLawfirm as $newarray)//this is for the last request lawfirms
{
 $lastrequestlawfirms[] = $newarray->lawfirmid;
}

$nowselectedlawfirmids = array();
foreach($this->requestdata as $aarray)
{
	$nowselectedlawfirmids[] = $aarray['lawfirm'];
}
$user		= JFactory::getUser();
?>

<?php 
			 $k = 0;
			 if(count( $this->items ))
			  for ($i=0, $n=count( $this->items ); $i < $n; $i++)
   				 {
			  $row =& $this->items[$i];
			  //$checked commented by yogendra because we wants radio button selection....
			  // for this added a new library by yogendra in libraries/zest/html/grid.php
	    	  $checked = JHTML::_('grid.id', $i, $row->lawfirmid );
			 //$checked = JHTMLGridZest::id( $i, $row->lawfirmid );
            //$checked = '<input type="radio" id="cb'.$i.'" name="cid[]" value="'.$row->lawfirmid.'" />';
	       //$link = JRoute::_( 'index.php?option=com_legalconfirm&view=clientprofile&id='. (int)$row->id );
		     $ordering	= ($this->state->get('list.ordering') == 'a.ordering');
		?>
		
	<tr class="row<?php echo $i % 2; ?>" id="tobeselectedlawfirms">
		<td width="10%"><?php if(in_array($row->lawfirmid, $lastrequestlawfirms) || in_array($row->lawfirmid, $nowselectedlawfirmids))
		$checkedtrue = "checked=true";
		else
		$checkedtrue = "";
        ?>
			<input type="checkbox" class="nowtobeselected" title="JGRID_CHECKBOX_ROW_N" <?php echo $checkedtrue; ?> value="<?php echo $row->lawfirmid;?>" name="cid[]" id="cb" />
		</td>
		<td width="55%">
			<?php echo $row->accounting_firm; ?>
		</td>
			
		<td width="35%">
			<?php 
			//if(strlen($row->email) > 20)
			//echo substr($row->email, 0,20).'..'; 
			//else
			//echo $row->email; 
			?>
		</td>
		
	</tr>
	<?php
 		$k = 1 - $k; 
		} else{?> 
		<tr><td style="text-align: left; padding: 5px;">No Lawfirm available.</td></tr>
		<?php }?>

