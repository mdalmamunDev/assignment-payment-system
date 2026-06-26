<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Customer;
use App\Services\ProjectService;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;

class ProjectController extends Controller
{
    public function __construct(private ProjectService $projectService) {}

    public function index()
    {
        $projects = Project::with('customer')
            ->when(request('name'), fn($q) => $q->where(function($q) {
                $q->where('project_name', 'like', '%' . request('name') . '%')
                  ->orWhere('project_code', 'like', '%' . request('name') . '%')
                  ->orWhereHas('customer', fn($q) =>
                      $q->where('name', 'like', '%' . request('name') . '%')
                  );
            }))
            ->when(request('status'), fn($q) => $q->where('status', request('status')))
            ->when(request('customer_id'), fn($q) => $q->where('customer_id', request('customer_id')))
            ->latest()
            ->paginate(request('per_page', 15));

        return retRes('', $projects, 2000);
    }

    public function store(StoreProjectRequest $request)
    {
        $this->projectService->createProject($request->validated());

        return retRes('Project created successfully.', null, 2000);
    }

    public function show(Project $project)
    {
        $project->load(['customer', 'invoices']);

        return retRes('', $project, 2000);
    }

    public function update(UpdateProjectRequest $request, Project $project)
    {
        $project->update($request->validated());

        return retRes('Project updated successfully.', null, 2000);
    }

    public function destroy(Project $project)
    {
        if ($project->hasInvoices()) {
            return retRes('Cannot delete project with existing invoices.', null, 4000);
        }

        $project->delete();

        return retRes('Project deleted successfully.', null, 2000);
    }
}