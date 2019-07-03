<?php

/**
 *
 *    Copyright (C) 2017 onOffice GmbH
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU Affero General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU Affero General Public License for more details.
 *
 *    You should have received a copy of the GNU Affero General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace onOffice\WPlugin\Gui;

use onOffice\WPlugin\Model\FormModel;
use onOffice\WPlugin\Model\InputModelOption;
use onOffice\WPlugin\Renderer\InputModelRenderer;
use const ONOFFICE_PLUGIN_DIR;
use function __;
use function add_action;
use function admin_url;
use function do_settings_sections;
use function esc_attr;
use function esc_html;
use function get_option;
use function json_encode;
use function plugins_url;
use function settings_fields;
use function submit_button;
use function wp_nonce_field;

/**
 *
 * @url http://www.onoffice.de
 * @copyright 2003-2017, onOffice(R) GmbH
 *
 */

class AdminPageApiSettings
	extends AdminPage
{
	/**
	 *
	 * @param string $pageSlug
	 *
	 */

	public function __construct($pageSlug)
	{
		parent::__construct($pageSlug);
		$this->addFormModelAPI();
		$this->addFormModelGoogleCaptcha();
		$this->addFormModelGoogleMapsKey();
	}


	/**
	 *
	 */

	private function addFormModelAPI()
	{
		$labelKey = __('API token', 'onoffice');
		$labelSecret = __('API secret', 'onoffice');
		$pInputModelApiKey = new InputModelOption('onoffice-settings', 'apikey', $labelKey, 'string');
		$optionNameKey = $pInputModelApiKey->getIdentifier();
		$pInputModelApiKey->setValue(get_option($optionNameKey));
		$pInputModelApiSecret = new InputModelOption('onoffice-settings', 'apisecret', $labelSecret, 'string');
		$pInputModelApiSecret->setIsPassword(true);
		$optionNameSecret = $pInputModelApiSecret->getIdentifier();
		$pInputModelApiSecret->setSanitizeCallback(function($password) use ($optionNameSecret) {
			return $this->checkPassword($password, $optionNameSecret);
		});
		$pInputModelApiSecret->setValue(get_option($optionNameSecret, $pInputModelApiSecret->getDefault()));

		$pFormModel = new FormModel();
		$pFormModel->addInputModel($pInputModelApiSecret);
		$pFormModel->addInputModel($pInputModelApiKey);
		$pFormModel->setGroupSlug('onoffice-api');
		$pFormModel->setPageSlug($this->getPageSlug());
		$pFormModel->setLabel(__('API settings', 'onoffice'));

		$this->addFormModel($pFormModel);
	}


	/**
	 *
	 */

	private function addFormModelGoogleCaptcha()
	{
		$labelSiteKey = __('Site Key', 'onoffice');
		$labelSecretKey = __('Secret Key', 'onoffice');
		$pInputModelCaptchaSiteKey = new InputModelOption
			('onoffice-settings', 'captcha-sitekey', $labelSiteKey, 'string');
		$optionNameKey = $pInputModelCaptchaSiteKey->getIdentifier();
		$pInputModelCaptchaSiteKey->setValue(get_option($optionNameKey));
		$pInputModelCaptchaPageSecret = new InputModelOption
			('onoffice-settings', 'captcha-secretkey', $labelSecretKey, 'string');
		$pInputModelCaptchaPageSecret->setIsPassword(true);
		$optionNameSecret = $pInputModelCaptchaPageSecret->getIdentifier();
		$pInputModelCaptchaPageSecret->setSanitizeCallback(function($password) use ($optionNameSecret) {
			return $this->checkPassword($password, $optionNameSecret);
		});

		$pInputModelCaptchaPageSecret->setValue
			(get_option($optionNameSecret, $pInputModelCaptchaPageSecret->getDefault()));

		$pFormModel = new FormModel();
		$pFormModel->addInputModel($pInputModelCaptchaSiteKey);
		$pFormModel->addInputModel($pInputModelCaptchaPageSecret);
		$pFormModel->setGroupSlug('onoffice-google-recaptcha');
		$pFormModel->setPageSlug($this->getPageSlug());
		$pFormModel->setLabel(__('Google reCAPTCHA', 'onoffice'));
		$pFormModel->setTextCallback(function() {
			$this->renderTestFormReCaptcha();
		});

		$this->addFormModel($pFormModel);
	}


	/**
	 *
	 */

	private function addFormModelGoogleMapsKey()
	{
		$labelgoogleMapsKey = __('Google Maps Key', 'onoffice');
		$pInputModelGoogleMapsKey = new InputModelOption
				('onoffice-settings', 'googlemaps-key', $labelgoogleMapsKey, 'string');
		$optionMapKey = $pInputModelGoogleMapsKey->getIdentifier();
		$pInputModelGoogleMapsKey->setValue(get_option($optionMapKey));

		$pFormModel = new FormModel();
		$pFormModel->addInputModel($pInputModelGoogleMapsKey);
		$pFormModel->setGroupSlug('onoffice-google-maps-key');
		$pFormModel->setPageSlug($this->getPageSlug());
		$pFormModel->setLabel(__('Google Maps Key', 'onoffice'));

		$this->addFormModel($pFormModel);
	}

	/**
	 *
	 * @param string $password
	 * @return bool
	 *
	 */

	public function checkPassword($password, $optionName)
	{
		return $password != '' ? $password : get_option($optionName);
	}


	/**
	 *
	 */

	public function handleAdminNotices()
	{
		$cacheClean = filter_input(INPUT_GET, 'cache-refresh');

		if ($cacheClean === 'success') {
			add_action( 'admin_notices', [$this, 'displayCacheClearSuccess']);
		}
	}


	/**
	 *
	 */

	public function renderTestFormReCaptcha()
	{
		$tokenOptions = get_option('onoffice-settings-captcha-sitekey', '');
		$secretOptions = get_option('onoffice-settings-captcha-secretkey', '');
		$stringTranslations = [
			'response_ok' => __('The keys are OK.', 'onoffice'),
			'response_error' => __('There was an error:', 'onoffice'),
			'missing-input-secret' => __('The secret parameter is missing.', 'onoffice'),
			'invalid-input-secret' => __('The secret parameter is invalid or malformed.', 'onoffice'),
			'missing-input-response' => __('The response parameter is missing.', 'onoffice'),
			'invalid-input-response' => __('The response parameter is invalid or malformed.', 'onoffice'),
			'bad-request' => __('The request is invalid or malformed.', 'onoffice'),
		];

		if ($tokenOptions !== '' && $secretOptions !== '') {
			$template = file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'resource'
				.DIRECTORY_SEPARATOR.'CaptchaTestForm.html');
			printf($template,
				json_encode(admin_url('admin-ajax.php')),
				json_encode($stringTranslations),
				esc_html($tokenOptions));
		} else {
			echo __('In order to use Google reCAPTCHA, you need to provide your keys. '
				.'You\'re free to enable it in the form settings for later use.', 'onoffice');
		}
	}


	/**
	 *
	 */

	public function renderContent()
	{
		$this->generatePageMainTitle('Settings');

		echo '<form method="post" action="options.php">';

		foreach ($this->getFormModels() as $pFormModel) {
			$pFormBuilder = new InputModelRenderer($pFormModel);
			$pFormBuilder->buildForm();
		}

		settings_fields($this->getPageSlug());
		do_settings_sections($this->getPageSlug());

		submit_button();
		echo '</form>';

		echo '<form method="post" action="'.plugins_url('/tools/clearCache.php', ONOFFICE_PLUGIN_DIR.'/plugin.php').'">';
		wp_nonce_field('onoffice-clear-cache', 'onoffice-cache-nonce');
		submit_button(__('Clear cache'), 'delete');
		echo '</form>';
	}


	/**
	 *
	 */

	public function displayCacheClearSuccess()
	{
		$class = 'notice notice-success is-dismissible';
		$message = __('The cache was cleaned.', 'onoffice');

		printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
	}
}