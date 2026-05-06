<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(): View
    {
        return view('inventory.index');
    }

    public function create(): View
    {
        return view('inventory.create');
    }

    public function store(): RedirectResponse
    {
        return redirect()->route('inventory.index');
    }
}
