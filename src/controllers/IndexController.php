<?php
/**
 * reCAPTCHA Pro plugin for Craft CMS 3.x
 *
 * @link      https://github.com/kerosin
 * @copyright Copyright (c) 2021 kerosin
 */

namespace kerosin\recaptchapro\controllers;

use kerosin\recaptchapro\RecaptchaPro;
use kerosin\recaptchapro\services\RecaptchaProService;

use Craft;
use craft\web\Controller;

use Exception;

/**
 * Index Controller
 *
 * @author    kerosin
 * @package   RecaptchaPro
 * @since     1.0.0
 */
class IndexController extends Controller
{
    // Protected Properties
    // =========================================================================

    /**
     * @var bool|array Allows anonymous access to this controller's actions.
     */
    protected $allowAnonymous = ['verify-form'];

    // Public Methods
    // =========================================================================

    /**
     * @return mixed
     * @throws Exception
     */
    public function actionVerifyForm()
    {
        $this->requirePostRequest();

        /** @var RecaptchaProService $recaptchaProService */
        $recaptchaProService = RecaptchaPro::$plugin->recaptchaProService;
        $request = Craft::$app->getRequest();

        $gotoAction = $request->getRequiredParam($recaptchaProService::PARAM_GOTO_ACTION);

        if ($recaptchaProService->verifyForm()) {
            return Controller::run('/' . $gotoAction, func_get_args());
        } else {
            $errorMessage = $recaptchaProService->getFormLastErrorMessage();

            if ($errorMessage != '') {
                Craft::$app->getSession()->setError(Craft::t('recaptcha-pro', $errorMessage));
            }

            return null;
        }
    }
}
