# Assets
Assets/Resource storage managment for Laravel

## Installation

### Composer

```shell
composer require panoscape/assets
```

### Service provider

> config/app.php

```php
'providers' => [
    ...
    Panoscape\Assets\AssetsServiceProvider::class,
];
```

### Asset

> config/app.php

```php
'aliases' => [
    ...
    'App\Asset' => Panoscape\Assets\Asset::class,
];
```

### Migration

```shell
php artisan vendor:publish --provider="Panoscape\Assets\AssetsServiceProvider" --tag=migrations
```

## Usage

Add `OwnAssets` trait to user model.

```php
<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Panoscape\Assets\OwnAssets;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes, OwnAssets;
}
```

Add `HasAssets` trait to the model that uses assets.

```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Panoscape\Assets\HasAssets;

class Article extends Model
{
    use HasAssets;
}
```

### Get assets owned by a user

```php
$user->assets();
//or dynamic property
$user->assets;
```

### Get assets used by a model

```php
$model->assets();
//or dynamic property
$model->assets;
```

### Asset

```php
//get the owner
$asset->owner();
//check owner's existence
$asset->hasOwner();

//get items of a specific type which use this asset
$asset->itemsOf(App\Article::class);

//get url
$asset->url();

//check file existence
$asset->exists();

//check if this asset is attached to any items
$asset->attached();

//remove the asset's related file
$asset->remove();

//get all assets which are not attached
Asset::getDetached();

//get all assets which have no owner
Asset::getAnonymous();

//get MIME type of the given extension
Asset::getMimeType('jpg');
//get all MIME types of the given extension
Asset::getAllMimeTypse('jpg');

//create asset from request
//the first argument is the key of the file in the request
//the second argument is the folder path(relative to the disk) in which the file will be stored, which if optional
//the third argument is the disk to store in, which by default is 'public'
Asset::createFromRequest('avatar', 'avatars', 'public');

//create asset from storage
//the first argument is the file path relative to the disk
//the second argument is the disk to store in, which by default is 'public'
Asset::createFromStorage('avatars/avatar_927.png', 'public');

```

#### Attach/Detach/Sync

```php
//by id
$article->attachAssets(1);
//by model instance
$article->attachAssets($asset);
//with array
$article->attachAssets([1, 2]);
$article->attachAssets([$asset1, $asset2]);

//detach
$article->detachAssets(1);
//detach all
$article->detachAssets([]);

//sync
$article->syncAssets(1);
//detach all
$article->syncAssets([]);
//sync without detaching
$article->syncAssets([1, 2], false);

```