<?php
/**
 * reCAPTCHA Pro plugin for Craft CMS 3.x
 *
 * @link      https://github.com/kerosin
 * @copyright Copyright (c) 2021 kerosin
 */

namespace kerosin\recaptchapro;

use kerosin\recaptchapro\services\RecaptchaProService;
use kerosin\recaptchapro\variables\RecaptchaProVariable;
use kerosin\recaptchapro\models\Settings;

use Craft;
use craft\base\Plugin;
use craft\elements\User;
use craft\web\twig\variables\CraftVariable;
use craft\contactform\models\Submission;

use yii\base\Event;

use Exception;

/**
 * @author    kerosin
 * @package   RecaptchaPro
 * @since     1.0.0
 *
 * @property  RecaptchaProService $recaptchaProService
 * @property  Settings $settings
 * @method    Settings getSettings()
 */
class RecaptchaPro extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * RecaptchaPro::$plugin
     *
     * @var RecaptchaPro
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * To execute your plugin’s migrations, you’ll need to increase its schema version.
     *
     * @var string
     */
    public $schemaVersion = '1.0.0';

    /**
     * Set to `true` if the plugin should have a settings view in the control panel.
     *
     * @var bool
     */
    public $hasCpSettings = true;

    /**
     * Set to `true` if the plugin should have its own section (main nav item) in the control panel.
     *
     * @var bool
     */
    public $hasCpSection = false;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        // Register variables
        Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, function (Event $event) {
            /** @var CraftVariable $variable */
            $variable = $event->sender;
            $variable->set('recaptchaPro', RecaptchaProVariable::class);
        });

        // Register event listeners
        $this->registerEventListeners();

        Craft::info(Craft::t('recaptcha-pro', '{name} plugin loaded', ['name' => $this->name]), __METHOD__);
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * Returns the rendered settings HTML, which will be inserted into the content
     * block on the settings page.
     *
     * @return string The rendered settings HTML
     * @throws Exception
     */
    protected function settingsHtml(): string
    {
        $settings = $this->getSettings();

        return Craft::$app->view->renderTemplate(
            'recaptcha-pro/settings',
            [
                'settings' => $settings,
                'action3DefaultValue' => $settings::ACTION_3_DEFAULT_VALUE,
                'score3DefaultValue' => $settings::SCORE_3_DEFAULT_VALUE,
                'tabindexCheckbox2DefaultValue' => $settings::TABINDEX_CHECKBOX_2_DEFAULT_VALUE,
                'tabindexInvisible2DefaultValue' => $settings::TABINDEX_INVISIBLE_2_DEFAULT_VALUE,
                'errorMessageDefault' => $settings::ERROR_MESSAGE_DEFAULT,
            ]
        );
    }

    /**
     * @return void
     */
    protected function registerEventListeners(): void
    {
        $settings = $this->getSettings();

        if ($settings->validateContactForm && class_exists(Submission::class)) {
            Event::on(Submission::class, Submission::EVENT_AFTER_VALIDATE, function (Event $event) {
                /** @var RecaptchaProService $recaptchaProService */
                $recaptchaProService = RecaptchaPro::$plugin->recaptchaProService;

                if (!$recaptchaProService->verifyForm($recaptchaProService::FORM_TYPE_CONTACT)) {
                    $errorMessage = $recaptchaProService->getFormLastErrorMessage();

                    if ($errorMessage != '') {
                        $event->sender->addError('recaptchaPro', Craft::t('recaptcha-pro', $errorMessage));
                    }
                }
            });
        }

        if ($settings->validateUserRegistrationForm) {
            Event::on(User::class, User::EVENT_BEFORE_SAVE, function (Event $event) {
                if (!$event->isNew) {
                    return;
                }

                /** @var RecaptchaProService $recaptchaProService */
                $recaptchaProService = RecaptchaPro::$plugin->recaptchaProService;

                if (!$recaptchaProService->verifyForm($recaptchaProService::FORM_TYPE_USER_REGISTRATION)) {
                    $event->isValid = false;
                    $errorMessage = $recaptchaProService->getFormLastErrorMessage();

                    if ($errorMessage != '') {
                        $event->sender->addError('recaptchaPro', Craft::t('recaptcha-pro', $errorMessage));
                    }
                }
            });
        }
    }
}
