<?php

namespace Panoscape\Assets;

trait OwnAssets
{
    /**
     * Get all of the model's assets.
     */
    public function assets()
    {
        return $this->morphMany(Asset::class, 'owner');
    }

    /**
     * Delete all assets owned by this item
     */
    public function deleteAssets()
    {
        
    }

    public static function bootOwnAssets()
    {
        static::deleted(function($owner) {
            $owner->deleteAssets();
        });
    }
}