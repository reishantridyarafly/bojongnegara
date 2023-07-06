<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PengaturanController extends Controller
{
    public function profile()
    {
        return view('backend.pengaturan.profile');
    }

    public function updateProfile(Request $request)
    {
        $id = $request->id;
        $validated = Validator::make(
            $request->all(),
            [
                'name' => 'required|string',
                'email' => 'required|string|unique:users,email,' . $id,
                'foto' => 'image|mimes:jpg,png,jpeg,webp,svg',
                'no_telepon' => 'required',
            ],
            [
                'name.required' => 'Silakan isi nama terlebih dahulu!',
                'email.required' => 'Silakan isi email terlebih dahulu!',
                'email.unique' => 'Email sudah tersedia!',
                'foto.image' => 'File harus berupa gambar!, ',
                'foto.mimes' => 'Gambar yang diunggah harus dalam format JPG, PNG, JPEG, WEBP, atau SVG.',
                'no_telepon.required' => 'Silakan isi no telepon terlebih dahulu!',
            ]
        );

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()]);
        } else {
            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                if ($file->isValid()) {
                    $guessExtension = $request->file('foto')->guessExtension();
                    $request->file('foto')->storeAs('avatar/', $request->name . '.' . $guessExtension, 'public');

                    $user = User::findOrFail($id);

                    if (Storage::exists('users/' . $user->image)) {
                        Storage::delete('users/' . $user->image);
                    }

                    $user->name = $request->name;
                    $user->email = $request->email;
                    $user->no_telepon = $request->no_telepon;
                    $user->avatar = $request->name . '.' . $guessExtension;
                    $user->save();

                    return response()->json(['success' => 'Data barhasil diperbarui']);
                }
            } else {
                $user = User::findOrFail($id);

                $user->name = $request->name;
                $user->email = $request->email;
                $user->no_telepon = $request->no_telepon;
                $user->save();

                return response()->json(['success' => 'Data barhasil diperbarui']);
            }
        }
    }
}