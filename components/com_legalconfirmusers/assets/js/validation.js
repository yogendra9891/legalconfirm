/*function to validate form*/

 function validate(formId)
{
	
	 var flag=true;
	 	for(var i=0;i<formId.elements.length;i++)
	 	{
	 		
	 		
	 		if(formId.elements[i].className=="inputbox required")
	 		{
	 			if(isEmpty(formId.elements[i].value))
	 			{
	 				flag=false;
	 				formId.elements[i].style.border="2px solid red";
	 			}else if(isDefault(formId.elements[i].value))
		 			{
	 					flag=false;
		 				formId.elements[i].style.border="2px solid red";
		 			}else{
	 				formId.elements[i].style.border="1px solid silver";
	 				}
	 		}else if(formId.elements[i].className=="required email1")
			{
	 			if(isEmpty(formId.elements[i].value))
				{
					flag=false;
					formId.elements[i].style.border="2px solid red";
				}else if(!isEmail(formId.elements[i].value)){
						flag=false;
						formId.elements[i].style.border="2px solid red";
						 $('#emailerr1').html('invalid email');
						
				}else{
	 				formId.elements[i].style.border="1px solid silver";
	 				$('#emailerr1').html('');
	 				}
	 			
			}
	 		else if(formId.elements[i].className=="required email2")
			{
	 			if(isEmpty(formId.elements[i].value))
				{
					flag=false;
					formId.elements[i].style.border="2px solid red";
				}else if(!isEmail(formId.elements[i].value)){
						flag=false;
						formId.elements[i].style.border="2px solid red";
						 $('#emailerr2').html('invalid email');
						
				}else{
	 				formId.elements[i].style.border="1px solid silver";
	 				$('#emailerr2').html('');
	 				}
	 			
			}
	 		else if(formId.elements[i].className=="required user")
			{
				if(isEmpty(formId.elements[i].value))
				{
					flag=false;
					formId.elements[i].style.border="2px solid red";
				}else if(isDefault(formId.elements[i].value))
	 			{
					flag=false;
	 				formId.elements[i].style.border="2px solid red";
	 			}else{
					formId.elements[i].style.border="1px solid silver";
				}
				
			}
			else if(formId.elements[i].className=="required photo")
			{
				var fileInsert = document.getElementById('photo').value;
				if(fileInsert != ""){
				  filear = fileInsert.split('.').pop().toLowerCase();
				  if($.inArray(filear, ['gif','png','jpg','jpeg']) == -1) {
					  flag=false;
					  formId.elements[i].style.border="2px solid red";
					  $('#photoerr').html('invalid image');
					}
				  else{
					  formId.elements[i].style.border="1px solid silver";
					  $('#photoerr').html('');
				}
				 
			}
			}
			else if(formId.elements[i].className=="required video")
			{
				var fileInsert = document.getElementById('video').value;
				if(fileInsert != ""){
				  filear = fileInsert.split('.').pop().toLowerCase();
				  if($.inArray(filear, ['mp4','m4v','f4v','mov','webm','flv','ogv']) == -1) {
					  flag=false;
					  formId.elements[i].style.border="2px solid red";
					  $('#videoerr').html('invalid video');
					}
				  else{
					  formId.elements[i].style.border="1px solid silver";
					  $('#videoerr').html('');
				}
				}
				 
			}
			else if(formId.elements[i].className=="category required")
			{
				var catid = document.getElementById('catd').value;
				if(catid == ""){
					flag=false;
	 				formId.elements[i].style.border="2px solid red";
				}else{
					formId.elements[i].style.border="1px solid silver";
				}
					
				 
			}
			else if(formId.elements[i].className=="zip required")
			{
				
				// alert( (/(^\d{5}$)|(^\d{5}-\d{4}$)/).test(formId.elements[i].value));
				if(isEmpty(formId.elements[i].value))
	 			{
	 				flag=false;
	 				formId.elements[i].style.border="2px solid red";
	 				$('.errzip').html('');
	 			}
				else if(!(/(^\d{5}$)|(^\d{5}-\d{4}$)/).test(formId.elements[i].value)){
					flag=false;
					formId.elements[i].style.border="2px solid red";
					$('.errzip').html('Not valid ZIP code');
				}
				
				else{
	 				formId.elements[i].style.border="1px solid silver";
	 				$('.errzip').html('');
	 				}
					
				 
			}
	 		
			else if(formId.elements[i].className=="ccnumber required")
			{
				//credit card link http://www.freeformatter.com/credit-card-number-generator-validator.html
				//for american express card
				var american_regex = /(^(?:3[47][0-9]{13})$)/;
				//alert( (ameican_regex).test(formId.elements[i].value));
				
				//for visa card
				var visa_regex = /^(?:4[0-9]{12}(?:[0-9]{3})?)$/;
				
				//for master card
				var master_regex = /^(?:5[1-5][0-9]{14})$/;
				
				//for Discover Card 
				var discover_regex = /^(?:6(?:011|5[0-9][0-9])[0-9]{12})$/;
				
				//for Dinner clib card
				var dinner_regex = /^(?:3(?:0[0-5]|[68][0-9])[0-9]{11})$/;
				
				//for JCB Card
				var jcb_regex = /^(?:(?:2131|1800|35\d{3})\d{11})$/;  
				
				//for mastero card
				var mastero_regex = /^(311|367|[5-6][0-9][0-9][0-9])\d{8,15}$/;
				
				var cardtype = $('#cc_type').val();
				
				if((american_regex).test(formId.elements[i].value) &&  (cardtype == 'AmericanExpress')){
					formId.elements[i].style.border="1px solid silver";
					$('#errccnumber').html('');
				}
			    else if(visa_regex.test(formId.elements[i].value) && (cardtype == 'visa')){
			    	formId.elements[i].style.border="1px solid silver";
			    	$('#errccnumber').html('');
				}
				else if(master_regex.test(formId.elements[i].value) && (cardtype == 'MasterCard')){
					formId.elements[i].style.border="1px solid silver";
					$('#errccnumber').html('');
				}
				else if(discover_regex.test(formId.elements[i].value) && (cardtype == 'Discover')){
					formId.elements[i].style.border="1px solid silver";
					$('#errccnumber').html('');
				}
				else if(dinner_regex.test(formId.elements[i].value) && (cardtype == 'Diners')){
					formId.elements[i].style.border="1px solid silver";
					$('#errccnumber').html('');
				}
				else if(jcb_regex.test(formId.elements[i].value) && (cardtype == 'JCB')){
					formId.elements[i].style.border="1px solid silver";
					$('#errccnumber').html('');
				}
				else if(mastero_regex.test(formId.elements[i].value) && (cardtype == 'Mastero')){
					formId.elements[i].style.border="1px solid silver";
					$('#errccnumber').html('');
				}
				else{
					flag=false;
					formId.elements[i].style.border="2px solid red";
	 				$('#errccnumber').html('Invalid Credit card number')
				}
				 
			}
	 		//yogendra code start
			else if(formId.elements[i].className=="ccvnumber required")
			{  
				var ccvnoexp = /^[0-9]{3,4}$/;
	 			if(isEmpty(formId.elements[i].value))
				{
					flag=false;
					formId.elements[i].style.border="2px solid red";
				}else if(!(ccvnoexp).test(formId.elements[i].value))
				{
					flag=false;
					formId.elements[i].style.border="2px solid red";
					$('#errccvnumber').html('Invalid ccv no.');
				}else{
					formId.elements[i].style.border="1px solid silver";
					$('#errccvnumber').html('');
				}
			}
			else if(formId.elements[i].className=="ccvexpdatemonth required")
			{
	 			if(isEmpty(formId.elements[i].value))
				{
					flag=false;
					formId.elements[i].style.border="2px solid red";
				}else if(isNaN(formId.elements[i].value))
				{
					flag=false;
					formId.elements[i].style.border="2px solid red";
					$('#errccvexpdatemonth').html('Invalid month');
				}
	 			else if(formId.elements[i].value > 12 || formId.elements[i].value <= 0)
				{
					flag=false;
					formId.elements[i].style.border="2px solid red";
					$('#errccvexpdatemonth').html('Invalid month');
				}else
				{
					formId.elements[i].style.border="1px solid silver";
					$('#errccvexpdatemonth').html('');
				}
			}
			else if(formId.elements[i].className=="ccvexpdateyear required")
			{
				var dateReg1 = /^\d{4}$/;
				var currentdate = new Date();
	 			if(isEmpty(formId.elements[i].value))
				{
					flag=false;
					formId.elements[i].style.border="2px solid red";
				}else if((dateReg1).test(formId.elements[i].value))
				{
					if(currentdate.getFullYear() > formId.elements[i].value)
					{
						flag=false;
						formId.elements[i].style.border="2px solid red";
		 				$('#errccvexpdateyear').html('Invalid year, should be a future year');
					}else
					{
						formId.elements[i].style.border="1px solid silver";
		 				$('#errccvexpdateyear').html('');						
					}
				}else{
					flag=false;
					formId.elements[i].style.border="2px solid red";
	 				$('#errccvexpdateyear').html('Invalid year');
				}
			}//yogendra code end
	 		
	 	}
		return flag;
}
 
//Check valid image

//Check whether username is already inuse.
 
 function isValidUser(str)
 {
	 $(document).ready(function(){
		 $.ajax({
		 type: 'POST',
		    url: 'index.php?option=com_locator&view=subscribe&format=raw&task=checkUser&str='+str,
		    success: function(data) {
		 		$('#checkusername').html(data);
			}
		});
		});
}
 
//Check whether email id  is already inuse while registeration.
 
 function isValidEmail(email)
 {
	 $(document).ready(function(){
	$.ajax({
		 type: 'POST',
		    url: 'index.php?option=com_locator&view=subscribe&format=raw&task=checkEmail&email='+email,
		    success: function(data1) {
			$('#checkemail').html(data1);
		 	}
		});
	 
	 });
}
 
//Check whether email id  is already inuse while profile management.
 
 function isValidProfileEmail(email,userid)
 {
	 $(document).ready(function(){
	$.ajax({
		 type: 'POST',
		    url: 'index.php?option=com_locator&view=memberprofile&format=raw&task=checkProfileEmail&email='+email+'&userid='+userid,
		    success: function(profiledata) {
			$('#checkemail').html(profiledata);
		 	}
		});
	 
	 });
}

 function isEmpty(str) {
	 // Check whether string is empty.
     for (var intLoop = 0; intLoop < str.length; intLoop++)
        if (" " != str.charAt(intLoop))
           return false;
     
     return true;
 }
 
 
 function isDefault(str) {
	 var str=trim(str);
	 var str=str.toLowerCase();
	 if(str=='username'||str=='password' || str=='email' || str=='first name'|| str=='last name' || str=='confirm password' || str=='create password')
	 {
		 return true;
	 }
 }

 
 
 

 function trim (str)     
 {     
     return str.replace(/^s+/g,'').replace(/s+$/g,'')     
 }
 
 
 
 function isEmail(str) {
	    // Check whether email is proper.
	    var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;  
	   return emailPattern.test(str);  
	}

 
 
/*function to confirm before delete*/
 function confirmDelete()
 {
     return confirm( 'Are you sure?\r\n\r\nThis will permanantly delete from the database!' );
 }
 
 function checkfiletype(fm){
	
	 var fileInsert = document.getElementById('photo').value;
	  filear = fileInsert.split('.').pop().toLowerCase();
	  if($.inArray(filear, ['gif','png','jpg','jpeg']) == -1) {
		  return false;
		}
	  return true;
 }
 
