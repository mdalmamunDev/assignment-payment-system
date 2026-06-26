<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Customer;
use App\Services\ProjectService;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;

class ProjectController extends Controller
{
    public function __construct(
        private ProjectService $projectService
    ) {
    }

    public function index()
    {
        $projects = Project::query()
            ->with(['customer'])
            ->when(request('search'), function ($q) {
                $q->where(function ($q) {
                    $q->where('project_name', 'like', '%' . request('search') . '%')
                        ->orWhere('project_code', 'like', '%' . request('search') . '%')
                        ->orWhereHas(
                            'customer',
                            fn($q) =>
                            $q->where('name', 'like', '%' . request('search') . '%')
                        );
                });
            })
            ->when(request('status'), fn($q) => $q->where('status', request('status')))
            ->when(request('customer_id'), fn($q) => $q->where('customer_id', request('customer_id')))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $customers = Customer::where('status', 'active')->get();

        return view('projects.index', compact('projects', 'customers'));
    }

    public function create()
    {
        $customers = Customer::where('status', 'active')->orderBy('name')->get();

        return view('projects.create', compact('customers'));
    }

    public function store(StoreProjectRequest $request)
    {
        $this->projectService->createProject($request->validated());

        return redirect()->route('projects.index')
            ->with('success', 'Project created successfully.');
    }

    public function show(Project $project)
    {
        $project->load(['customer', 'invoices.payments']);

        return view('projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $customers = Customer::where('status', 'active')->orderBy('name')->get();

        return view('projects.edit', compact('project', 'customers'));
    }

    public function update(UpdateProjectRequest $request, Project $project)
    {
        $project->update($request->validated());

        return redirect()->route('projects.index')
            ->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        if ($project->hasInvoices()) {
            return back()->with('error', 'Cannot delete project with existing invoices.');
        }

        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Project deleted successfully.');
    }
}