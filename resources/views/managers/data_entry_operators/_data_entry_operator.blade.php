<tr id="deo_{{ $deo->id }}" class="hover:bg-gray-50 dark:hover:bg-gray-700" data-entry-operator-id="{{ $deo->id }}">
  <td class="px-4 py-3">{{ $deo->name }}</td>
  <td class="px-4 py-3">{{ $deo->email }}</td>
  <td>{{ $deo->supervisors->isNotEmpty() ? $deo->supervisors->pluck('name')->join(', ') : '-' }}</td>
  <td>
    <div class="flex justify-center space-x-2">
      @can('assign_supervisor', 'App\Http\Controllers\Managers\DataEntryOperatorsController')
        <button type="button"
                class="bg-indigo-100 dark:bg-indigo-600/25 hover:bg-indigo-200 text-indigo-600 dark:text-indigo-400 font-medium w-10 h-10 flex justify-center items-center rounded-full cursor-pointer"
                data-deo-id="{{ $deo->id }}"
                data-action="click->manager--data-entry-operator--allocate-supervisor#setSupervisor"
                title="{{ __('managers.data_entry_operators.index.table_headers.actions') }}">
          <iconify-icon icon="heroicons-outline:user-add" class="text-lg"></iconify-icon>
        </button>
      @else
        -
      @endcan
    </div>
  </td>
</tr>