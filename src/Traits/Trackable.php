<?php

namespace Duyngha\ModelTracker\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Jenssegers\Mongodb\Eloquent\Builder;

trait Trackable
{
    private array $oldData = [];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::saving(function ($model) {
            $model->prepareTrackingData();
        });

        static::saved(function ($model) {
            $model->tracking($model);
        });
    }

    /**
     * Storing original data
     * 
     * @return void
     */
    private function prepareTrackingData(): void
    {
        $this->oldData = $this->original;
    }

    /**
     * Create new records for the updating occured on the model
     * 
     * @param  \Illuminate\Database\Eloquent\Model $model Specific instance of a model
     * 
     * @return void
     */
    private function tracking($model): void
    {
        // mapping a new data for adjustment of each field into an array
        $data = $this->mappingData();

        // saving new data to mongodb if it is not empty
        if (empty($data) == false) {
            $this->getTrackableInstance()->insert($data);
        }
    }

    /**
     * Mapping new data before use it for saving process
     * 
     * @return array
     */
    private function mappingData(): array
    {
        return $this->filterTrackingFields()->map(function ($value, $field) {
            return [
                'model_id' => $this->oldData['id'],
                'field' => $field,
                'old' => $this->oldData[$field],
                'new' => $value,
                'change_time' => now()->toDateTimeString()
            ];
        })->values()->all();
    }

    /**
     * Only tracking on selective fields
     * 
     * @return \Illuminate\Support\Collection
     */
    private function filterTrackingFields(): Collection
    {
        $modelChanges = collect($this->getDirty());

        if (empty($this->trackingFields)) {
            return $modelChanges;
        }

        // if trackingFields array is set (not empty) then only collect data for those fields
        return $modelChanges->filter(function ($value, $field) {
            return in_array($field, $this->trackingFields);
        });
    }

    /**
     * Get coressponding trackable instance for each model
     * 
     * @return Jenssegers\Mongodb\Eloquent\Builder
     *
     * @throws \Exception
     */
    private function getTrackableInstance(): Builder
    {
        if ($this->trackableModel === null || $this->trackableModel == '') {
            throw new \Exception("The " . self::class . " model has not assigned for a trackable model.");
        }
        
        if (class_exists($this->trackableModel) === false) {
            throw new \Exception("The {$this->trackableModel} does not exist");
        }

        return (new $this->trackableModel)::on('mongodbsrv');
    }
}