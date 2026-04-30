<?php

namespace App\Traits;

use App\Helpers\ActivityLogger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait LogsActivity
{
    /**
     * Boot the trait to listen to eloquent events.
     */
    protected static function bootLogsActivity()
    {
        static::created(function (Model $model) {
            $modelName = class_basename($model);
            $description = "Membuat data {$modelName} baru (ID: {$model->id}).";
            ActivityLogger::log('Create', $description, $model);
        });

        static::updated(function (Model $model) {
            $modelName = class_basename($model);
            
            // Generate description comparing old and new values
            $changes = $model->getChanges();
            $original = $model->getOriginal();
            
            $ignoredColumns = ['updated_at', 'remember_token'];
            $changeDetails = [];

            foreach ($changes as $key => $newValue) {
                if (!in_array($key, $ignoredColumns)) {
                    $oldValue = $original[$key] ?? 'null';
                    $changeDetails[] = "{$key}: '{$oldValue}' ➔ '{$newValue}'";
                }
            }

            if (!empty($changeDetails)) {
                $description = "Memperbarui data {$modelName} (ID: {$model->id}). Perubahan: " . implode(', ', $changeDetails);
                ActivityLogger::log('Update', Str::limit($description, 60000), $model);
            }
        });

        static::deleted(function (Model $model) {
            $modelName = class_basename($model);
            $description = "Menghapus data {$modelName} (ID: {$model->id}).";
            ActivityLogger::log('Delete', $description, $model);
        });
    }
}
