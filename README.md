# model-tracker

## Installation
```shell
composer require duyngha/model-tracker
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