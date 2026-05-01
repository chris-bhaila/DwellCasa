<?php

namespace App\Http\Controllers;

class AdminController extends Controller
{
    protected function currentLocationId(): ?int
    {
        $user = auth()->user();
        if ($user->hasRole('super_admin')) {
            return session('selected_location_id');
        }
        return $user->location_id;
    }
}