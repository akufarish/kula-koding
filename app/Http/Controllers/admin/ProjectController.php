<?php

namespace App\Http\Controllers\admin;

use App\Models\Project;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Js;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    function Index() {
        $user = Auth::user();

        $project = Project::where("user_id". $user->id)->get();

        return response()->json($project, 200);
    }

    function Show($id) {
        $project = Project::firstWhere("id", $id);

        return response()->json($project, 200);
    }

    function Store(Request $request) {
        $validation = Validator::make($request->all(), [
            
        ]);

        if ($validation->fails()) {
            return response()->json($validation->errors(), 401);
        }

        $user = Auth::user();

        $file = $request->file("image");
        $extension = $file->extension();
        $dir = "storage/project";
        $name = Str::random(32) . "." . $extension;
        $image = $dir . $name;

        $payload = [
            "user_id" => $user->id,
            "nama_project" => $request->nama_project,
            "image" => $image,
            "deskripsi" => $request->deskripsi,
            "github" => $request->github
        ];

        $project = Project::create($payload);

        return response()->json([
            "message" => "Project berhasil diupload!"
        ], 201);
    }

    function Update($id, Request $request) {
        $project = Project::firstWhere("id", $id);

        $project->update($request->all());
        $project->save();

        return response()->json([
            "message" => "Data berhasil diupdate!"
        ], 200);
    }

    function Delete($id) {
        $project = Project::firstWhere("id", $id);

        $project->delete();

        return response()->json([
            "message" => "Data berhasil dihapus"
        ], 200);
    }
}