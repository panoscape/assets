<?php

namespace Panoscape\Assets;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Mimey\MimeTypes;
use DB;

class Asset extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'assets_assets';

    /**
    * Indicates if the model should be timestamped.
    *
    * @var bool
    */
    public $timestamps = false;

    /**
    * The attributes that should be mutated to dates.
    *
    * @var array
    */
    protected $dates = ['created_at'];
     
    /**
    * The attributes that are not mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Get the owner of this asset
     */
    public function owner()
    {
        return $this->morphTo();
    }

    /**
     * Returns whether or not a owner type/id are present.
     *
     * @return bool
     */
    public function hasOwner()
    {
        return !empty($this->owner_type) && !empty($this->owner_id);
    }

    /**
     * Get the items which use this asset
     *
     * @param string $type
     * @return mixed
     */
    public function itemsOf($type)
    {
        return $this->morphedByMany($type, 'item', 'assets_usage', 'asset_id', 'item_id');
    }

    /**
     * Get the url of this asset
     *
     * @return string
     */
    public function url()
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    /**
     * Check if this asset exists
     *
     * @return bool
     */
    public function exists()
    {
        return Storage::disk($this->disk)->exists($this->path);
    }

    /**
     * Check if this asset is attached to any item
     *
     * @return bool
     */
    public function attached()
    {
        return DB::table('assets_usage')->where('asset_id', $this->getKey())->count() > 0;
    }

    /**
     * Remove the associated file from disk
     *
     * @return bool
     */
    public function remove()
    {
        return Storage::disk($this->disk)->delete($this->path);
    }

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        static::deleted(function($asset) {
            $asset->remove();
        });
    }

    /**
     * Get all assets which are not attached
     *
     * @return mixed
     */
    public static function getDetached()
    {
        return static::leftJoin('assets_usage', "assets_assets.id", '=', 'assets_usage.asset_id')->where('assets_usage.asset_id', null)->select("assets_assets.*");
    }

    /**
     * Get all assets which have no owner
     *
     * @return mixed
     */
    public static function getAnonymous()
    {
        return static::where('owner_id', null);
    }

    /**
     * Get MIME type of the given extension
     *
     * @param string $extension
     * @return string
     */
    public static function getMimeType($extension)
    {
        return app(MimeTypes::class)->getMimeType($extension);
    }

    /**
     * Get all MIME types of the given extension
     *
     * @param string $extension
     * @return array
     */
    public static function getAllMimeTypse($extension)
    {
        return app(MimeTypes::class)->getAllMimeTypes($extension);
    }

    /**
     * Create an asset from the request
     *
     * @param string $key
     * @param string $folder
     * @param string $disk
     * @return array
     */
    public static function createFromRequest($key, $folder = '', $disk = 'public')
    {
        if(!request()->hasFile($key)) {
            return null;
        }

        $file = request()->file($key);
        if(!$file->isValid()) {
            return null;
        }

        $user = request()->user();

        if(!is_null($file)) {
            $path = $file->store($folder, $disk);
            if(!empty($path)) {
                return static::create([
                    'owner_id' => is_null($user) ? null : $user->getKey(),
                    'owner_type' => is_null($user) ? null : get_class($user),
                    'disk' => $disk,
                    'path' => $path,
                    'name' => pathinfo($path, PATHINFO_FILENAME),
                    'hash' => pathinfo($path, PATHINFO_FILENAME),
                    'mime' => static::getMimeType($file->extension()),
                    'size' => Storage::disk($disk)->size($path),
                    'created_at' => time()
                ]);
            } 
        }

        return null;
    }

    public static function createFromStorage($path, $disk = 'public')
    {
        if(!Storage::disk($disk)->exists($path)) {
            return null;
        }
        
        $user = request()->user();

        return static::create([
                    'owner_id' => is_null($user) ? null : $user->getKey(),
                    'owner_type' => is_null($user) ? null : get_class($user),
                    'disk' => $disk,
                    'path' => $path,
                    'name' => pathinfo($path, PATHINFO_FILENAME),
                    'hash' => hash_file('md5', config("filesystems.disks.$disk.root")."/$path"),
                    'mime' => static::getMimeType(pathinfo($path, PATHINFO_EXTENSION)),
                    'size' => Storage::disk($disk)->size($path),
                    'created_at' => time()
                ]);
    }
}