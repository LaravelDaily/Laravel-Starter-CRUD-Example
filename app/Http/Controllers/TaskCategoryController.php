<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskCategoryRequest;
use App\Http\Requests\UpdateTaskCategoryRequest;
use App\Models\TaskCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TaskCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $taskCategories = TaskCategory::query()
            ->paginate(10);

        return view('task-categories.index', [
            'taskCategories' => $taskCategories,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('task-categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskCategoryRequest $request): RedirectResponse
    {
        $taskCategory = TaskCategory::create($request->validated());

        return redirect()->route('task-categories.index')->with('status', 'Task category created successfully');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TaskCategory $taskCategory): View
    {
        return view('task-categories.edit', [
            'taskCategory' => $taskCategory,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskCategoryRequest $request, TaskCategory $taskCategory): RedirectResponse
    {
        $taskCategory->update($request->validated());

        return redirect()->route('task-categories.index')->with('status', 'Task category updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TaskCategory $taskCategory): RedirectResponse
    {
        if ($taskCategory->tasks()->exists()) {
            return redirect()->route('task-categories.index')->with('status', 'Task category has tasks and cannot be deleted');
        }

        $taskCategory->delete();

        return redirect()->route('task-categories.index')->with('status', 'Task category deleted successfully');
    }
}
