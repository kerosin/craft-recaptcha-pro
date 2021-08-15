<?php
/**
 * reCAPTCHA Pro plugin for Craft CMS 3.x
 *
 * @link      https://github.com/kerosin
 * @copyright Copyright (c) 2021 kerosin
 */

namespace kerosin\recaptchapro\services;

use kerosin\recaptchapro\RecaptchaPro;

use Craft;
use craft\base\Component;
use craft\web\View;

use GuzzleHttp;
use GuzzleHttp\Exception\GuzzleException;

use Exception;

/**
 * RecaptchaProService Service
 *
 * @author    kerosin
 * @package   RecaptchaPro
 * @since     1.0.0
 */
class RecaptchaProService extends Component
{
    // Constants
    // =========================================================================

    const API_URL = 'https://www.google.com/recaptcha/api.js';

    const VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

    const PARAM_RESPONSE_TOKEN = 'g-recaptcha-response';
    const PARAM_SITE_KEY = 'recaptchaProSiteKey';
    const PARAM_ACTION = 'recaptchaProAction';
    const PARAM_GOTO_ACTION = 'recaptchaProGotoAction';

    const RECAPTCHA_VERSION_2 = 2;
    const RECAPTCHA_VERSION_3 = 3;

    const FORM_TYPE_CONTACT = 'contact';
    const FORM_TYPE_USER_REGISTRATION = 'user-registration';


    // Protected Properties
    // =========================================================================

    /**
     * @var string
     */
    protected $formLastErrorMessage;

    // Public Methods
    // =========================================================================

    /**
     * @return array
     */
    public function getSupportedRecaptchaVersions(): array
    {
        return [
            self::RECAPTCHA_VERSION_2,
            self::RECAPTCHA_VERSION_3,
        ];
    }

    /**
     * @param string|null $trigger
     * @return bool
     */
    public function isTrigger3Onsubmit(?string $trigger): bool
    {
        $settings = RecaptchaPro::$plugin->getSettings();

        return $trigger == $settings::TRIGGER_3_ONSUBMIT;
    }

    /**
     * @param string|null $trigger
     * @return bool
     */
    public function isTrigger3Onload(?string $trigger): bool
    {
        $settings = RecaptchaPro::$plugin->getSettings();

        return $trigger == $settings::TRIGGER_3_ONLOAD;
    }

    /**
     * @param string $siteKey
     * @return bool
     */
    public function isSiteKey3(string $siteKey): bool
    {
        $settings = RecaptchaPro::$plugin->getSettings();

        return $siteKey != '' && $siteKey == $settings->siteKey3;
    }

    /**
     * @param string $siteKey
     * @return string|bool
     */
    public function getSecretKeyBySiteKey(string $siteKey)
    {
        $result = false;

        if ($siteKey == '') {
            return $result;
        }

        $settings = RecaptchaPro::$plugin->getSettings();

        switch ($siteKey) {
            case $settings->siteKey3:
                $result = $settings->secretKey3;
                break;
            case $settings->siteKeyCheckbox2:
                $result = $settings->secretKeyCheckbox2;
                break;
            case $settings->siteKeyInvisible2:
                $result = $settings->secretKeyInvisible2;
                break;
        }

        return $result;
    }

    /**
     * @param string $siteKey
     * @return string|null
     */
    public function getErrorMessageBySiteKey(string $siteKey): ?string
    {
        $settings = RecaptchaPro::$plugin->getSettings();
        $result = $settings::ERROR_MESSAGE_DEFAULT;

        if ($siteKey == '') {
            return $result;
        }

        switch ($siteKey) {
            case $settings->siteKey3:
                $result = $settings->errorMessage3;
                break;
            case $settings->siteKeyCheckbox2:
                $result = $settings->errorMessageCheckbox2;
                break;
            case $settings->siteKeyInvisible2:
                $result = $settings->errorMessageInvisible2;
                break;
        }

        return $result != '' ? $result : $settings::ERROR_MESSAGE_DEFAULT;
    }

    /**
     * @param array $options
     * @return void
     * @throws Exception
     */
    public function render(array $options = []): void
    {
        $settings = RecaptchaPro::$plugin->getSettings();
        $defaultOptions = [];

        if (
            isset($options['version']) &&
            in_array($options['version'], $this->getSupportedRecaptchaVersions())
        ) {
            $version = (int)$options['version'];
        } else {
            $version = self::RECAPTCHA_VERSION_3;
        }

        if ($version == self::RECAPTCHA_VERSION_2) {
            if (
                (isset($options['invisible']) && $options['invisible']) ||
                (isset($options['size']) && $options['size'] == 'invisible')
            ) {
                $siteKey = $settings->siteKeyInvisible2;
                $defaultOptions['size'] = 'invisible';

                if ($settings->badgeInvisible2 != '') {
                    $defaultOptions['badge'] = $settings->badgeInvisible2;
                }

                if (is_numeric($settings->tabindexInvisible2)) {
                    $defaultOptions['tabindex'] = $settings->tabindexInvisible2;
                }
            } else {
                $siteKey = $settings->siteKeyCheckbox2;

                if ($settings->themeCheckbox2 != '') {
                    $defaultOptions['theme'] = $settings->themeCheckbox2;
                }

                if ($settings->sizeCheckbox2 != '') {
                    $defaultOptions['size'] = $settings->sizeCheckbox2;
                }

                if (is_numeric($settings->tabindexCheckbox2)) {
                    $defaultOptions['tabindex'] = $settings->tabindexCheckbox2;
                }
            }
        } else {
            $siteKey = $settings->siteKey3;
            $defaultOptions['trigger'] = $settings->trigger3;
            $defaultOptions['action'] = $settings->action3;
        }

        $variables = [
            'siteKey' => $siteKey,
            'apiUrl' => self::API_URL,
            'responseTokenParam' => self::PARAM_RESPONSE_TOKEN,
            'siteKeyParam' => self::PARAM_SITE_KEY,
            'actionParam' => self::PARAM_ACTION,
            'defaultActionValue' => $settings::ACTION_3_DEFAULT_VALUE,
            'options' => array_merge($defaultOptions, $options),
        ];

        $output = Craft::$app->view->renderTemplate(
            'recaptcha-pro/_recaptcha-' . $version,
            $variables,
            View::TEMPLATE_MODE_CP
        );

        echo $output;
    }

    /**
     * @param string $token
     * @param string $secret
     * @param string|null $action
     * @param float|null $score
     * @return bool
     */
    public function verify(string $token, string $secret, string $action = null, float $score = null): bool
    {
        $result = false;

        if ($token == '' || $secret == '') {
            return $result;
        }

        $params = [
            'secret' => $secret,
            'response' => $token,
        ];

        $remoteIP = Craft::$app->getRequest()->getRemoteIP();

        if ($remoteIP) {
            $params['remoteip'] = $remoteIP;
        }

        $client = new GuzzleHttp\Client();

        try {
            $response = $client->request('POST', self::VERIFY_URL, ['form_params' => $params]);
        } catch (GuzzleException $e) {
            return $result;
        }

        if ($response->getStatusCode() != 200) {
            return $result;
        }

        $responseJson = json_decode($response->getBody());
        $result = isset($responseJson->success) && $responseJson->success ? true : false;

        if ($result && isset($responseJson->action) && $action != null) {
            $result = $responseJson->action == $action;
        }

        if ($result && isset($responseJson->score) && is_numeric($score)) {
            $result = $responseJson->score >= $score;
        }

        return $result;
    }

    /**
     * @param string|null $formType
     * @return bool
     */
    public function verifyForm(string $formType = null): bool
    {
        $result = false;

        $this->resetFormLastErrorMessage();

        $settings = RecaptchaPro::$plugin->getSettings();
        $request = Craft::$app->getRequest();
        $securityService = Craft::$app->getSecurity();

        try {
            $siteKey = $securityService->validateData($request->getRequiredParam(self::PARAM_SITE_KEY));

            if ($siteKey === false) {
                $this->setFormLastErrorMessage($settings::ERROR_MESSAGE_DEFAULT);
                return $result;
            }

            $token = $request->getRequiredParam(self::PARAM_RESPONSE_TOKEN);
            $secretKey = $this->getSecretKeyBySiteKey($siteKey);

            if ($secretKey === false) {
                $this->setFormLastErrorMessage($this->getErrorMessageBySiteKey($siteKey));
                return $result;
            }

            $params = [$token, $secretKey];

            if ($this->isSiteKey3($siteKey)) {
                $action = $securityService->validateData($request->getParam(self::PARAM_ACTION));
                $action = $action !== false ? $action : $settings::ACTION_3_DEFAULT_VALUE;
                $params[] = $action;

                $score = $settings->score3;

                switch ($formType) {
                    case self::FORM_TYPE_CONTACT:
                        if (is_numeric($settings->contactFormScore3)) {
                            $score = $settings->contactFormScore3;
                        }
                        break;
                    case self::FORM_TYPE_USER_REGISTRATION:
                        if (is_numeric($settings->userRegistrationFormScore3)) {
                            $score = $settings->userRegistrationFormScore3;
                        }
                        break;
                }

                $score = (float)(is_numeric($score) ? $score : $settings::SCORE_3_DEFAULT_VALUE);
                
                $params[] = $score;
            }

            $result = $this->verify(...$params);
        } catch (Exception $e) {
            $result = false;
        }

        if (!$result) {
            if (!empty($siteKey)) {
                $errorMessage = $this->getErrorMessageBySiteKey($siteKey);
            } else {
                $errorMessage = $settings::ERROR_MESSAGE_DEFAULT;
            }

            $this->setFormLastErrorMessage($errorMessage);
        }
        
        return $result;
    }

    /**
     * @return string|null
     */
    public function getFormLastErrorMessage(): ?string
    {
        return $this->formLastErrorMessage;
    }

    // Protected Methods
    // =========================================================================

    /**
     * @param string|null $message
     * @return void
     */
    protected function setFormLastErrorMessage(?string $message): void
    {
        $this->formLastErrorMessage = $message;
    }

    /**
     * @return void
     */
    protected function resetFormLastErrorMessage(): void
    {
        $this->formLastErrorMessage = '';
    }
}
