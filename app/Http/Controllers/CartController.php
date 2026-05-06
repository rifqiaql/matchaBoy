<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(): View
    {
        return view('keranjang.index');
    }

    public function create(): View
    {
        return view('keranjang.create');
    }

    public function store(): RedirectResponse
    {
        return redirect()->route('keranjang.index');
    }

    public function destroy(): RedirectResponse
    {
        return redirect()->route('keranjang.index');
    }
}
