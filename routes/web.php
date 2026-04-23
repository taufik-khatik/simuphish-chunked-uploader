<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UploadController;

Route::get('/', function () {
    return view('upload');
});

Route::post('/upload-chunk', [UploadController::class, 'uploadChunk']);
Route::post('/upload-complete', [UploadController::class, 'completeUpload']);

Route::get('/thank-you', function () {
    return view('thankyou');
});

Route::get('/test-email', function () {
    $dummyUrl = "https://example.com/dummy-file.pdf";
    $fileName = "dummy-file.pdf";

    \Illuminate\Support\Facades\Mail::to(env('ADMIN_EMAIL'))
        ->send(new \App\Mail\UploadSuccessMail($dummyUrl, $fileName));

    return 'Test email sent!';
});
