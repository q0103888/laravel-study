<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index() {
        $name = "이현호";
        $age = 25;
        return view('test.show', compact('name','age'));
    }
}
