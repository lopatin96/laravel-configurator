# Install
### Publish config
```php
php artisan vendor:publish --tag="laravel-lang-switcher-config"
```
Add language code and names to the config *config/laravel-lang-switcher.php*:
```json
'languages' => [
  'en' => 'English',
  'ru' => 'русский',
]
```

Include lang-switcher anywhere you want, for example in your footer:
```php
 @include('laravel-lang-switcher::lang-switcher.index')
```

Add LangSwitcher middleware to middleware array in *app/Http/Kernel.php*:
```php
  protected $middleware = [
        …
        \Atin\LaravelLangSwitcher\Http\Middleware\LangSwitcher::class,
    ];
```

# Publishing
### Localization
```php
php artisan vendor:publish --tag="laravel-lang-switcher-lang"
```

### Views
```php
php artisan vendor:publish --tag="laravel-lang-switcher-views"
```

### Config
```php
php artisan vendor:publish --tag="laravel-lang-switcher-config"
```