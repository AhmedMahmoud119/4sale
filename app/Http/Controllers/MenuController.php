<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Meal;

class MenuController extends Controller
{
    public function listMenuItems()
    {
        $menuItems = Meal::paginate(10);

        return response()->json([
            'menu_items' => $menuItems
        ], 200);
    }
}
