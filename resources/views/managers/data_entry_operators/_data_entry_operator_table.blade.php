<div class="table-responsive">
  <table class="table bordered-table mb-0">
    <thead>
      <tr>
        <th scope="col">{{ __('managers.data_entry_operators.index.table_headers.name') }}</th>
        <th scope="col">{{ __('managers.data_entry_operators.index.table_headers.email') }}</th>
        <th scope="col">{{ __('managers.data_entry_operators.index.table_headers.supervisor') }}</th>
        <th scope="col" class="!text-center">{{ __('managers.data_entry_operators.index.table_headers.actions') }}</th>
      </tr>
    </thead>
    <tbody>
      @if($data_entry_operators && $data_entry_operators->count())
        @foreach($data_entry_operators as $deo)
          @include('managers.data_entry_operators.data_entry_operator', ['deo' => $deo])
        @endforeach
      @else
        <tr>
          <td colspan="5" class="px-6 py-12 text-center text-neutral-500 dark:text-neutral-400">
            <div class="flex flex-col items-center justify-center">
              <iconify-icon icon="heroicons-outline:document-magnifying-glass" class="text-4xl text-neutral-400 mb-3" title="{{ __('managers.data_entry_operators.index.no_operators_found.icon_title') }}"></iconify-icon>
              <p class="text-lg font-medium mb-1">{{ __('no_date_entry_operator') }}</p>
            </div>
          </td>
        </tr>
      @endif
    </tbody>
  </table>
</div>