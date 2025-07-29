<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource; 

class UserController extends Controller
{
    /**
     * Display a listing of all users.
     */
    public function index()
    {
        // Get all users and transform them using the resource collection
        return UserResource::collection(User::all());
    }
}
