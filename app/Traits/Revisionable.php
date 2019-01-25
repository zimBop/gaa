<?php
namespace App\Traits;

use App\Revision;

trait Revisionable
{
    private $update = false;
    
    public static function bootRevisionable()
    {
        static::created(function ($model) {
            if ($model->update === false) {
                $modelId = $model->getKey();
                Revision::create([
                    'new_id' => $modelId,
                    'old_id' => $modelId,
                    'current' => 1
                ]);
            }
        });
        
        static::deleting(function ($model) {
            $modelId = $model->getKey();
            $modelClass = get_class($model);
            Revision::where([
                    ['new_id', $modelId],
                    ['model', $modelClass]
                ])
                ->orWhere([
                    ['old_id', $modelId],
                    ['model', $modelClass]
                ])
                ->delete();
        });
        
        static::updating(function ($model) {
            $newModel = $model->replicate();
            $newModel->update = true;
            $newModel->save();
            
            $modelClass = get_class($model);
            $modelRevisionIds = Revision::getAllById($model->getKey(), $modelClass);
            $revisionsData = [];
            $newId = $newModel->getKey();
            $modelRevisionIds[] = $newId;
            foreach ($modelRevisionIds as $oldId) {
                $revisionsData[] = [
                    'new_id' => $newId,
                    'old_id' => $oldId,
                    'model'  => $modelClass
                ];
            }
            
            Revision::insert($revisionsData);
            
            return false;
        });
    }
}
