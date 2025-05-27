# LaravelDaily Starter Kit Demo Application

---

## About

This project aims to showcase the starter kit and show you how we created this project.

---

## Creating Project and First CRUD - Task Categories

To start with, we have created a new project using the following:

```bash
laravel new Laravel-Starter-CRUD-Example --using=laraveldaily/starter-kit
```

This installed the first commit to our repository.

From there, we'll create two CRUDs:

- Tasks
- Task Categories

Let's start with Task Categories:

```bash
php artisan make:model TaskCategory -mc
php artisan make:request StoreTaskCategoryRequest
php artisan make:request UpdateTaskCategoryRequest
```

This generated these files:

- Model
- Migration
- Controller
- Store/Update requests

Fill them based on the repository code.

From there, we will register the sidebar navigation. But this time, we want it to be in 2 levels like this:

- Task Categories
    - List
    - Create

To do that, we'll have to create another component based on our [raw HTML design](<https://github.com/LaravelDaily/starter-kit/wiki/Design-Examples-(Raw-Files)#dashboard>):

**resources/views/components/layouts/sidebar-two-level-link-parent.blade.php**

```blade
@props(["active" => false, "title" => "", "icon" => "fas-list"])

<li x-data="{ open: {{ $active ? "true" : "false" }} }">
    <button @click="open = !open" @class([
        "flex items-center justify-between w-full px-3 py-2 text-sm rounded-md hover:bg-sidebar-accent hover:text-sidebar-accent-foreground",
        "bg-sidebar-accent text-sidebar-accent-foreground font-medium" => $active,
        "hover:bg-sidebar-accent hover:text-sidebar-accent-foreground text-sidebar-foreground" => !$active,
    ])>
        <div class="flex items-center">
            @svg($icon, $active ? "w-5 h-5 text-white" : "w-5 h-5 text-gray-500")
            <span
                :class="{ 'opacity-0 hidden ml-0': !sidebarOpen, 'ml-3': sidebarOpen }"
                class="transition-opacity duration-300">{{ $title }}</span>
        </div>
        <svg xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4 transition-transform"
            :class="{ 'rotate-90': open, 'opacity-0': !sidebarOpen }"
            fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 5l7 7-7 7" />
        </svg>
    </button>

    <!-- Level 2 submenu -->
    <div x-show="open && sidebarOpen" class="mt-1 ml-4 space-y-1">
        {{ $slot }}
    </div>
</li>
```

And child component:

**resources/views/components/layouts/sidebar-two-level-link.blade.php**

```blade
@props(["active" => false, "href" => "#", "icon" => "fas-house"])

<a href="{{ $href }}" @class([
    "flex items-center px-3 py-2 text-sm rounded-md transition-colors duration-200",
    "bg-sidebar-accent text-sidebar-accent-foreground font-medium" => $active,
    "hover:bg-sidebar-accent hover:text-sidebar-accent-foreground text-sidebar-foreground" => !$active,
])>
    <div class="flex items-center">
        @svg($icon, $active ? "w-5 h-5 mr-3 text-white" : "w-5 h-5 mr-3 text-gray-500")
        <span>{{ $slot }}</span>
    </div>
</a>
```

Then, we can create our Sidebar menu items like this:

**resources/views/components/layouts/app/sidebar.blade.php**

```blade
<x-layouts.sidebar-two-level-link-parent :active="request()->routeIs("task-categories*")"
    title="Task Categories" icon='fas-folder'>
    <x-layouts.sidebar-two-level-link href="{{ route("task-categories.index") }}"
        icon='fas-list' :active="request()->routeIs("task-categories.index")">Task Categories
        List</x-layouts.sidebar-link>
        <x-layouts.sidebar-two-level-link
            href="{{ route("task-categories.create") }}" icon='fas-plus'
            :active="request()->routeIs("task-categories.create")">Create New Task
            Category</x-layouts.sidebar-link>
</x-layouts.sidebar-two-level-link-parent>
```

Next, we can create our Index view using [the tables raw HTML template](<https://github.com/LaravelDaily/starter-kit/wiki/Design-Examples-(Raw-Files)#tables>).

The only difference was that we had to create our own pagination component. Let's publish the default templates:

```bash
php artisan vendor:publish --tag=laravel-pagination
```

And then replace the **resources/views/vendor/pagination/tailwind.blade.php** content with our component:

```blade
@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __("Pagination Navigation") }}"
        class="flex items-center justify-between">
        <div class="flex items-center">
            <p class="text-sm text-gray-700 dark:text-gray-300">
                {{ __("Showing") }}
                <span class="font-medium">{{ $paginator->firstItem() }}</span>
                {{ __("to") }}
                <span class="font-medium">{{ $paginator->lastItem() }}</span>
                {{ __("of") }}
                <span class="font-medium">{{ $paginator->total() }}</span>
                {{ __("results") }}
            </p>
        </div>
        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
            aria-label="Pagination">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span aria-disabled="true"
                    aria-label="{{ __("pagination.previous") }}"
                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-400 cursor-default">
                    <span class="sr-only">Previous</span>
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20" fill="currentColor"
                        aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                            clip-rule="evenodd" />
                    </svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <span class="sr-only">Previous</span>
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20" fill="currentColor"
                        aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                            clip-rule="evenodd" />
                    </svg>
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span aria-disabled="true"
                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm font-medium text-gray-700 dark:text-gray-300">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <a href="#"
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 bg-blue-50 dark:bg-blue-900/30 text-sm font-medium text-blue-600 dark:text-blue-400">{{ $page }}</a>
                        @else
                            <a href="{{ $url }}"
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <span class="sr-only">Next</span>
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20" fill="currentColor"
                        aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                            clip-rule="evenodd" />
                    </svg>
                </a>
            @else
                <span aria-disabled="true"
                    aria-label="{{ __("pagination.next") }}"
                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-400 cursor-default">
                    <span class="sr-only">Next</span>
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20" fill="currentColor"
                        aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                            clip-rule="evenodd" />
                    </svg>
                </span>
            @endif
        </nav>
    </nav>
@endif
```

From there, we implemented the Create/Edit forms following our Profile design.

---

## Creating Tasks CRUD

To create our Tasks CRUD, we will do the same type of command:

```bash
php artisan make:model Task -mcf
php artisan make:request StoreTaskRequest
php artisan make:request UpdateTaskRequest
php artisan make:migration create_task_task_category_table
```

Two differences here:

- We will create a Factory using the `f` in `-mcf` flag
- We will create a many-to-many pivot table

Then, fill all of them based on the repository code.

The last thing to edit - add Seeder for tasks:

**database/seeders/DatabaseSeeder.php**

```php
namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Task; // <-- Add this line

class DatabaseSeeder extends Seeder
{
    /**
 * Seed the application's database.
 */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        Task::factory(100)->create(); // <-- Add this line
    }
}
```

Then we need to register a two-level menu, too:

```blade
<x-layouts.sidebar-link href="{{ route("dashboard") }}" icon='fas-house'
    :active="request()->routeIs("dashboard*")">Dashboard</x-layouts.sidebar-link>

<x-layouts.sidebar-two-level-link-parent :active="request()->routeIs("task-categories*")"
    title="Task Categories" icon='fas-folder'>
    <x-layouts.sidebar-two-level-link href="{{ route("task-categories.index") }}"
        icon='fas-list' :active="request()->routeIs("task-categories.index")">Task Categories
        List</x-layouts.sidebar-link>
        <x-layouts.sidebar-two-level-link
            href="{{ route("task-categories.create") }}" icon='fas-plus'
            :active="request()->routeIs("task-categories.create")">Create New Task
            Category</x-layouts.sidebar-link>
</x-layouts.sidebar-two-level-link-parent>

<x-layouts.sidebar-two-level-link-parent :active="request()->routeIs("tasks*")"
    title="Tasks" icon='fas-list'>
    <x-layouts.sidebar-two-level-link href="{{ route("tasks.index") }}"
        icon='fas-list' :active="request()->routeIs("tasks.index")">Tasks
        List</x-layouts.sidebar-link>
        <x-layouts.sidebar-two-level-link href="{{ route("tasks.create") }}"
            icon='fas-plus' :active="request()->routeIs("tasks.create")">Create New
            Task</x-layouts.sidebar-link>
</x-layouts.sidebar-two-level-link-parent>
```
