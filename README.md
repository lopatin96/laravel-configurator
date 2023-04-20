# Install
### Enums

Create two enums in *app/Enums* folder:
```php
<?php

namespace App\Enums;

enum ConfigKey: string
{
    case TestKey = 'TestKey';
    case TestKeyPro = 'TestKeyPro';
}
```

and

```php
<?php

namespace App\Enums;

enum ConfigCategory: string
{
    case TestCategory = 'TestCategory';
}
```

# Usage
```php
use App\Enums\ConfigKey;
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