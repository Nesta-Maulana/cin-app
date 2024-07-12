<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MyAccountController extends Controller
{
    public function edit()
    {
        $data = auth()->user();
        return view('admin.my-account.edit', compact('data'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'name' => 'required',
            'username' => 'required|unique:users,username,' . $user->id,
            'email' => 'required',
            'password' => 'string|min:8|confirmed|nullable'
        ]);

        if (!$data['password']) {
            unset($data['password']);
        }

        $user->update($data);
        alertNotif('update');

        return redirect()->back();
    }
}
