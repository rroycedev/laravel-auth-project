<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class WelcomeController extends Controller
{
    public function index()
    {
        $user = Auth::User();

        if ($user) {
            $user->GetRoles();
        }

        return view('welcome');
    }
}
