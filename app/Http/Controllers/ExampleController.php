<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExampleController extends Controller
{
    public function homepage()
    {
        $myName = 'Joe';
        $animals = ['cat', 'dog', 'fish'];

        return view('homepage', [
            'allAnimals' => $animals, 'name' => $myName, 'catName' => 'Garfield']);
    }

    public function about()
    {
        return view('single-post');
    }
}
