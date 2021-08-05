# model-tracker

## Installation
```shell
composer require duyngha/motrac
```

## How to use

- Add this trait to the model that you want to tracking.

```php
use Duyngha\ModelTracker\Traits\Trackable;

class Task extends Model
{
    use Trackable;
}
```

- Create "trackable" model that extends `Duyngha\ModelTracker\Models\TrackerModel` model.

```php
use Duyngha\ModelTracker\Models\TrackerModel;

class TaskTrackable extends TrackerModel
{
    protected $collection = 'task_revisions'; // This collection property determines name of collection will be created in MongoDB
}
```

Then, assign it for `$trackableModel` property in main model.

```php
use Duyngha\ModelTracker\Traits\Trackable;

class Task extends Model
{
    use Trackable;

    protected $trackableModel = TaskTrackable::class;
}
```

- If you would like to tracking the model on specific fields only. Then add the fields into `$trackingFields` array in main model.

```php
class Task extends Model
{
    protected $trackingFields = [
        'name'
    ];
}
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.