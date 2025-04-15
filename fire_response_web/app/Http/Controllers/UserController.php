<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\User;
use App\Models\User as ModelsUser;

class UserController extends Controller
{
    public function update(Request $request, $id)
    {
        $user = ModelsUser::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Update logic here, e.g.
        $user->update($request->all());

        return response()->json($user);
    }
}
