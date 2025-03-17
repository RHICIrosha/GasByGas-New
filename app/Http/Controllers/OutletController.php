<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use Illuminate\Http\Request;

class OutletController extends Controller
{
    public function findOutlets()
    {
        // Get all outlets from the database
        $outlets = Outlet::all();

        // Add latitude and longitude if they don't exist
        // In production, these would be stored in your database
        foreach ($outlets as $outlet) {
            if (!isset($outlet->latitude) || !isset($outlet->longitude)) {
                // You'd want to add real coordinates in a production environment
                // This is just for demonstration
                $outlet->latitude = null;
                $outlet->longitude = null;
            }
        }

        return view('customer.find_outlets', compact('outlets'));
    }
}
