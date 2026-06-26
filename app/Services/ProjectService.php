<?php

namespace App\Services;

use App\Models\Project;

class ProjectService
{
    // format: prj-2026-0001
  public function generateProjectCode(): string
  {
    $year = now()->year;
    $prefix = "PRJ-{$year}-";

    $last = Project::where('project_code', 'like', "{$prefix}%")
      ->orderByDesc('id')
      ->first();

    if ($last) {
      $lastNumber = (int) str_replace($prefix, '', $last->project_code);
      $next = $lastNumber + 1;
    } else {
      $next = 1;
    }

    return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
  }

  public function createProject(array $data): Project
  {
    return Project::create([
      'customer_id' => $data['customer_id'],
      'project_name' => $data['project_name'],
      'project_code' => $this->generateProjectCode(),
      'start_date' => $data['start_date'],
      'deadline' => $data['deadline'] ?? null,
      'budget_amount' => $data['budget_amount'],
      'status' => $data['status'] ?? 'pending',
    ]);
  }
}