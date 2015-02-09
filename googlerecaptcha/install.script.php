<?php
/**
 * @package     Joomla Plugin for google recaptcha 
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 * @author      mManishTrivedi
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class plgCaptchaGoogleRecaptcha
{
	/**
	 * Called before any type of action
	 *
	 * @param   string  $type  Which action is happening (install|uninstall|discover_install)
	 * @param   object  $parent  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function preflight($type, $parent)
	{}

	/**
	 * Called on installation
	 *
	 * @param   object  $parent  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	function install($parent)
	{ }

    /**
	 * Called on uninstallation
	 *
	 * @param   object  $parent  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	function uninstall($parent)
	{ }

	/**
	 * Called after install
	 *
	 * @param   string  $type  Which action is happening (install|uninstall|discover_install)
	 * @param   object  $parent  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function postflight($type, $parent)
	{		
		return $this->_addScript();
	}

	//Redirects to plugin after Installation
	function _addScript()
	{
		$query 		 = " SELECT `extension_id` FROM `#__extensions` WHERE `element` = 'googlerecaptcha' AND `folder` = 'captcha' ";
		$extensionId = JFactory::getDbo()->setQuery($query)->loadResult();
		
		if (empty($extensionId)) {
			return true;
		}
		$url 		 = 'index.php?option=com_plugins&view=plugin&task=plugin.edit&extension_id='.$extensionId;
		
		?>
			<script type="text/javascript">
				window.onload = function(){	
				  setTimeout("location.href = '<?php echo$url;?>';", 100);
				}

				// Tracking Installation
				  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
				  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
				  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
				  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

				  ga('create', 'UA-59501642-1', 'auto');
				  ga('send', 'pageview');
			</script>
		<?php
	}

}




