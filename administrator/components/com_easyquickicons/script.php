<?php
/**
 * Installer File
 *
 * @package			Easy QuickIcons
 * @version			$Id: script.php 34 2012-09-25 14:50:29Z allan $
 *
 * @author			Allan <allan@awynesoft.com>
 * @link			http://www.awynesoft.com
 * @copyright		Copyright (C) 2012 AwyneSoft.com All Rights Reserved
 * @license			GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @credits			JoomlaShine.com Team
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
class com_easyquickiconsInstallerScript
{
	/**
	 * @var JXMLElement
	 */
	private $_manifest = null;
	
	/**
	 * Contains all extensions is declared in subinstall section
	 * @var array
	 */
	//private $_relatedExtensions = array();
	/*
	 * $parent is the class calling this method.
	 * $type is the type of change (install, update or discover_install, not uninstall).
	 * preflight runs before anything else and while the extracted files are in the uploaded temp folder.
	 * If preflight returns false, Joomla will abort the update and undo everything already done.
	 */
	function preflight( $type, $parent ) {
		$app 		= JFactory::getApplication();
		$installer	= $parent->getParent();
		
		$jversion 	= new JVersion();
		
		// Installing component manifest file version
		$this->release = $parent->get( "manifest" )->version;
		
		// Manifest file minimum Joomla version
		$this->minimum_joomla_release = $parent->get( "manifest" )->attributes()->version;   

		// abort if the current Joomla release is older
		if( version_compare( $jversion->getShortVersion(), $this->minimum_joomla_release, 'lt' ) ) {
			Jerror::raiseWarning(null, 'COM_EASYQUICKICONS_CANNOT_INSTALL'.$this->minimum_joomla_release);
			return false;
		}
 
		// abort if the component being installed is not newer than the currently installed version
		if ( $type == 'update' ) {
			$oldRelease = $this->getParam('version');
			$rel = $oldRelease . ' to ' . $this->release;
			if ( version_compare( $this->release, $oldRelease, 'le' ) ) {
				Jerror::raiseWarning(null, 'COM_EASYQUICKICONS_INCORRECT_VERSION_SEQUENCE ' . $rel);
				return false;
			}
		}
		else { $rel = $this->release; }
		
		$this->_manifest = $installer->getManifest();
		$this->_relatedExtensions = $this->_parseRelatedExtensions($installer);
		
		$canInstallExtension 		= true;
		$canInstallAdminLanguage 	= is_writable(JPATH_ADMINISTRATOR.'/language');
		

		if ($canInstallAdminLanguage === false) {
			$app->enqueueMessage(sprintf('Cannot install language file at "%s"', JPATH_ADMINISTRATOR.'/language'), 'error');
		}

		foreach (glob(JPATH_ADMINISTRATOR.'/language/*', GLOB_ONLYDIR) as $dir) {
			if (!is_writable($dir)) {
				$canInstallAdminLanguage = false;
				$app->enqueueMessage(sprintf('Cannot install language file at "%s"', $dir), 'error');
			}
		}

		// Checking folder permissions for related extensions
		foreach ($this->_relatedExtensions as $extension) 
		{
			
			switch ($extension->type) {

				case 'component':
					$sitePath = JPATH_SITE.'/components';
					$adminPath = JPATH_ADMINISTRATOR.'/components';

					if (!is_dir($sitePath) || !is_writable($sitePath)) {
						$canInstallExtension = false;
						$app->enqueueMessage(sprintf('Cannot install %s "%s" because "%s" is readonly', $extension->type, $extension->name, $sitePath), 'error');
					}

					if (!is_dir($adminPath) || !is_writable($adminPath)) {
						$canInstallExtension = false;
						$app->enqueueMessage(sprintf('Cannot install %s "%s" because "%s" is readonly', $extension->type, $extension->name, $adminPath), 'error');
					}
				break;

				case 'module':
					$path = ($extension->client == 'site') ? JPATH_SITE.'/' : JPATH_ADMINISTRATOR.'/';
					$path.= 'modules';

					if (!is_dir($path) || !is_writable($path)) {
						$canInstallExtension = false;
						$app->enqueueMessage(sprintf('Cannot install %s "%s" because "%s" is readonly', $extension->type, $extension->name, $path), 'error');
					}
				break;
			}
		}
		
		return $canInstallExtension && $canInstallAdminLanguage;

	}
 
	/*
	 * $parent is the class calling this method.
	 * install runs after the database scripts are executed.
	 * If the extension is new, the install method is run.
	 * If install returns false, Joomla will abort the install and undo everything already done.
	 */
	
	function install( $parent ) {
		
		$app = JFactory::getApplication();
		
		$app->setUserState('com_installer.message', '');
		$app->setUserState('com_installer.extension_message', '');
		
		$this->addCategories();
		
		$this->disableQuickicon();
		$parent->getParent()->setRedirectURL('index.php?option=com_easyquickicons&layout=welcome');
	}
 	
	/*
	 * $parent is the class calling this method.
	 * update runs after the database scripts are executed.
	 * If the extension exists, then the update method is run.
	 * If this returns false, Joomla will abort the update and undo everything already done.
	 */
	
	function update( $parent ) {
		
		$this->addCategories();
		
		$this->disableQuickicon();

		$parent->getParent()->setRedirectURL('index.php?option=com_easyquickicons&layout=welcome');
	}
 	
	/*
	 * $parent is the class calling this method.
	 * $type is the type of change (install, update or discover_install, not uninstall).
	 * postflight is run after the extension is registered in the database.
	 */
	function postflight( $type, $parent ) {
		// always create or modify these parameters
		
		$params['version'] = $this->release;
		$params['license'] = '';
 
		// define the following parameters
		if ( $type == 'install' ) {
			$params['install_type'] = 'install';
		} else if($type == 'update'){
			$params['install_type'] = 'update';
		} else {
			$params['install_type'] = '';
		}
 
		$this->setParams( $params );
 		
		$installer	= $parent->getParent();
		$app 		= JFactory::getApplication();
		
		$this->_manifest = $installer->getManifest();
		$this->_relatedExtensions = $this->_parseRelatedExtensions($installer);

		foreach ($this->_relatedExtensions as $extension) {

			$subInstaller = new JInstaller();
			if (!$subInstaller->install($extension->source)) {
				$app->enqueueMessage(sprintf('Error installing %s "%s"', $extension->type, $extension->name), 'error');
				continue;
			}

			$this->_updateExtensionSettings($extension);
			$app->enqueueMessage(sprintf('Install %s "%s" was successful', $extension->type, $extension->name));
		}
	}

	/*
	 * $parent is the class calling this method
	 * uninstall runs before any other action is taken (file removal or database processing).
	 */
	function uninstall( $parent ) {
		$installer = $parent->getParent();

		$this->_manifest = $installer->getManifest();
		$this->_uninstall	= true;

		// Get component dependency
		$this->_relatedExtensions = $this->_parseRelatedExtensions($installer);
		// Disable all dependency
		$this->_disableAllRelatedExtensions();

		foreach ($this->_relatedExtensions as $extension) {
			$this->_removeExtension($extension);
		}
		//enable Standard quickicon
		$this->enableQuickicon();
		echo '<p>' . JText::_('COM_EASYQUICKICONS_UNINSTALL') . '</p>';
	}
 
	/*
	 * get a variable from the manifest file (actually, from the manifest cache).
	 */
	function getParam( $name ) {
		$db = JFactory::getDbo();
		$db->setQuery('SELECT manifest_cache FROM #__extensions WHERE element = "com_easyquickicons"');
		$manifest = json_decode( $db->loadResult(), true );
		return $manifest[ $name ];
	}
	/*
	 * Disable Joomla Quickicons and the easyquickicon plugin*/
	function disableQuickicon(){
	
		$db = JFactory::getDbo();
		$app = JFactory::getApplication();
		$query = $db->getQuery(true);

		$query->select('id');
		$query->from('#__modules');
		$query->where('module = '. $db->Quote('mod_quickicon'));

		$db->setQuery($query);
		if($id=$db->loadResult()) {
			$query = $db->getQuery(true);
			$query->update('#__modules');
			$query->set('published = 0');
			$query->where('id = '. $db->Quote($id));
			$db->setQuery($query);
			if ($db->query()) {
				$app->enqueueMessage('Standard Joomla Quickicons has been successfully disabled');
			} 
		} 
		
	}
	/* Enable Joomla Quickicons*/
	function enableQuickicon(){
	
		$db = JFactory::getDbo();
		$app = JFactory::getApplication();
		$query = $db->getQuery(true);

		$query->select('id');
		$query->from('#__modules');
		$query->where('module = '. $db->Quote('mod_quickicon'));

		$db->setQuery($query);
		if($id=$db->loadResult()) {
			$query = $db->getQuery(true);
			$query->update('#__modules');
			$query->set('published = 1');
			$query->where('id = '. $db->Quote($id));
			$db->setQuery($query);
			if ($db->query()) {
				$app->enqueueMessage('Standard Joomla Quickicons has been successfully enabled');
			} 
		} 
	}
	function addCategories(){
		
		require_once JPATH_SITE.'/libraries/joomla/database/table/category.php';
		
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		$app = JFactory::getApplication();
		
		$category = array();
		$category['parent_id'] = '1';
		$category['extension'] = "com_easyquickicons";
		$category['title'] = 'Standard';
		$category['published'] = '1';
		$category['access'] = '1';
		$category['level'] = '1';
		$category['description'] = "<p>Default Joomla! quickicons</p>";
		$category['language'] = "*";
		$category['created_user_id'] = $user->id;

		$catTable = new JTableCategory($db);
		$catTable->setLocation($category['parent_id'], 'last-child');
		$catTable->setRules('{"core.view":{"1":1},"core.delete":[],"core.edit":[],"core.edit.state":[]}');
		if ($catTable->save($category)) {
			
			$app->enqueueMessage('Easy Quickicons Standard category has been successfuly created.');
			
			//Get category ID
			$query = $db->getQuery(true);
			$query->select('id');
			$query->from('#__categories');
			$query->where('extension = \'com_easyquickicons\'');
			$query->where('title = \'Standard\'');
	
			$db->setQuery($query);
			$catid = (int)$db->loadResult();

			$icons = array();
			$icons[] = array('catid' => $catid, 'name' => 'Add New Article', 'link' => 'index.php?option=com_content&task=article.add', 'target' => '_self', 'icon' => 'icon-48-article-add.png', 'access' => 1, 'published' => 1, 'ordering' => 1, 'description' => 'Adds new Joomla! article');
			$icons[] = array('catid' => $catid, 'name' => 'Article Manager', 'link' => 'index.php?option=com_content', 'target' => '_self', 'icon' => 'icon-48-article.png', 'access' => 1, 'published' => 1, 'ordering' => 2, 'description' => 'Joomla! article manager');
			$icons[] = array('catid' => $catid, 'name' => 'Category Manager', 'link' => 'index.php?option=com_categories&extension=com_content', 'target' => '_self', 'icon' => 'icon-48-category.png', 'access' => 1, 'published' => 1, 'ordering' => 3, 'description' => 'Joomla! category manager');
			$icons[] = array('catid' => $catid, 'name' => 'Media Manager', 'link' => 'index.php?option=com_media', 'target' => '_self', 'icon' => 'icon-48-media.png', 'access' => 1, 'published' => 1, 'ordering' => 4, 'description' => 'Joomla! media manager');
			$icons[] = array('catid' => $catid, 'name' => 'Menu Manager', 'link' => 'index.php?option=com_menus', 'target' => '_self', 'icon' => 'icon-48-menumgr.png', 'access' => 1, 'published' => 1, 'ordering' => 5, 'description' => 'Joomla! menu manager');
			$icons[] = array('catid' => $catid, 'name' => 'User Manager', 'link' => 'index.php?option=com_users', 'target' => '_self', 'icon' => 'icon-48-user.png', 'access' => 1, 'published' => 1, 'ordering' => 6, 'description' => 'Joomla! user manager');
			$icons[] = array('catid' => $catid, 'name' => 'Module Manager', 'link' => 'index.php?option=com_modules', 'target' => '_self', 'icon' => 'icon-48-module.png', 'access' => 1, 'published' => 1, 'ordering' => 7, 'description' => 'Joomla! module manager');
			$icons[] = array('catid' => $catid, 'name' => 'Extension Manager', 'link' => 'index.php?option=com_installer', 'target' => '_self', 'icon' => 'icon-48-extension.png', 'access' => 1, 'published' => 1, 'ordering' => 8, 'description' => 'Joomla! extension manager');
			$icons[] = array('catid' => $catid, 'name' => 'Language Manager', 'link' => 'index.php?option=com_languages', 'target' => '_self', 'icon' => 'icon-48-language.png', 'access' => 1, 'published' => 1, 'ordering' => 9, 'description' => 'Joomla! language manager');
			$icons[] = array('catid' => $catid, 'name' => 'Global Configuration', 'link' => 'index.php?option=com_config', 'target' => '_self', 'icon' => 'icon-48-config.png', 'access' => 1, 'published' => 1, 'ordering' => 10, 'description' => 'Joomla! global configuration');
			$icons[] = array('catid' => $catid, 'name' => 'Template Manager', 'link' => 'index.php?option=com_templates', 'target' => '_self', 'icon' => 'icon-48-themes.png', 'access' => 1, 'published' => 1, 'ordering' => 11, 'description' => 'Joomla! template manager');
			$icons[] = array('catid' => $catid, 'name' => 'Edit Profile', 'link' => 'index.php?option=com_admin&task=profile.edit', 'target' => '_self', 'icon' => 'icon-48-info.png', 'access' => 1, 'published' => 1, 'ordering' => 12, 'description' => 'Joomla! profile editor');
	
			require_once JPATH_BASE.'/components/com_easyquickicons/tables/easyquickicon.php';
			
			foreach ($icons as $icon) {
				
				$iconsTable = new EasyquickiconsTableEasyquickicon($db);
				$iconsTable->setRules('{"core.view":{"1":1},"core.delete":[],"core.edit":[],"core.edit.state":[]}');
				
				if(!$iconsTable->save($icon)){
					$app->enqueueMessage('Standard category icons cannot be created.', 'error');
				}
					
			}
		} 

		$custom = array();
		$custom['parent_id'] = '1';
		$custom['extension'] = "com_easyquickicons";
		$custom['title'] = 'Custom';
		$custom['published'] = '1';
		$custom['access'] = '1';
		$custom['level'] = '1';
		$custom['description'] = "<p>Custom quickicons</p>";
		$custom['language'] = "*";
		$custom['created_user_id'] = $user->id;

		$cTable = new JTableCategory($db);
		$cTable->setLocation($custom['parent_id'], 'last-child');
		$cTable->setRules('{"core.view":{"1":1},"core.delete":[],"core.edit":[],"core.edit.state":[]}');
		if($cTable->save($custom)){
			
			//get custom category id
			$query = $db->getQuery(true);
			$query->select('id');
			$query->from('#__categories');
			$query->where('extension = \'com_easyquickicons\'');
			$query->where('title = \'Custom\'');
			$db->setQuery($query);
			$id = (int)$db->loadResult();
			
			//get uncategorized icons
			$query = $db->getQuery(true);
			$query->select('id, catid');
			$query->from('#__easyquickicons');
			//$query->where('module_group IN (0,2)');
			$query->where('catid=0');
			
			$db->setQuery($query);
			$db->query();
			$uncatIcons = $db->loadObjectList();
			//set category
			for($i = 0; count($uncatIcons) > $i; $i++){
				
				$query = 'UPDATE #__easyquickicons'
					.	' SET catid=' . $db->quote($id)
					.	' WHERE id=' . $uncatIcons[$i]->id
					;
				$db->setQuery($query);
				
			    if (!$db->query()) {
					throw new Exception($db->getErrorMsg());
				}
			}
			
		}
	}
	/*
	 * sets parameter values in the component's row of the extension table
	 */
	function setParams($param_array) {
		if ( count($param_array) > 0 ) {
			// read the existing component value(s)
			$db = JFactory::getDbo();
			$db->setQuery('SELECT params FROM #__extensions WHERE element = "com_easyquickicons"');
			$params = json_decode( $db->loadResult(), true );
			// add the new variable(s) to the existing one(s)
			foreach ( $param_array as $name => $value ) {
				$params[ (string) $name ] = (string) $value;
			}
			// store the combined new and existing values back as a JSON string
			$paramsString = json_encode( $params );
			$db->setQuery('UPDATE #__extensions SET params = ' .
				$db->quote( $paramsString ) .
				' WHERE element = "com_easyquickicons"' );
				$db->query();
		}
	}
	
	private function _parseRelatedExtensions ($installer)
	{
		// Declared component dependency.
		static $relatedExtensions;

		// Continue only if component dependency not parsed before
		if ( ! isset($relatedExtensions) OR ! is_array($relatedExtensions))
		{
			// Start parsing component dependency
			$relatedExtensions = array();

			if (isset($this->_manifest->subinstall) AND $this->_manifest->subinstall instanceOf SimpleXMLElement)
			{
				// Loop on each node to retrieve dependency information
				foreach ($this->_manifest->subinstall->children() AS $node)
				{
					// Verify tag name
					if ($node->name() !== 'extension')
					{
						continue;
					}

					// Get dependency information
					$attributes	= $node->attributes();
					$name		= (isset($attributes->name))	? (string) $attributes->name	: '';
					$type		= (isset($attributes->type))	? (string) $attributes->type	: '';
					$folder		= (isset($attributes->folder))	? (string) $attributes->folder	: '';
					$publish	= (isset($attributes->publish)	AND ((string) $attributes->publish == 'true'	OR (string) $attributes->publish == 'yes'));
					$lock		= (isset($attributes->lock)		AND ((string) $attributes->lock == 'true'		OR (string) $attributes->lock == 'yes'));
					$remove		= (isset($attributes->remove)	AND ((string) $attributes->remove == 'true'		OR (string) $attributes->remove == 'yes'));
					$client		= (isset($attributes->client))		? (string) $attributes->client		: 'site';
					$position	= (isset($attributes->position))	? (string) $attributes->position	: '';
					$ordering	= (isset($attributes->ordering))	? (string) $attributes->ordering	: '1';
					$title		= (isset($attributes->title))		? (string) $attributes->title		: $name;

					// Validate dependency
					if (empty($name) OR empty($type) OR ! in_array($type, array('plugin', 'module', 'component')))
					{
						continue;
					}
					
					if ($type == 'plugin' AND empty($folder))
					{
						continue;
					}

					if ($type == 'plugin' AND $name == 'easyquickicons')
					{
						/*
						// Call method to safely install/uninstall plugin
						(isset($this->_uninstall) AND $this->_uninstall)
						? $this->_uninstallPlugin($installer, $attributes)
						: $this->_installPlugin($installer, $attributes);
						*/
					}
					else
					{
						// Prepare dependency installation
						$extension = new StdClass;
						$extension->type	= $type;
						$extension->name	= $name;
						$extension->folder	= $folder;
						$extension->publish	= $publish;
						$extension->lock	= $lock;
						$extension->remove	= $remove;
						$extension->client	= $client;
						$extension->source	= $installer->getPath('source') . DS . $attributes->dir;

						if ($type == 'module')
						{
							$extension->position = $position;
							$extension->ordering = $ordering;
							$extension->title = $title;
						}

						$relatedExtensions[] = $extension;
					}
				}
			}
		}

		return $relatedExtensions;
	}
	private function _updateExtensionSettings ($extension)
	{
		$table = JTable::getInstance('Extension');
		$table->load(array('element' => $extension->name));
		$table->enabled = ($extension->publish == true) ? 1 : 0;
		$table->protected = ($extension->lock == true) ? 1 : 0;
		$table->client_id = ($extension->client == 'site') ? 0 : 1;
		$table->store();

		if ($extension->type == 'module') {
			$module = JTable::getInstance('module');
			$module->load(array('module' => $extension->name));

			$module->title = $extension->title;
			$module->ordering = $extension->ordering;
			$module->published = ($extension->publish == true) ? 1 : 0;
			$module->position = $extension->position;

			$module->store();

			if (is_numeric($module->id) && $module->id > 0) {
				$db =& JFactory::getDbo();
				$db->setQuery("INSERT INTO #__modules_menu (moduleid, menuid) VALUES ({$module->id}, 0)");
				$db->query();
			}
		}

		return $this;
	}
	private function _disableAllRelatedExtensions ()
	{
		foreach ($this->_relatedExtensions AS $extension)
		{
			$this->_disableExtension($extension);
		}

		return $this;
	}

	private function _disableExtension ($extension)
	{
		$dbo = JFactory::getDbo();
		$dbo->setQuery("UPDATE #__extensions SET enabled=0 WHERE element='{$extension->name}'");
		$dbo->query();
	}

	private function _unlockExtension ($extension)
	{
		$dbo = JFactory::getDbo();
		$dbo->setQuery("UPDATE #__extensions SET protected=0 WHERE element='{$extension->name}'");
		$dbo->query();
	}

	private function _removeExtension ($extension)
	{
		$app = JFactory::getApplication();

		$dbo = JFactory::getDbo();
		$dbo->setQuery("SELECT * FROM #__extensions WHERE element='{$extension->name}'");
		$extensions = $dbo->loadObjectList();

		foreach ($extensions as $ext) {
			$installer = new JInstaller();

			$this->_disableExtension($extension);
			$this->_unlockExtension($extension);

			if ($ext->extension_id > 0) {
				if ($installer->uninstall($extension->type, $ext->extension_id)) {
					$app->enqueueMessage(sprintf('%s "%s" has been uninstalled', ucfirst($extension->type), $extension->name));
				}
				else {
					$app->enqueueMessage(sprintf('Cannot uninstall %s "%s"', $extension->type, $extension->name . ' ' . $ext->extension_id));
				}
			}
		}
	}
}