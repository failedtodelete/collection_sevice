<?php

// Общий роут на все пути.
Route::get('/{any}', 'AppController@app')->where('any', '.*');
