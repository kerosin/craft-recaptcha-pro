<?php
/**
 * reCAPTCHA Pro plugin for Craft CMS 3.x
 *
 * @link      https://github.com/kerosin
 * @copyright Copyright (c) 2021 kerosin
 */

namespace kerosin\recaptchapro\models;

use Craft;
use craft\base\Model;

/**
 * RecaptchaPro Settings Model
 *
 * This is a model used to define the plugin's settings.
 *
 * @author    kerosin
 * @package   RecaptchaPro
 * @since     1.0.0
 */
class Settings extends Model
{
    // Constants
    // =========================================================================

    const TRIGGER_3_ONSUBMIT = 'onsubmit';
    const TRIGGER_3_ONLOAD = 'onload';

    const ACTION_3_DEFAULT_VALUE = 'submit';

    const SCORE_3_DEFAULT_VALUE = 0.5;

    const TABINDEX_CHECKBOX_2_DEFAULT_VALUE = 0;

    const TABINDEX_INVISIBLE_2_DEFAULT_VALUE = 0;

    const ERROR_MESSAGE_DEFAULT = 'Unable to verify your submission.';


    // Public Properties
    // =========================================================================

    /**
     * Validate contact form.
     *
     * @var bool
     */
    public $validateContactForm = false;

    /**
     * Validate user registration form.
     *
     * @var bool
     */
    public $validateUserRegistrationForm = false;

    /**
     * Site key (v3).
     *
     * @var string
     */
    public $siteKey3;

    /**
     * Secret key (v3).
     *
     * @var string
     */
    public $secretKey3;

    /**
     * Trigger (v3).
     *
     * @var string
     */
    public $trigger3 = self::TRIGGER_3_ONSUBMIT;

    /**
     * Action (v3).
     *
     * @var string
     */
    public $action3;

    /**
     * Score (v3).
     *
     * @var float
     */
    public $score3;

    /**
     * Error message (v3).
     *
     * @var string
     */
    public $errorMessage3;

    /**
     * Contact Form Score (v3).
     *
     * @var float
     */
    public $contactFormScore3;

    /**
     * User Registration Form Score (v3).
     *
     * @var float
     */
    public $userRegistrationFormScore3;

    /**
     * Site key (v2 Checkbox).
     *
     * @var string
     */
    public $siteKeyCheckbox2;

    /**
     * Secret key (v2 Checkbox).
     *
     * @var string
     */
    public $secretKeyCheckbox2;

    /**
     * Widget color theme (v2 Checkbox).
     *
     * @var string
     */
    public $themeCheckbox2;

    /**
     * Widget size (v2 Checkbox).
     *
     * @var string
     */
    public $sizeCheckbox2;

    /**
     * Widget tabindex (v2 Checkbox).
     *
     * @var int
     */
    public $tabindexCheckbox2;

    /**
     * Error message (v2 Checkbox).
     *
     * @var string
     */
    public $errorMessageCheckbox2;

    /**
     * Site key (v2 Invisible).
     *
     * @var string
     */
    public $siteKeyInvisible2;

    /**
     * Secret key (v2 Invisible).
     *
     * @var string
     */
    public $secretKeyInvisible2;

    /**
     * Widget position (v2 Invisible).
     *
     * @var string
     */
    public $badgeInvisible2;

    /**
     * Widget tabindex (v2 Invisible).
     *
     * @var int
     */
    public $tabindexInvisible2;

    /**
     * Error message (v2 Invisible).
     *
     * @var string
     */
    public $errorMessageInvisible2;

    // Public Methods
    // =========================================================================

    /**
     * @return array
     */
    public static function getTriggers3(): array
    {
        return [
            self::TRIGGER_3_ONSUBMIT => Craft::t('recaptcha-pro', 'On Form Submit'),
            self::TRIGGER_3_ONLOAD => Craft::t('recaptcha-pro', 'On Page Load'),
        ];
    }

    /**
     * @return array
     */
    public static function getThemesCheckbox2(): array
    {
        return [
            'light' => Craft::t('recaptcha-pro', 'Light'),
            'dark' => Craft::t('recaptcha-pro', 'Dark'),
        ];
    }

    /**
     * @return array
     */
    public static function getSizesCheckbox2(): array
    {
        return [
            'normal' => Craft::t('recaptcha-pro', 'Normal'),
            'compact' => Craft::t('recaptcha-pro', 'Compact'),
        ];
    }

    /**
     * @return array
     */
    public static function getBadgesInvisible2(): array
    {
        return [
            'bottomright' => Craft::t('recaptcha-pro', 'Bottom Right'),
            'bottomleft' => Craft::t('recaptcha-pro', 'Bottom Left'),
            'inline' => Craft::t('recaptcha-pro', 'Inline'),
        ];
    }

    /**
     * Returns the validation rules for attributes.
     *
     * @return array
     */
    public function rules()
    {
        return [
            ['validateContactForm', 'boolean'],
            ['validateUserRegistrationForm', 'boolean'],
            ['siteKey3', 'string'],
            ['secretKey3', 'string'],
            ['trigger3', 'in', 'range' => array_keys(self::getTriggers3())],
            ['action3', 'string'],
            ['score3', 'double', 'min' => 0.0, 'max' => 1.0],
            ['errorMessage3', 'string'],
            ['contactFormScore3', 'double', 'min' => 0.0, 'max' => 1.0],
            ['userRegistrationFormScore3', 'double', 'min' => 0.0, 'max' => 1.0],
            ['siteKeyCheckbox2', 'string'],
            ['secretKeyCheckbox2', 'string'],
            ['themeCheckbox2', 'in', 'range' => array_keys(self::getThemesCheckbox2())],
            ['sizeCheckbox2', 'in', 'range' => array_keys(self::getSizesCheckbox2())],
            ['tabindexCheckbox2', 'integer'],
            ['errorMessageCheckbox2', 'string'],
            ['siteKeyInvisible2', 'string'],
            ['secretKeyInvisible2', 'string'],
            ['badgeInvisible2', 'in', 'range' => array_keys(self::getBadgesInvisible2())],
            ['tabindexInvisible2', 'integer'],
            ['errorMessageInvisible2', 'string'],
        ];
    }
}
