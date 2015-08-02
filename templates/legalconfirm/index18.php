<?php
/**
 * @package		Joomla.Site
 * @subpackage	Templates.beez5
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>::Legal Confirm::</title>
<?php 
$doc				= JFactory::getDocument();
$doc->addStyleSheet($this->baseurl.'/templates/system/css/system.css');
?>
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/css/style.css" type="text/css"/>


<jdoc:include type="head" />

</head>

<body>
<!--start of main-wrapper-->
<div class="main-wrapper">
<div class="header-container">
<div class="nav-container">
<div class="nav">
<!--<ul>-->
<!--<li><a href="#">Home</a></li><li><a href="#">About us</a></li><li><a href="#">Contact us</a></li>-->
<!--</ul>-->
 <jdoc:include type="modules" name="main-nav"/>
<!--end of nav-->
</div>
<!--end of nav-container-->
</div>
 <jdoc:include type="modules" name="logout"/>
<div class="header">
<div class="logo"><a href="index.php"><img src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/images/logo.png"/></a></div>

<!--end of header-->
</div>
<div class="tagline-container">
<div class="tagline">
<h5>Secure and efficient communication between auditors and lawyers</h5>
</div>
<!--end of tagline-container-->
</div>

<!--end of header-container-->
</div>
<div class="main-container">
<div class="main">
<jdoc:include type="message" />
<jdoc:include type="component" />
<!--end of main-->
<div class="clear"></div>
</div>
<!--end of main-container-->
</div>
<div class="footer-container">
<div class="footer">
<p>All Rights Reserved. 2013-14</p>
</div>
<!--end of footer-container-->
</div>
</div>
<!--end of main-wrapper-->
</body>
</html>