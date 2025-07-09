<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
    $users = User::all(); // Menampilkan semua akun, termasuk admin dan user
    return view('user', compact('users'));
    }

    public function resetPassword($id)
{
    $user = User::findOrFail($id);
    $user->password = Hash::make('password123'); // Ganti dengan default yang kamu mau
    $user->save();

    return redirect()->route('user.index')->with('success', 'Password berhasil direset ke "password123".');
}
}
