# Easy way of adding PostHog Experiments to your Laravel application.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/carandclassic/posthog-experiments.svg?style=flat-square)](https://packagist.org/packages/carandclassic/posthog-experiments)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/carandclassic/posthog-experiments/run-tests?label=tests)](https://github.com/carandclassic/posthog-experiments/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/carandclassic/posthog-experiments/Fix%20PHP%20code%20style%20issues?label=code%20style)](https://github.com/carandclassic/posthog-experiments/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/carandclassic/posthog-experiments.svg?style=flat-square)](https://packagist.org/packages/carandclassic/posthog-experiments)

## Installation

You can install the package via composer:

```bash
composer require carandclassic/posthog-experiments
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="posthog-experiments-config"
```

## Usage

This package is for integrating [PostHog Experiments](https://posthog.com/manual/experimentation). Before you start you will need to set up a PostHog Experiment. You can do that by following the instructions in the [PostHog Experiments Manual](https://posthog.com/manual/experimentation).

Once you have a PostHog Experiment set up, you can use the `<x-posthog-experiment>` Blade component. For example:

```html
<x-posthog-experiment experiment="experiment-feature-key">
    <x-slot name="control">
        <a href="/control">Try the control</a>
    </x-slot>

    <x-slot name="test_a">
        <a href="/test-a">Try test A</a>
    </x-slot>

    <x-slot name="test_b">
        <a href="/test-b">Try test B</a>
    </x-slot>

    <a href="/fallback">Show fallback</a>
</x-posthog-experiment>
```

Here we have an experiment that can be broken down to:

| Attribute | Description | Required |
| --- | --- | --- |
| experiment | The Feature flag key | ✅ |
| participant | A unique distinct ID* |  |
| x-slot | The slots are for the variants. Each variant get’s it’s own slot, for example if you have three variants, control, blue_button and red_button you would need three slots, one for each variant. The code that is in the slot will be shown | Not necessary but you should add at least one. |
| fallback | Any code that is in the same nesting as the slots will be used as a fallback. If a fallback isn’t present, the component will look for a control slot, if there is no control slot, then an empty string is returned and nothing is shown to the user. |  |

You can also provide an `override` by adding a `posthog` query parameter that matches a variant. For example `https://your-cool-site.com?posthog=test_b`.

* The `participant` unique distinct ID is not required, if one is not passed in the method will check if the user is logged in and use the logged in users id. If the user is not logged in, the method will try get the Laravel session and if the session is not set (being in a private/incognito window for example) the method will return an empty string which will then let the fallback or control be shown (depending on how the component is being used.) The `$participant` variable is anonymised so that we do not send any form of personal identifiable info to PostHog, this also adds a layer of security by not sending information that can be intercepted and changed by the user.

You can also use the `PosthogExperiments` alias to get access to helpful static methods. For example:

```php
PosthogExperiments::getFeatureFlag('experiment-feature-key');
```

The `PosthogExperiments` alias has the below static methods.

## getFeatureFlag

| Attribute | Description | Required |
| --- | --- | --- |
| experiment | (string) The feature flag key | ✅ |
| participant | (string\|int) The unique distinct ID |  |
| override | (string) The feature flag to always return |  |

This method retrieves the feature flag based on the feature flag key and the unique distinct ID. It also takes in an `override` where when testing you can set the feature flag to always return a specific value.

The `$participant` variable is not required, if one is not passed in the method will check if the user is logged in and use the logged in users id. If the user is not logged in, the method will try get the Laravel session and if the session is not set (being in a private/incognito window for example) the method will return an empty string which will then let the fallback or control be shown (depending on how the component is being used.) The `$participant` variable is anonymised so that we do not send any form of personal identifiable info to PostHog, this also adds a layer of security by not sending information that can be intercepted and changed by the user.

Once a feature flag has been retrieved the `[SendFeatureFlagCalledJob](https://www.notion.so/PostHog-Experiments-Integration-25a2c6c5c1964da7b68a803157119f08)` job is called to track the feature flag of the participant.

## hasFeatureFlag

| Attribute | Description | Required |
| --- | --- | --- |
| experiment | (string) The feature flag key | ✅ |
| featureFlag | (string\|array) The flag(s) to check | ✅ |
| participant | (string\|int) The unique distinct ID |  |

This method helps with checking whether the feature flag that is being used is a specific one or is one of a couple options. `experiment` and `participant` is needed to get the correct feature flag and `featureFlag` is to test against. The `featureFlag` can be a string on array so that more feature flags can be checked against. This is helpful if you would like to have a form request be required for a specific field if two of three feature flags are set. You could then use something like:

```php
public function rules(): array
{
    return [
        'new_feature' => Rule::when(PosthogExperiment::hasFeatureFlag(
            'experiment-feature-key',
            ['test_a', 'test_b']
        ), ['required']),
    ];
}
```

The `$participant` variable is not required, if one is not passed in the method will check if the user is logged in and use the logged in users id. If the user is not logged int, the method will try get the Laravel session and if the session is not set (being in a private/incognito window for example) false will be returned.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
