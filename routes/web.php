<?php

use App\Http\Controllers\AttributeController;
use App\Http\Controllers\Config\DeleteVendorApiController;
use App\Http\Controllers\Entity\widgetController;
use App\Http\Controllers\initialization\indexController;
use App\Http\Controllers\Setting\AccessController;
use App\Http\Controllers\Setting\CreateAuthTokenController;
use App\Http\Controllers\Setting\testController;
use Illuminate\Support\Facades\Route;


Route::get('/', [indexController::class, 'initialization']);
Route::get('/{accountId}/', [indexController::class, 'index'])->name('main');


Route::get('/test/SessionService/{accountId}', [testController::class, 'testController']);


Route::get('/Setting/createAuthToken/{accountId}', [CreateAuthTokenController::class, 'getCreateAuthToken'])->name('getCreateAuthToken');
Route::post('/Setting/createAuthToken/{accountId}', [CreateAuthTokenController::class, 'postCreateAuthToken']);

Route::get('/Setting/Worker/{accountId}', [AccessController::class, 'getWorker'])->name('getWorker');
Route::post('/Setting/Worker/{accountId}', [AccessController::class, 'postWorker']);

Route::get('/widget/{object}', [widgetController::class, 'widgetObject']);
Route::get('/widget/Info/Attributes', [widgetController::class, 'widgetInfoAttributes']);


Route::get('delete/{accountId}/', [DeleteVendorApiController::class, 'delete']);
Route::get('setAttributes/{accountId}/{tokenMs}', [AttributeController::class, 'setAllAttributesVendor']);
//для админа
Route::get('/web/getPersonalInformation/', [collectionOfPersonalController::class, 'getPersonal']);
Route::get('/collectionOfPersonalInformation/{accountId}/', [collectionOfPersonalController::class, 'getCollection']);
