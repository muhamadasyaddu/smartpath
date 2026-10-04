<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class BantuanController extends Controller
{
    /**
     * Pusat bantuan administrator SmartPath.
     */
    public function index(): View
    {
        abort_unless(
            auth()->check() &&
            auth()->user()->isAdmin(),
            403
        );

        return view('bantuan.index');
    }
}