<?php

namespace Panoscape\Assets;

trait HasAssets
{
    /**
     * Get all of the model's assets.
     */
    public function assets()
    {
        return $this->morphToMany(Asset::class, 'item', 'assets_usage', 'item_id', 'asset_id');
    }

    /**
     * Delete all assets associated with this item
     */
    public function deleteAssets()
    {

    }

    /**
     * Attach assets to the model
     *
     * @param array|integer|\Panoscape\Assets\Asset $assets
     * @return void
     */
    public function attachAssets($assets)
    {
        if(is_array($assets)) {
            foreach($assets as $key=>$asset) {
                $this->attachAssets($asset);
            }
        }
        elseif($assets instanceof Asset) {
            $this->assets()->attach($assets->id);
        }
        elseif(is_integer($assets)) {
            $this->assets()->attach($assets);
        }
        else {
            throw new \Exception("Key must be array|integer|\Panoscape\Assets\Asset");
        }
    }

    /**
     * Detach assets from the model
     *
     * @param array|integer|\Panoscape\Assets\Asset $assets
     * @return void
     */
    public function detachAssets($assets)
    {
        if(is_array($assets)) {
            $array = [];
            foreach($assets as $key=>$asset) {
                if($asset instanceof Assets) {
                    array_push($array, $asset->id);
                }
                elseif(is_integer($asset)) {
                    array_push($array, $asset);
                }
                else {
                    throw new \Exception("Key must be integer|\Panoscape\Assets\Asset");
                }
            }
            $this->assets()->detach($array);
        }
        elseif($assets instanceof Assets) {
            $this->assets()->detach($assets->id);
        }
        elseif(is_integer($assets)) {
            $this->assets()->detach($assets);
        }
        else {
            throw new \Exception("Key must be array|integer|\Panoscape\Assets\Asset");
        }
    }

    /**
     * Detach assets from the model
     *
     * @param array|integer|string|\Panoscape\Assets\Asset $assets
     * @param bool $detaching
     * 
     * @return void
     */
    public function syncAssets($assets, $detaching = true)
    {
        $array = [];       
        if(is_array($assets)) {            
            foreach($assets as $key=>$asset) {
                if($asset instanceof Asset) {
                    array_push($array, $asset->id);
                }
                elseif(is_integer($asset)) {
                    array_push($array, $asset);
                }
                else {
                    throw new \Exception("Key must be integer|\Panoscape\Assets\Asset");
                }
            }            
        }
        elseif($assets instanceof Asset) {
            array_push($array, $assets->id);
        }
        elseif(is_integer($assets)) {
            array_push($array, $assets);
        }
        else {
            throw new \Exception("Key must be array|integer|\Panoscape\Assets\Asset");
        }

        if($detaching) {
            $this->assets()->sync($array);
        }
        else {
            $this->assets()->syncWithoutDetaching($array);
        }
    }

    public static function bootHasAssets()
    {
        static::deleted(function($item) {
            $item->deleteAssets();
        });
    }
}