# AjaxResponseBuilder

This PHP package, `takielias/laravel-ajax-builder`, provides a fluent interface for constructing AJAX responses in Laravel applications. It simplifies the creation of JSON responses for AJAX requests by allowing developers to set various response parameters such as status codes, messages, views, data, alerts, and redirect URLs. The core functionality of the package centers around a `AjaxResponseBuilder` class, which encapsulates the logic for building the response.

Key Features:

- **Set Status Code**: Allows setting the HTTP status code for the response.
- **Set Message**: Enables the inclusion of a custom message in the response.
- **Set View**: Supports sending rendered HTML views as part of the response, useful for dynamic content replacement.
- **Set Data**: Facilitates appending data to the response, which can be used to update or manipulate the client-side state.
- **Set Alert**: Provides a way to send alert or notification content that can be displayed to the user.
- **Set Redirect**: Enables specifying a URL to redirect the client to, which can be useful for actions requiring navigation.

The `AjaxResponseBuilder` utilizes Laravel's response helper to return a `JsonResponse` object, ensuring compatibility and ease of use within the Laravel framework. This package is particularly useful for developers looking to streamline their AJAX response handling, making it more readable and maintainable.

## Installation

### Open your terminal and run the following command to add the takielias/laravel-ajax-builder package to your project:

```bash
composer require takielias/laravel-ajax-builder
```

## Usage

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Testing

```bash
composer test
```

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email author@email.com instead of using the issue tracker.

## Credits

- [Author Name][link-author]
- [All Contributors][link-contributors]

## License

MIT. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/takielias/laravel-ajax-builder.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/takielias/laravel-ajax-builder.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/takielias/laravel-ajax-builder/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/12345678/shield

[link-packagist]: https://packagist.org/packages/takielias/laravel-ajax-builder
[link-downloads]: https://packagist.org/packages/takielias/laravel-ajax-builder
[link-travis]: https://travis-ci.org/takielias/laravel-ajax-builder
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/nativebl
[link-contributors]: ../../contributors
