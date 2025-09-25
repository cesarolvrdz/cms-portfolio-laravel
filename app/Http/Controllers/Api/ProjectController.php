<?php
namespace App\Http\Controllers\Api;

use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::query();
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        $projects = $query->with('techTags')->paginate(10);
        return response()->json($projects);
    }

    public function show($id)
    {
        $project = Project::with('techTags')->findOrFail($id);
        return response()->json($project);
    }

    public function store(Request $request)
    {
        // Validación y autorización
        $project = Project::create($request->all());
        $project->techTags()->sync($request->tech_tags ?? []);
        return response()->json($project, 201);
    }

    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        $project->update($request->all());
        $project->techTags()->sync($request->tech_tags ?? []);
        return response()->json($project);
    }

    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        $project->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
