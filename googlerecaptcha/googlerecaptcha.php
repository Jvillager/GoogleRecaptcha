<?php
/**
 * @package     Google Recaptcha
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Google Recaptcha Plugin.
 * Based on the official recaptcha library( https://developers.google.com/recaptcha/docs/php )
 *
 * @package     Google Recaptcha
 * @since       2.5
 */
class plgCaptchagooglerecaptcha extends JPlugin
{
	const GOOGLE_RECAPTCHA_API_SERVER = "http://www.google.com/recaptcha/api.js";
	const GOOGLE_RECAPTCHA_API_SECURE_SERVER = "https://www.google.com/recaptcha/api.js";
	const GOOGLE_RECAPTCHA_VERIFY_SERVER = "https://www.google.com/recaptcha/api/siteverify";
	
	//@TODO:: add error msg into language string
	private static $error_code = Array(
					'missing-input-secret'=>'The secret parameter is missing.',
					'invalid-input-secret'=>'The secret parameter is invalid or malformed.',
					'missing-input-response'=>'The response parameter is missing.',
					'invalid-input-response'=>'The response parameter is invalid or malformed.'
					);

	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 * @since  3.1
	 */
	protected $autoloadLanguage = true;

	/**
	 * Initialise the captcha
	 *
	 * @param   string  $id  The id of the field.
	 *
	 * @return  Boolean	True on success, false otherwise
	 *
	 * @since  2.5
	 */
	public function onInit($id = 'dynamic_recaptcha_1')
	{
		$document = JFactory::getDocument();
		$app      = JFactory::getApplication();

		JHtml::_('jquery.framework');

		$site_key = $this->params->get('site_key', '');

		if (empty($site_key))
		{
			throw new Exception(JText::_('PLG_GOOGLE_RECAPTCHA_ERROR_NO_SITE_KEY'));
		}

		$server = self::GOOGLE_RECAPTCHA_API_SERVER;

		if ($app->isSSLConnection())
		{
			$server = self::GOOGLE_RECAPTCHA_API_SECURE_SERVER;
		}

		JHtml::_('script', $server);
		
		return true;
	}

	/**
	 * Gets the challenge HTML
	 *
	 * @param   string  $name   The name of the field.
	 * @param   string  $id     The id of the field.
	 * @param   string  $class  The class of the field. This should be passed as
	 *                          e.g. 'class="required"'.
	 *
	 * @return  string  The HTML to be embedded in the form.
	 *
	 * @since  2.5
	 */
	public function onDisplay($name, $id = 'dynamic_recaptcha_1', $class = '')
	{
		$key = $this->params->get('site_key', '');
		
		return "<div class='g-recaptcha' data-sitekey='$key'></div>";
		
	}

	/**
	 * verify if the user's guess was correct
	 *
	 * @param   string  $code  Answer provided by user.
	 *
	 * @return  True if the answer is correct, false otherwise
	 *
	 * @since  2.5
	 */
	public function onCheckAnswer($code)
	{
		$input      = JFactory::getApplication()->input;
		$privatekey = $this->params->get('secret_key');
		$remoteip   = $input->server->get('REMOTE_ADDR', '', 'string');
		$g_recaptcha_response = $input->get('g-recaptcha-response', '');


		// Check for Private Key
		if (empty($privatekey))
		{
			$this->_subject->setError(JText::_('PLG_GOOGLE_RECAPTCHA_ERROR_NO_PRIVATE_KEY'));

			return false;
		}

		// Check for IP
		if (empty($remoteip))
		{
			$this->_subject->setError(JText::_('PLG_GOOGLE_RECAPTCHA_ERROR_NO_IP'));
			return false;
		}
		
		$url=self::GOOGLE_RECAPTCHA_VERIFY_SERVER."?secret=".$privatekey."&response=".$g_recaptcha_response."&remoteip=".$remoteip;
	
		$response = $this->getCurlData($url);
		
		$response = json_decode($response, true);
		
		//reCaptcha success check 
		if($response['success'])
		{
			return true;
		}
		
		$error_msg="Please re-enter your reCAPTCHA.";
		
		if ( isset($response['error_code']) && isset(self::$error_code[$response['error_code']]) ) {
			$error_msg = self::$error_code[$response['error_code']];
		}
		
		$this->_subject->setError($error_msg);
		
		return false;
	}
	
	
	function getCurlData($url)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);
		$curlData = curl_exec($curl);
		curl_close($curl);
		
		return $curlData;
	}

}
