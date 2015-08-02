<?php
/**
 * @version     1.0.0
 * @package     com_legalconfirm
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Abhishek Gupta <abhishek.gupta@daffodilsw.com> - http://
 */

defined('_JEXEC') or die;

abstract class LegalconfirmHelper
{
	public static function myFunction()
	{
		$result = 'Something';
		return $result;
	}
	/*
	 * company function
	 */
	public static function getCompanyOptions(){
		$options = array();
		$db		= JFactory::getDbo();
		$user   = JFactory::getUser();
		$query	= $db->getQuery(true);
		$query->select('a.id As value, a.company As text');
		$query->from('#__auditorclients AS a');
		$query->where('a.lid='.$user->id);
		$query->order('a.company');
		// Get the options.
		$db->setQuery($query);
		$options = $db->loadObjectList();
		// Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseWarning(500, $db->getErrorMsg());
		}
		array_unshift($options, JHtml::_('select.option', '0', JText::_('COM_LEGALCONFIRM_CLIENT_NAME')));
		return $options;
	}
	/*
	 * function for getting the lawoffices..
	 * @params lawfirmids
	 */
	public static function getLawFirmDetail($lawfirmid)
	{
		$db		= JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('a.accounting_firm, b.email, a.lid');
		$query->from('#__users_profile_detail as a');
		$query->join('INNER', '#__users as b ON a.lid = b.id');
		$query->where('a.lid = '.$lawfirmid);
		$db->setQuery($query);
		$db->query();
		$lawfirmdata = $db->loadObject();
		return $lawfirmdata;
	}
	/*
	 * function for law firm offices
	 * @params lawfirm ids
	 */
	public static function getLawFirmOffices($lawfirmslid)
	{
		$db		= JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('a.*');
		$query->from('#__users_office as a');
		$query->where('a.lid = '.$lawfirmslid);
		$db->setQuery($query);
		$db->query();
		$lawfirmofficesdata = $db->loadObjectList();
		return $lawfirmofficesdata;
	}
	/*
	 * function for finding the auditor notes for a client
	 * @params clientid
	 */
	public static function auditornotesview($clientid)
	{
		$db	   = JFactory::getDbo();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();
		$query->select('a.*');
		$query->from('#__auditornotes as a');
		$query->where('a.lid = '.$user->id);
		$query->where('a.cid = '.$clientid);
		$db->setQuery($query);
		$db->query();
		$auditorclientnotes = $db->loadObject();
		return $auditorclientnotes;
	}
	/*
	 * finding the id of auditor notes for the client
	 */
	public static function findNotesid($cid)
	{
		$db	   = JFactory::getDbo();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();
		$query->select('a.id');
		$query->from('#__auditornotes as a');
		$query->where('a.lid = '.$user->id);
		$query->where('a.cid = '.$cid);
		$db->setQuery($query);
		$db->query();
		$auditorclientnotes = $db->loadResult();
		return $auditorclientnotes;
	}
	/*
	 * public function for finding the payment details
	 */
	public static function getadminPaymentDetail()
	{
		$db	   = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('a.*');
		$query->from('#__admin_payment_detail as a');
		$db->setQuery($query);
		$db->query();
		$paymentdata = $db->loadObject();
		return $paymentdata;
	}
	/*
	 * function for making the payment on initiating the confirmation
	 * @params no. of lawfirms was added in request made for approval by signer.
	 */
	public static function getPaymentstatus($count)
	{
		$config = JFactory::getConfig();
		$adminpaymentdetail = LegalconfirmHelper::getadminPaymentDetail();
        $environment = $adminpaymentdetail->payment_type; 
		$db	   = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query1 = $db->getQuery(true);
		$user = JFactory::getUser();
		$query->select('a.parent');
		$query->from('#__users_profile_detail as a');
		$query->where('a.lid = '.$user->id);
		$db->setQuery($query);
		$db->query();
		$parentid = $db->loadResult();
		
		$query1->select('a.*');
		$query1->from('#__users_payment_detail as a');
		$query1->where('a.lid = '.$parentid);
		$db->setQuery($query1);
		$db->query();
		$auditorpaymentdetail = $db->loadObject();
		if("sandbox" === $environment || "beta-sandbox" === $environment) // if for the test payment(sandbox)..
		$paymentString = LegalconfirmHelper::paymentSandboxRequestDetail($count, $auditorpaymentdetail, $adminpaymentdetail);
	    else // else part for live payment..
	    $paymentString = LegalconfirmHelper::paymentLiveRequestDetail($count, $auditorpaymentdetail, $adminpaymentdetail);
		$acknowledgement = LegalconfirmHelper::doDirectPayment($paymentString, $adminpaymentdetail);
		return $acknowledgement;
	}
	/*
	 * function for making the payment details in a url encoded stringof payer
	 * @params
	 */
	public static function paymentLiveRequestDetail($count, $auditorpaymentdetail, $adminpaymentdetail)
	{
		$definedamount   = $adminpaymentdetail->amount;
		$currency        = $adminpaymentdetail->currency;
		//making the url encoded string..
		// Set request-specific fields.
		$paymentType = urlencode('Sale');				// or 'Sale'
		$firstName = urlencode($auditorpaymentdetail->name_on_cc);
		//		$lastName = urlencode($auditorpaymentdetail->lname_on_cc);
		$creditCardType = urlencode($auditorpaymentdetail->cc_type);
		$creditCardNumber = urlencode($auditorpaymentdetail->cc_number);
		$expDateMonth = $auditorpaymentdetail->cc_expdatemonth;
		// Month must be padded with leading zero
		$padDateMonth = urlencode(str_pad($expDateMonth, 2, '0', STR_PAD_LEFT));

		$expDateYear = urlencode($auditorpaymentdetail->cc_expdateyear);
		$cvv2Number = urlencode($auditorpaymentdetail->cc_ccvno);
		$address1 = urlencode($auditorpaymentdetail->address);
		//$address2 = urlencode('sec 33');
		$city = urlencode($auditorpaymentdetail->city);
		$state = urlencode($auditorpaymentdetail->state);
		$zip = urlencode($auditorpaymentdetail->zip);
		$country = urlencode('USA');				// US or other valid country code
		$payamount = ($count * $definedamount);		
		$amount = urlencode($payamount);
		$currencyID = urlencode($currency);	        // or other currency ('GBP', 'EUR', 'JPY', 'CAD', 'AUD')

		// Add request-specific fields to the request string.
		$nvpStr =	"&PAYMENTACTION=$paymentType&AMT=$amount&CREDITCARDTYPE=$creditCardType&ACCT=$creditCardNumber".
			"&EXPDATE=$padDateMonth$expDateYear&CVV2=$cvv2Number&FIRSTNAME=$firstName&LASTNAME=$lastName".
			"&STREET=$address1&CITY=$city&STATE=$state&ZIP=$zip&COUNTRYCODE=$country&CURRENCYCODE=$currencyID";
		return $nvpStr;
	}
	/*
	 * function for making the payment details in a url encoded stringof payer sandBOX
	 * @params
	 */
	public static function paymentSandboxRequestDetail($count, $auditorpaymentdetail, $adminpaymentdetail)
	{
		$definedamount = $adminpaymentdetail->amount;
		$currency = $adminpaymentdetail->currency;
		//making the url encoded string..
		// Set request-specific fields.
		$paymentType = urlencode('Sale');				// or 'Sale'
		$firstName = urlencode('prem');
		$lastName = urlencode('baboo');
		$creditCardType = urlencode('visa');
		$creditCardNumber = urlencode('4222222222222');
		$expDateMonth = '06';
		// Month must be padded with leading zero
		$padDateMonth = urlencode(str_pad($expDateMonth, 2, '0', STR_PAD_LEFT));

		$expDateYear = urlencode('2018');
		$cvv2Number = urlencode('111');
		$address1 = urlencode('1 Main St');
		//$address2 = urlencode('sec 33');
		$city = urlencode('San Jose');
		$state = urlencode('CA');
		$zip = urlencode('95131');
		$country = urlencode('USA');
		$payamount = ($count * $definedamount);		// US or other valid country code
		$amount = urlencode($payamount);
		$currencyID = urlencode($currency);	        // or other currency ('GBP', 'EUR', 'JPY', 'CAD', 'AUD')

		// Add request-specific fields to the request string.
		$nvpStr =	"&PAYMENTACTION=$paymentType&AMT=$amount&CREDITCARDTYPE=$creditCardType&ACCT=$creditCardNumber".
			"&EXPDATE=$padDateMonth$expDateYear&CVV2=$cvv2Number&FIRSTNAME=$firstName&LASTNAME=$lastName".
			"&STREET=$address1&CITY=$city&STATE=$state&ZIP=$zip&COUNTRYCODE=$country&CURRENCYCODE=$currencyID"; 
		return $nvpStr;
	}
	/*
	 * function for making the payment for the initiation confirmation
	 * @params payment string
	 */
	public static function doDirectPayment($paymentString, $adminpaymentdetail)
	{
		$httpParsedResponseAr = LegalconfirmHelper::PPHttpPost('DoDirectPayment', $paymentString, $adminpaymentdetail);
		return $httpParsedResponseAr;
	}
	/**
	 * Send HTTP POST Request
	 *
	 * @param	string	The API method name
	 * @param	string	The POST Message fields in &name=value pair format
	 * @return	array	Parsed HTTP Response body
	 */
	public static function PPHttpPost($methodName_, $nvpStr_, $adminpaymentdetail) {
		global $environment;
        $environment = $adminpaymentdetail->payment_type;
        $api_user_name = $adminpaymentdetail->api_username;
        $api_password = $adminpaymentdetail->api_password;
        $api_signature = $adminpaymentdetail->api_signature;
		// Set up your API credentials, PayPal end point, and API version.
		$API_UserName = urlencode($api_user_name);
		$API_Password = urlencode($api_password);
		$API_Signature = urlencode($api_signature);
		//$IPaddress = urlencode('172.18.2.59');
		$API_Endpoint = "api.sandbox.paypal.com";
		if("sandbox" === $environment || "beta-sandbox" === $environment) {
			$API_Endpoint = "https://api-3t.sandbox.paypal.com/nvp";
		}
		$version = urlencode('53.0');

		// Set the curl parameters.
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);

		// Turn off the server and peer verification (TrustManager Concept).
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);

		// Set the API operation, version, and API signature in the request.
		$nvpreq = "METHOD=$methodName_&IPADDRESS=$IPaddress&VERSION=$version&PWD=$API_Password&USER=$API_UserName&SIGNATURE=$API_Signature$nvpStr_";
		// Set the request as a POST FIELD for curl.
		curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

		// Get response from the server.
		$httpResponse = curl_exec($ch);

		if(!$httpResponse) {
			exit("$methodName_ failed: ".curl_error($ch).'('.curl_errno($ch).')');
		}

		// Extract the response details.
		$httpResponseAr = explode("&", $httpResponse);

		$httpParsedResponseAr = array();
		foreach ($httpResponseAr as $i => $value) {
			$tmpAr = explode("=", $value);
			if(sizeof($tmpAr) > 1) {
				$httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
			}
		}

		if((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
			exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
		}

		return $httpParsedResponseAr;
	}
  /*
   * function for generating the template for lawfirm request on initiating the confirmation
   * @params proposalid
   */
	public static function generateTemplateLawfirmRequest($proposalid)
	{
		$db	   = JFactory::getDbo();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();
		$query->select('a.template');
		$query->from('#__clientproposals as a');
		$query->where('a.id = '.$proposalid);
		$db->setQuery($query);
		$db->query();
		$proposaltemplate = $db->loadResult();
		return $proposaltemplate;
	}
	/*
	 * function for finding the latest proposal id corrospoding to the client, propoal should be accepted by signer.
	 * @params clientid 
	 */
	public static function getLatestproposal($clientid)
	{
		$db	   = JFactory::getDbo();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();
		$query->select('max(a.id)');
		$query->from('#__clientproposals as a');
		$query->where('a.cid = '.$clientid);
		$query->where('a.status = '.'"1"');
		$db->setQuery($query);
		$db->query();
		$proposalid = $db->loadResult();
		return $proposalid;
	}
}
