<?php

namespace App\Http\Controllers;

class AppController extends Controller
{

    /**
     * Отображение общей статичесткой страницы JS приложения.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function app()
    {
        // Отображение статичной страницы JS приложения.
        return view('app');
    }

}
