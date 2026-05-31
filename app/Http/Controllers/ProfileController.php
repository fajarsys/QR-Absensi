<?php

namespace App\Http\Controllers;

use App\Services\ProfileService;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Support\Facades\Auth; 

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user(); 

        return view('user.profile', compact('user')); 
    }

   public function edit() 
    {
        $user = Auth::user();

        return view('user.profile_edit', compact('user'));
    }

    public function update(UpdateProfileRequest $request,ProfileService $service)
    
    {
        $user = Auth::user();

        $service->updateProfile($user, $request->validated());

        // Kembalikan ke halaman profil dengan pesan sukses
        return redirect()->route('profile.index')->with('success', 'Profil berhasil diperbarui!');
    }
}