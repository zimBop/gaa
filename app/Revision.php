<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\onlyCurrentScope;

class Revision extends Model
{
    protected $fillable = ['new_id', 'old_id', 'current'];
    
    public $timestamps = false;
    
    protected static function boot()
    {
        parent::boot();

        // static::addGlobalScope(new onlyCurrentScope);
    }
    
    /**
     * Get all revisions for model with ID = $id as array of model IDs
     *
     * @param integer $id
     * @param string $modelClassName
     * @return array
     */
    public static function getAllById($id, $modelClassName)
    {
        $lastRevisionId = static::getLast($id, $modelClassName);
        
        return static::where([
            ['new_id', $lastRevisionId],
            ['model', $modelClassName]
        ])->pluck('old_id')->toArray();
    }

    /**
     * find last revision model ID
     *
     * @param integer $id model ID
     * @param string $modelClassName
     * @return integer
     */
    public static function getLast($id, $modelClassName)
    {
        return static::where([
            ['old_id', $id],
            ['model', $modelClassName]
        ])->max('new_id');
    }
    
    /**
     * get current revision model ID
     *
     * @param integer $id model ID
     * @param string $modelClassName
     * @return integer
     */
    public static function getCurrent($id, $modelClassName)
    {
        $ids = static::getAllById($id, $modelClassName);
        
        return static::whereIn('new_id', $ids)
            ->where([
                ['model', $modelClassName],
                ['current', 1]
            ])
            ->first()
            ->new_id;
    }
    
    /**
     * get all current revision model IDs as array
     *
     * @param string $modelClassName
     * @return array
     */
    public static function getOnlyCurrent($modelClassName)
    {
        return static::select('new_id')
            ->where([
                ['model', $modelClassName],
                ['current', 1]
            ])
            ->groupBy('new_id')
            ->get()
            ->pluck('new_id')
            ->toArray();
    }
    
    /**
     *
     * @param integer $id
     * @param string $modelClassName
     */
    public static function markAsCurrent($id, $modelClassName)
    {
        $ids = static::getAllById($id, $modelClassName);
        static::whereIn('new_id', $ids)
            ->where('model', $modelClassName)
            ->update(['current' => 0]);
        
        static::where([
            ['new_id', $id],
            ['model', $modelClassName]
        ])->update(['current' => 1]);
    }
}
