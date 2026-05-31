<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewUseerRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Menampilkan daftar pengguna
    public function adminIndex()
    {
        $users = User::all();
        return view('admin.data_karyawan.index', compact('users'));
    }

    //Menampilkan form tambah pengguna
    public function adminCreate()
    {
        return view('admin.data_karyawan.create');
    }

    //menyimpan data users di admin 
    public function adminStore(UserRequest $request, UserService $service) {
        
        $service->createUser($request->validated());

        return redirect()->route('admin.users.adminIndex')
                         ->with('success', 'Karyawan baru berhasil ditambahkan.');

    }

    /**
     * Prints the user details.
     * @param int $id
    */
    //edit pengguna
    public function adminEdit($id) {
        $user = User::findOrfail($id);
        return view('admin.data_karyawan.edit', compact ('user'));
    }

    /**
     * Prints the user details.
     * @param int $id
    */
    //update pengguna
    public function adminUpdate(UpdateUserRequest $request, UserService $service, $id) {
        $user = User::findOrfail($id);
        
        $service->updateUser($user, $request->validated());

        return redirect()->route('admin.users.adminIndex')
                         ->with('success', 'Data karyawan berhasil diperbarui.');
    
    }

    /**
     * Prints the user details.
     * @param int $id
    */
    //Menghapus pengguna
    public function adminDestroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('admin.users.adminIndex')->with('success', 'User berhasil dihapus');
    }

    // Menyimpan pengguna baru
    public function store(NewUseerRequest $request)
    {
        $data = $request->validated();
        
        $data['password'] = bcrypt($data['password']);
        User::create($data);
        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan');
    }

    /**
     * Prints the user details.
     * @param int $id
    */
    // Menampilkan profil satu pengguna
    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('users.show', compact('user'));
    }

    /**
     * Prints the user details.
     * @param int $id
    */
    // Menampilkan form edit
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('users.edit', compact('user'));
    }

    /**
     * Prints the user details.
     * @param int $id
    */
    // Memperbarui data pengguna
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
        ]);
        
        $user->update($data);
        return redirect()->route('users.index')->with('success', 'User berhasil diupdate');
    }
    
}
