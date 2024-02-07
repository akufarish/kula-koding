<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    function index() {
        $user = User::all();

        return response()->json([
            "data" => $user
        ], 200);
    }

    function update($id, Request $request) {
        $user = User::firstWhere("id", $id);

        $user->update([
            "isVerified" => $request->isVerified
        ]);

        $user->save();

        return response()->json([
            "message" => "User telah berhasil diverifikasi"
        ], 200);
    }

    function Destroy($id) {
        $user = User::firstWhere("id", $id);

        if ($user) {
            $user->delete();

            return response()->json([
                "message" => "Data berhasil dihapus"
            ], 200);
        }
    }
}