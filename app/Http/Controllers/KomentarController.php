<?php

namespace App\Http\Controllers;

use App\Http\Requests\KomentarRequest;
use App\Models\Komentar;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KomentarController extends Controller
{
    function Show($id) {
        $komentar = Komentar::where("project_id", $id)->get();

        if ($komentar) {
            return response()->json([
                "message" => "Komentar ditemukan",
                "komentar" => $komentar
            ], 200);
        } else {
            return response()->json([
                "message" => "Tidak memiliki komentar"
            ], 404);
        }
    }

    function Store($id, KomentarRequest $request) {
        $user = Auth::user();
        $project = Project::firstWhere("id", $id);

        $payload = $request->validated();

        $payload["user_id"] = $user->id;
        $payload["project_id"] = $project->id;

        Komentar::create($payload);

        return response()->json([
            "message" => "Komentar berhasil dibuat"
        ], 201);
    }

    function Destroy($id, Komentar $komentar) {
        $project = Project::firstWhere("id", $id);
        $user = Auth::user();

        if ($komentar->user_id == $user) {
            $komentar->delete();
        } else {
            return response()->json([
                "message" => "Anda tidak memiliki akses"
            ], 401);
        }
    }
}