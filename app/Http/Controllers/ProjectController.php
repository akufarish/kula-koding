<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectRequest;
use App\Models\Image;
use App\Models\Komentar;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Js;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    function index() {
        $projects = Project::with('image')->get();
    
        return response()->json([
            'data' => $projects,
            'status' => 'success',
            'message' => 'Data berhasil diambil',
        ], 200);
    }    

    function Show($id) {
        $project = Project::firstWhere("id", $id);

        if ($project) {
            return response()->json($project, 200);
        } else {
            return response()->json([
                "message" => "Data tidak ditemukan"
            ], 404);
        }

    }

    function Store(ProjectRequest $request)
    {
        try {
            $user = Auth::user();

            $payload = $request->validated();
            $payload["user_id"] = $user->id;

            $project = Project::create($payload);

            $images = [];

            foreach ($request->file("image") as $uploadedImage) {
                $extension = $uploadedImage->extension();
                $dir = "storage/project/";
                $name = Str::random(32) . "." . $extension;
                $foto = $dir . $name;
                $uploadedImage->move($dir, $name);

                Image::create([
                    "project_id" => $project->id,
                    "image" => $foto,
                ]);
            }

            return response()->json([
                "message" => "Project berhasil diupload!",
                "images" => $images,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Terjadi kesalahan saat mengunggah proyek.",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    function Update($id, ProjectRequest $request) {
        $user = Auth::user();
        $project = Project::firstWhere("id", $id);

        $payload = $request->validated();

        if ($project) {
            if ($project->user_id == $user->id) {
                $project->update($payload);
                        
                return response()->json([
                    "message" => "Data berhasil diupdate!"
                ], 200);
            } else {
                return response()->json([
                    "message" => "Anda tidak memiliki akses untuk mengubah data ini!"
                ], 401);
            }
        } else {
            return response()->json([
                "message" => "Data tidak ditemukan"
            ], 404);
        }
    }

    function Destroy($id) {
        $user = Auth::user();
        $project = Project::firstWhere("id", $id);

        if ($project->user_id == $user->id) {
            $komentar = Komentar::where("project_id", $id);
            $komentar->delete();
            $project->delete();
    
            return response()->json([
                "message" => "Data berhasil dihapus"
            ], 200);
        } else {
            return response()->json([
                "message" => "Anda tidak memiliki akses untuk mengubah data ini!"
            ], 401);
        }
    }
}
