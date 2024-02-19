# Install
### Trait
Add **casts** and **HasConfig** trait to User model

```php

use Atin\LaravelConfigurator\Traits\HasConfig;

class User extends Authenticatable
{
    use HasConfig, …

    protected $casts = [
        …
        'config' => 'object',
    ];
```

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

### Nova
#### Config Resource
```php
<?php

namespace App\Nova;

use App\Enums\ConfigCategory;
use Atin\LaravelConfigurator\Enums\ConfigType;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use function PHPUnit\Framework\matches;

class Config extends Resource
{
    public static $perPageOptions = [
        100,
        200,
    ];

    public static string $model = \Atin\LaravelConfigurator\Models\Config::class;

    public static $title = 'key';

    public static $search = [
        'key',
        'title',
        'value',
        'description',
    ];

    public function fields(NovaRequest $request): array
    {
        return [
            Text::make('Key')->sortable()->readonly(),

            Select::make('Category')->options([
                ConfigCategory::Profiles->value => ConfigCategory::Profiles->value,
            ])
                ->sortable()
                ->readonly(),

            Text::make('Title')
                ->rules('nullable', 'max:64')
                ->sortable()
                ->hideFromIndex(),

            match ($this->type) {
                ConfigType::ArrayOfStrings => Text::make('Value')->displayUsing(fn () => mb_strimwidth($this->value, 0, 50, '…'))->onlyOnIndex(),
                default => Textarea::make('Value')->hide()->hideFromDetail(),
            },

            match ($this->type) {
                ConfigType::Integer => Number::make('Value'),
                ConfigType::Float => Number::make('Value')->step(0.01),
                ConfigType::Boolean => Boolean::make('Value'),
                ConfigType::ArrayOfStrings => Textarea::make('Value')->alwaysShow(),
                default => Text::make('Value')->displayUsing(fn () => mb_strimwidth($this->value, 0, 50, '…')),
            },

            Select::make('Type')->options([
                ConfigType::String->value => ConfigType::String->value,
                ConfigType::Integer->value => ConfigType::Integer->value,
                ConfigType::Float->value => ConfigType::Float->value,
                ConfigType::Boolean->value => ConfigType::Boolean->value,
                ConfigType::ArrayOfStrings->value => ConfigType::ArrayOfStrings->value,
                ConfigType::ArrayOfIntegers->value => ConfigType::ArrayOfIntegers->value,
            ])
                ->sortable(),

            Text::make('Description')
                ->rules('nullable', 'max:256')
                ->hideFromIndex(),
        ];
    }

    public static function authorizedToCreate(Request $request): bool
    {
        return false;
    }

    public function authorizedToDelete(Request $request): bool
    {
        return false;
    }
}
```

#### User Resource
```php
class User extends Resource
{
    …
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()
                ->sortable()
                ->hideFromIndex(),
                
            new Panel('Config', $this->configFields()),
            …
    }
    
    protected function configFields(): array
    {
        return [
            KeyValue::make('Config')->rules('json'),
        ];
    }
    …
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
