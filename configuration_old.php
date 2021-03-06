<?php
class JConfig {
	public $offline = '0';
	public $offline_message = 'This site is down for maintenance.<br /> Please check back again soon.';
	public $display_offline_message = '1';
	public $offline_image = '';
	public $sitename = 'LegalConfirm';
	public $editor = 'tinymce';
	public $captcha = '0';
	public $list_limit = '10';
	public $access = '1';
	public $debug = '0';
	public $debug_lang = '0';
	public $dbtype = 'mysql';
	public $host = '172.18.2.245';
	public $user = 'root';
	public $password = '';
	public $db = 'legalconfirm_dev1';
	public $dbprefix = 'lc_';
	public $live_site = 'http://localhost/legalconfirm/';
	public $secret = 'iECLMa8NQdhFMax2';
	public $gzip = '0';
	public $error_reporting = 'default';
	public $helpurl = 'http://help.joomla.org/proxy/index.php?option=com_help&keyref=Help{major}{minor}:{keyref}';
	public $ftp_host = '127.0.0.1';
	public $ftp_port = '21';
	public $ftp_user = '';
	public $ftp_pass = '';
	public $ftp_root = '';
	public $ftp_enable = '0';
	public $offset = 'Asia/Kolkata';
	public $mailer = 'mail';
	public $mailfrom = 'admin@legalfirm.com';
	public $fromname = 'LegalConfirm';
	public $sendmail = '/usr/sbin/sendmail';
	public $smtpauth = '0';
	public $smtpuser = '';
	public $smtppass = '';
	public $smtphost = 'localhost';
	public $smtpsecure = 'none';
	public $smtpport = '25';
	public $caching = '0';
	public $cache_handler = 'cachelite';
	public $cachetime = '15';
	public $MetaDesc = '';
	public $MetaKeys = '';
	public $MetaTitle = '1';
	public $MetaAuthor = '1';
	public $MetaVersion = '0';
	public $robots = '';
	public $sef = '0';
	public $sef_rewrite = '0';
	public $sef_suffix = '0';
	public $unicodeslugs = '0';
	public $feed_limit = '10';
	public $log_path = '/opt/lampp/htdocs/legalconfirm/logs';
	public $tmp_path = '/opt/lampp/htdocs/legalconfirm/tmp';
	public $lifetime = '16';
	public $session_handler = 'database';
	public $MetaRights = '';
	public $sitename_pagetitles = '0';
	public $force_ssl = '0';
	public $feed_email = 'author';
	public $cookie_domain = '';
	public $cookie_path = '';
	public $auditor = '9';
	public $auditor_emp = '10';
	public $lawfirm = '11';
	public $lawfirm_emp = '12';
	public $lawfirm_partner = '13';
}