{{-- <div class="table-responsive">
    <table class="table bordered-table mb-0">
        <thead>
     <tr>
        <th scope="col">{{ __('admin.data_entry_operators.table.name') }}</th>
        <th scope="col">{{ __('admin.data_entry_operators.table.email') }}</th>
        <th scope="col">{{ __('admin.data_entry_operators.table.managers') }}</th>
        <th scope="col">{{ __('admin.data_entry_operators.table.supervisors') }}</th>
        <th scope="col">{{ __('admin.data_entry_operators.table.groups') }}</th>
        <th scope="col" class="flex justify-center">{{ __('admin.data_entry_operators.table.actions') }}</th>
      </tr>
        </thead> --}}
{{-- <tbody> --}}
@if ($data_entry_operators && $data_entry_operators->count())
    @foreach ($data_entry_operators as $data_entry_operator)
        @include('admin.data_entry_operators.data_entry_operator_row', [
            'data_entry_operator' => $data_entry_operator,
        ])
    @endforeach
@else
    <tr>
        <td colspan="6" class="text-center py-12 text-neutral-500 dark:text-neutral-400">
            <div class="flex flex-col items-center justify-center">
                <iconify-icon icon="heroicons-outline:document-magnifying-glass"
                    class="text-4xl text-neutral-400 mb-3"></iconify-icon>
                <!-- <p class="text-lg font-medium mb-1">
                    {{ __('admin.data_entry_operators.table.no_data_entry_operators_title') }}</p>
                <p class="text-sm">
                    {{ __('admin.data_entry_operators.table.no_data_entry_operators_description') }}</p> -->
                <p class="text-base font-medium">No managers found</p>
                <p class="text-sm">Click “Add Data Entry Operator” to create one.</p>
            </div>
        </td>
    </tr>
@endif
{{-- </tbody>
</table>
</div> --}}
