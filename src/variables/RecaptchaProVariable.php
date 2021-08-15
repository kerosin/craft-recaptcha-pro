<?php
/**
 * reCAPTCHA Pro plugin for Craft CMS 3.x
 *
 * @link      https://github.com/kerosin
 * @copyright Copyright (c) 2021 kerosin
 */

namespace kerosin\recaptchapro\variables;

use kerosin\recaptchapro\RecaptchaPro;

use Exception;

/**
 * reCAPTCHA Pro Variable
 *
 * @author    kerosin
 * @package   RecaptchaPro
 * @since     1.0.0
 */
class RecaptchaProVariable
{
    // Public Methods
    // =========================================================================

    /**
     * Renders the reCAPTCHA Pro widget.
     *
     * @param array $options
     * @return void
     * @throws Exception
     */
    public function render(array $options = []): void
    {
        RecaptchaPro::$plugin->recaptchaProService->render($options);
    }

    /**
     * @param string|null $trigger
     * @return bool
     */
    public function isTrigger3Onsubmit(?string $trigger): bool
    {
        return RecaptchaPro::$plugin->recaptchaProService->isTrigger3Onsubmit($trigger);
    }

    /**
     * @param string|null $trigger
     * @return bool
     */
    public function isTrigger3Onload(?string $trigger): bool
    {
        return RecaptchaPro::$plugin->recaptchaProService->isTrigger3Onload($trigger);
    }
}
