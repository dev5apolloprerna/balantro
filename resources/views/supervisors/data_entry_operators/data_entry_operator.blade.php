<tr class="hover:bg-gray-50 dark:hover:bg-gray-700" data-entry-operator-id="{{ $deo->id }}">
  <td class="px-4 py-3">{{ $deo->name }}</td>
  <td class="px-4 py-3">{{ $deo->email }}</td>
  <td class="px-4 py-3">{{ $deo->managers->first()->name ?? '-' }}</td>
  @if(false)
    <td class="text-center flex space-x-2">
      @can('update', App\Http\Controllers\Supervisors\DataEntryOperatorsController::class)
        <button type="button"
                class="remove-item-btn bg-green-100 dark:!bg-success-600/25 hover:bg-green-200 text-green-600 dark:!text-green-500 font-medium w-10 h-10 flex justify-center items-center rounded-full enabled:cursor-pointer disabled:cursor-not-allowed"
                data-action="click->edit#edit">
          <iconify-icon icon="fluent:edit-24-regular" class="menu-icon"></iconify-icon>
        </button>
      @endcan
      @can('destroy', App\Http\Controllers\Supervisors\DataEntryOperatorsController::class)
        <form action="{{ route('supervisors.data_entry_operators.destroy', $deo) }}" method="POST">
          @csrf
          @method('DELETE')
          <button type="submit"
                  class="remove-item-btn bg-danger-100 dark:!bg-danger-600/25 hover:bg-danger-200 text-danger-600 dark:!text-danger-500 font-medium w-10 h-10 flex justify-center items-center rounded-full enabled:cursor-pointer disabled:cursor-not-allowed"
                  data-action="confirm-delete#confirm">
            <iconify-icon icon="fluent:delete-24-regular" class="menu-icon"></iconify-icon>
          </button>
        </form>
      @endcan
    </td>
  @endif
</tr>