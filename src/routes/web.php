<?php

use Illuminate\Support\Facades\Route;
use admin\product_return_refunds\Controllers\ReturnRefundManagerController;

Route::name('admin.')->middleware(['web', 'admin.auth'])->group(function () {
    //Return and Refunds
    Route::resource('return_refunds', ReturnRefundManagerController::class) ->only(['index', 'show']);
    Route::post('return_refunds/updateStatus', [ReturnRefundManagerController::class, 'updateStatus'])->name('return_refunds.updateStatus');
});
