<tr id="client-{{ $client->id }}" class="hover:bg-gray-50 dark:hover:bg-gray-700" data-entry-operator-id="{{ $client->id }}">
  <td class="px-4 py-3">{{ $client->name }}</td>
  <td class="px-4 py-3">{{ $client->email }}</td>
  <td>{{ $client->supervisors->isNotEmpty() ? $client->supervisors->pluck('name')->join(', ') : '-' }}</td>
  <td>{{ $client->data_entry_operators->isNotEmpty() ? $client->data_entry_operators->pluck('name')->join(', ') : '-' }}</td>
  <td>{{ $client->company_id ?: '-' }}</td>
  <td>
    <div class="flex justify-center space-x-2">
      @can('assign_users', 'App\Http\Controllers\Managers\ClientsController')
        <button type="button"
                class="bg-indigo-100 dark:bg-indigo-600/25 hover:bg-indigo-200 text-indigo-600 dark:text-indigo-400 font-medium w-10 h-10 flex justify-center items-center rounded-full cursor-pointer"
                data-client-id="{{ $client->id }}"
                data-supervisor-id="{{ $client->supervisors->first()->id ?? null }}"
                data-data-entry-operator-id="{{ $client->data_entry_operators->first()->id ?? null }}"
                data-action="click->manager--client--allocate-supervisor-deo#setSupervisor"
                title="{{ __('managers.client.index.table_headers.actions') }}">
          <iconify-icon icon="heroicons-outline:user-add" class="text-lg"></iconify-icon>
        </button>
      @endcan

      @can('set_company_id', 'App\Http\Controllers\Managers\ClientsController')
        <button type="button"
                class="bg-green-100 dark:bg-green-600/25 hover:bg-green-200 text-green-600 dark:text-green-400 font-medium w-10 h-10 flex justify-center items-center rounded-full cursor-pointer"
                data-client-id="{{ $client->id }}"
                data-company-id="{{ $client->company_id }}"
                data-action="click->manager--client--set-company-id#showModal"
                title="Set Company ID">
          <iconify-icon icon="heroicons-outline:identification" class="text-2xl"></iconify-icon>
        </button>
      @endcan
    </div>
  </td>
</tr>