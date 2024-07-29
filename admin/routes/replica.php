<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;

Route::controller(ReplicaController::class)->group(function () {
    Route::get('/{replica?}', 'index')->name('replica');
        Route::post('/contact', 'replicacontact')->name('replica.contact');
        Route::get('/register/{username}', 'userregistrationForm')->name('replica.registerForm');
        Route::post('/register', 'userRegister')->name('replica.register');
        Route::post('/user/add-payment-receipt', 'addPaymentReceipt')->name('replica.add-payment-receipt');
        Route::get('/state/{username?}', 'state')->name('replica.state');
        Route::post('/checkavailability/{username?}', 'checkEwalletAvailability')->name('replica.check.ewallet');
        Route::post('/epin/availability/{username?}', 'checkEpinAvailability')->name('replica.check.epin');
        Route::get('/check-leg-availability/{sponsorLeg?}/{username?}', 'checkLegAvailability')->name('replica.legAvailability');
        Route::get('/country/{country?}/{username?}', 'getstate')->name('replica.country.state');
        Route::get('/replica/check-dob', 'checkDob')->name('replica.check.dob');
        Route::get('/replica/check-package', 'checkPackage')->name('replica.check.package');
        Route::post('/replica/check-mobile', 'checkMobile')->name('replica.check.mobile');
        Route::post('/replica/check-username', 'checkUsername')->name('replica.check.username');

        //stripe
        Route::post('/stripe-client-secret', 'getClientSecret')->name('replica.stripe');
        Route::post('/stripe', 'stripePost')->name('replica.stripe.post');
});
