# reCAPTCHA Pro plugin for Craft CMS 3.x

Craft CMS plugin to verify forms via Google's reCAPTCHA. The plugin supports reCAPTCHA v3, reCAPTCHA v2 (Checkbox, Invisible).

## License

This plugin requires a commercial license purchasable through the [Craft Plugin Store](https://plugins.craftcms.com/craft-recaptcha-pro).

## Requirements

This plugin requires Craft CMS 3.1.0 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require kerosin/craft-recaptcha-pro

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for reCAPTCHA Pro.

## Configuring reCAPTCHA Pro

1. [Sign up for an reCAPTCHA API key pair](https://www.google.com/recaptcha/admin).
2. Open the Craft admin and go to Settings → Plugins → reCAPTCHA Pro → Settings.
3. Add your `site key` and `secret key`, then save.
4. Add the reCAPTCHA template tag to your forms.

## Using reCAPTCHA Pro

Add the following tag to your form where you’d like reCAPTCHA to be displayed.

```twig
{{ craft.recaptchaPro.render({# options #}) }}
```

#### reCAPTCHA v3 Options

```twig
{{ craft.recaptchaPro.render({
    {# The reCAPTCHA version. #}
    version: 3, {# Optional. #}
    trigger: 'onsubmit', {# Optional. Value: onsubmit, onload. Default: onsubmit. #}
    {# reCAPTCHA v3 introduces a new concept: actions. #}
    action: 'submit' {# Optional. #}
}) }}
```

Option | Value | Default | Required | Description
------ | ----- | ------- | -------- | -----------
version | 3 | 3 | - | The reCAPTCHA version.
trigger | onsubmit<br>onload | onsubmit | - |
action | | submit | - | reCAPTCHA v3 introduces a new concept: actions. When you specify an action name in each place you execute reCAPTCHA, you enable the following new features: a detailed break-down of data for your top ten actions in the admin console; adaptive risk analysis based on the context of the action, because abusive behavior can vary.

#### reCAPTCHA v2 Checkbox Options

```twig
{{ craft.recaptchaPro.render({
    {# The reCAPTCHA version. #}
    version: 2, {# Required. #}
    {# The reCAPTCHA HTML element id. #}
    id: 'recaptcha-html-element-id', {# Optional. #}
    {# The color theme of the widget. #}
    theme: 'light', {# Optional. Value: light, dark. Default: light. #}
    {# The size of the widget. #}
    size: 'normal', {# Optional. Value: compact, normal. Default: normal. #}
    {# The tabindex of the widget and challenge. If other elements in your page use tabindex, it should be set to make user navigation easier. #}
    tabindex: 0, {# Optional. #}
    {# The name of your callback function, executed when the user submits a successful response. The g-recaptcha-response token is passed to your callback. #}
    callback: 'callback-function-name', {# Optional. #}
    {# The name of your callback function, executed when the reCAPTCHA response expires and the user needs to re-verify. #}
    expired-callback: 'callback-function-name', {# Optional. #}
    {# The name of your callback function, executed when reCAPTCHA encounters an error (usually network connectivity) and cannot continue until connectivity is restored. If you specify a function here, you are responsible for informing the user that they should retry. #}
    error-callback: 'callback-function-name' {# Optional. #}
}) }}
```

Option | Value | Default | Required | Description
------ | ----- | ------- | -------- | -----------
version | 2 | 2 | + | The reCAPTCHA version.
id | | | - | The reCAPTCHA HTML element id.
theme | light<br>dark | light | - | The color theme of the widget.
size | normal<br>compact | normal | - | The size of the widget.
tabindex | | 0 | - | The tabindex of the widget and challenge. If other elements in your page use tabindex, it should be set to make user navigation easier.
callback | | | - | The name of your callback function, executed when the user submits a successful response. The **g-recaptcha-response** token is passed to your callback.
expired-callback | | | - | The name of your callback function, executed when the reCAPTCHA response expires and the user needs to re-verify.
error-callback | | | - | The name of your callback function, executed when reCAPTCHA encounters an error (usually network connectivity) and cannot continue until connectivity is restored. If you specify a function here, you are responsible for informing the user that they should retry.

#### reCAPTCHA v2 Invisible Options

```twig
{{ craft.recaptchaPro.render({
    {# The reCAPTCHA version. #}
    version: 2, {# Required. #}
    {# Used to create an invisible widget bound to a div and programmatically executed. #}
    invisible: true, {# Required. #}
    {# The reCAPTCHA HTML element id. #}
    id: 'recaptcha-html-element-id', {# Optional. #}
    {# Reposition the reCAPTCHA badge. 'inline' lets you position it with CSS. #}
    badge: 'bottomright', {# Optional. Value: bottomright, bottomleft, inline. Default: bottomright. #}
    {# The tabindex of the challenge. If other elements in your page use tabindex, it should be set to make user navigation easier. #}
    tabindex: 0, {# Optional. #}
    {# The name of your callback function, executed when the user submits a successful response. The g-recaptcha-response token is passed to your callback. #}
    callback: 'callback-function-name', {# Optional. #}
    {# The name of your callback function, executed when the reCAPTCHA response expires and the user needs to re-verify. #}
    expired-callback: 'callback-function-name', {# Optional. #}
    {# The name of your callback function, executed when reCAPTCHA encounters an error (usually network connectivity) and cannot continue until connectivity is restored. If you specify a function here, you are responsible for informing the user that they should retry. #}
    error-callback: 'callback-function-name' {# Optional. #}
}) }}
```

Option | Value | Default | Required | Description
------ | ----- | ------- | -------- | -----------
version | 2 | 2 | + | The reCAPTCHA version.
invisible | true | true | + | Used to create an invisible widget bound to a div and programmatically executed.
id | | | - | The reCAPTCHA HTML element id.
badge | bottomright<br>bottomleft<br>inline | bottomright | - | Reposition the reCAPTCHA badge. 'inline' lets you position it with CSS.
tabindex | | 0 | - | The tabindex of the challenge. If other elements in your page use tabindex, it should be set to make user navigation easier.
callback | | | - | The name of your callback function, executed when the user submits a successful response. The **g-recaptcha-response** token is passed to your callback.
expired-callback | | | - | The name of your callback function, executed when the reCAPTCHA response expires and the user needs to re-verify.
error-callback | | | - | The name of your callback function, executed when reCAPTCHA encounters an error (usually network connectivity) and cannot continue until connectivity is restored. If you specify a function here, you are responsible for informing the user that they should retry.

---

Brought to you by [kerosin](https://github.com/kerosin)
