<ul class="space-y-1 px-2">
    <li>
        <a href="{{ route('home') }}" title="Dashboard"
            class="nav-item flex items-center p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ Route::is('home') ? 'active bg-gray-200 dark:bg-gray-600' : '' }}">
            <i class="fas fa-chart-bar text-xl mr-3"></i>
            <span class="nav-text flex-1">Dashboard</span>
        </a>
    </li>
    <li>
        <a href="{{ route('managers.index') }}" title="Managers"
            class="nav-item flex items-center p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ Route::is('managers.*') ? 'active bg-gray-200 dark:bg-gray-600' : '' }}">
            <i class="fas fa-user-tie text-xl mr-3"></i>
            <span class="nav-text flex-1">Managers</span>
        </a>
    </li>
    <li>
        <a href="{{ route('supervisors.index') }}" title="Supervisors"
            class="nav-item flex items-center p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ Route::is('supervisors.*') ? 'active bg-gray-200 dark:bg-gray-600' : '' }}">
            <i class="fa-solid fa-users menu-icon text-xl mr-3"></i>
            <span class="nav-text flex-1">Supervisors</span>
        </a>
    </li>
    <li>
        <a href="{{ route('data_entry_operators.index') }}" title="Data Entry Operators"
            class="nav-item flex items-center p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ Route::is('data_entry_operators.*') ? 'active bg-gray-200 dark:bg-gray-600' : '' }}">
            <i class="fas fa-keyboard menu-icon text-xl mr-3"></i>
            <span class="nav-text flex-1">Data Entry Operators</span>
        </a>
    </li>
    <li>
        <a href="{{ route('clients.index') }}" title="Clients"
            class="nav-item flex items-center p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ Route::is('clients.*') ? 'active bg-gray-200 dark:bg-gray-600' : '' }}">
            <i class="fa-solid fa-users menu-icon  text-xl mr-3"></i>
            <span class="nav-text flex-1">Clients</span>
        </a>
    </li>
    <li>
        <a href="{{ route('documents.index') }}" title="Documents"
            class="nav-item flex items-center p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ Route::is('documents.*') ? 'active bg-gray-200 dark:bg-gray-600' : '' }}">
            <i class="fa-solid fa-file-lines menu-icon text-xl mr-3"></i>
            <span class="nav-text flex-1">Documents</span>
        </a>
    </li>
    <li>
        <a href="{{ route('groups.index') }}" title="Groups"
            class="nav-item flex items-center p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ Route::is('groups.*') ? 'active bg-gray-200 dark:bg-gray-600' : '' }}">
            <i class="fa-solid fa-users menu-icon  text-xl mr-3"></i>
            <span class="nav-text flex-1">Groups</span>
        </a>
    </li>

    <li>
        <a href="{{ route('super-admin.blog.index') }}" title="Blog"
            class="nav-item flex items-center p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ request()->is('admin/blog*') ? 'active bg-gray-200 dark:bg-gray-600' : '' }}">

            <i class="fas fa-blog menu-icon text-xl mr-3"></i>
            <span class="nav-text flex-1">Blog</span>

        </a>
    </li>
</ul>

