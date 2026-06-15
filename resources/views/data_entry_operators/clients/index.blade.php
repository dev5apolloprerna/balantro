<div data-controller="confirm-delete">
  @include('data_entry_operators.clients.client_list', ['clients' => $clients])

  <!-- MOVE modals OUTSIDE of turbo_frame -->
  @include('shared.confirm_delete_modal', ['resourceName' => 'client'])
</div>