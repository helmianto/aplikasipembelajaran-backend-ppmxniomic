<?php

Route::post('/tambahAdmin', 'AdminController@tambahAdmin');
Route::post('/loginAdmin', 'AdminController@loginAdmin');
Route::post('/hapusAdmin', 'AdminController@hapusAdmin');
Route::post('/listAdmin', 'AdminController@listAdmin');

Route::post('/tambahKonten', 'ContentController@tambahKonten');
Route::post('/ubahKonten', 'ContentController@ubahKonten');
Route::post('/hapusKonten', 'ContentController@hapusKonten');
Route::post('/listKonten', 'ContentController@listKonten');
