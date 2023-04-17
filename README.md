# Usage
```php
use Atin\LaravelConfigurator\Enums\ConfigKey;
use Atin\LaravelConfigurator\Helpers\ConfiguratorHelper;

class DashboardController extends Controller
{
    public function index()
    {
        $value = ConfiguratorHelper::getLimitedValue(ConfigKey::TestKey);
        …
    }
}
```

# Publishing
### Migrations
```php
php artisan vendor:publish --tag="laravel-configurator-migrations"
```

### Config
```php
php artisan vendor:publish --tag="laravel-configurator-config"
```