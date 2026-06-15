<tr class="hover:bg-gray-50 dark:hover:bg-gray-700" 
    data-entry-operator-id="{{ $client->id }}"
    data-client-mobile="{{ $client->profile->mobile_no ?? '' }}"
    data-client-pan="{{ $client->profile->pan_no ?? '' }}"
    data-client-gst="{{ $client->profile->gst_no ?? '' }}"
    data-client-address="{{ $client->profile->address ?? '' }}"
    data-client-business-type="{{ $client->profile->business_type ?? '' }}"
    data-entry-operator-id="{{ $client->id }}">
  <td>{{ $client->name }}</td>
  <td>{{ $client->email }}</td>
  <td>
    @if($client->dataEntryOperators->count() > 0)
      {{ $client->dataEntryOperators->pluck('name')->join(', ') }}
    @else
      <span class="text-gray-400">-</span>
    @endif
  </td>
  <td>
    @if($client->managers->count() > 0)
      {{ $client->managers->first()->name }}
    @else
      <span class="text-gray-400">-</span>
    @endif
  </td>
  <td>
    <div class="flex justify-center space-x-2">
      @can('assign_deos', App\Http\Controllers\Supervisors\ClientsController::class)
        <button type="button" class="bg-indigo-100 dark:bg-indigo-600/25 dark:hover:bg-indigo-600/50 hover:bg-indigo-200 text-indigo-600 dark:text-indigo-400 font-medium w-10 h-10 flex justify-center items-center rounded-full cursor-pointer"
                data-client-id="{{ $client->id }}"
                data-current-deo-id="{{ $client->dataEntryOperators->first()->id ?? null }}"
                data-action="click->supervisors--assign-deo#setClient"
                title="{{ __('supervisors.clients.table.assign_deo_btn') }}">
          <iconify-icon icon="heroicons-outline:user-add" class="text-lg"></iconify-icon>
        </button>
      @else
        -
      @endcan
    </div>
    @if(false)
      @can('update', App\Http\Controllers\Supervisors\ClientsController::class)
        <button type="button"
                class="remove-item-btn bg-green-100 dark:!bg-success-600/25 hover:bg-green-200 text-green-600 dark:!text-green-500 font-medium w-10 h-10 flex justify-center items-center rounded-full enabled:cursor-pointer disabled:cursor-not-allowed"
                data-action="client-edit#edit">
          <iconify-icon icon="fluent:edit-24-regular" class="menu-icon"></iconify-icon>
        </button>
      @endcan
      @can('destroy', App\Http\Controllers\Supervisors\ClientsController::class)
        <form action="{{ route('supervisors.clients.destroy', $client) }}" method="POST">
          @csrf
          @method('DELETE')
          <button type="submit" class="remove-item-btn bg-danger-100 dark:!bg-danger-600/25 hover:bg-danger-200 text-danger-600 dark:!text-danger-500 font-medium w-10 h-10 flex justify-center items-center rounded-full enabled:cursor-pointer disabled:cursor-not-allowed"
                  data-action="confirm-delete#confirm">
            <iconify-icon icon="fluent:delete-24-regular" class="menu-icon"></iconify-icon>
          </button>
        </form>
      @endcan
    @endif
  </td>
</tr>