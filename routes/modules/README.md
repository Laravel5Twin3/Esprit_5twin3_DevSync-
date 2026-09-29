# Routes des modules

Un fichier par module (ex. `coupures.php`), chargé automatiquement par `routes/web.php`.

Exemple de `routes/modules/coupures.php` :

```php
<?php

use App\Http\Controllers\Back\CoupureController as BackCoupureController;
use App\Http\Controllers\Front\CoupureController as FrontCoupureController;
use Illuminate\Support\Facades\Route;

// Front Office
Route::get('/coupures', [FrontCoupureController::class, 'index'])->name('coupures.index');

// Back Office (admin) => admin.coupures.index, admin.coupures.create, ...
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::resource('coupures', BackCoupureController::class);
});
```
