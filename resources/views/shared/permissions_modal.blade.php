<div id="permissions-modal"
     data-permissions-modal-target="modal"
     class="hidden fixed inset-0 z-50 items-center justify-center w-full overflow-x-hidden overflow-y-auto bg-slate-900/60 p-4 backdrop-blur-sm dark:bg-black/70"
     tabindex="-1"
     aria-hidden="true">
  <div class="relative w-full max-w-4xl max-h-[92vh]">
    <div class="relative overflow-hidden bg-white dark:bg-dark-2 rounded-2xl shadow-2xl ring-1 ring-slate-200/80 dark:ring-slate-700/80">
      <!-- Modal Header -->
      <div class="flex items-center justify-between gap-4 bg-slate-50 px-5 py-4 border-b border-gray-200 dark:bg-slate-900/70 dark:border-gray-700">
        <h6 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('admin.assign_permissions.modal.title') }}</h6>
        <button type="button"
                class="text-gray-500 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-700 dark:hover:text-white w-9 h-9 inline-flex justify-center items-center rounded-full cursor-pointer transition"
                data-action="permissions-modal#close">
          <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
          </svg>
          <span class="sr-only">{{ __('admin.assign_permissions.modal.close_icon') }}</span>
        </button>
      </div>
      <!-- Modal Body -->
      <form method="POST" action="#" class="p-5 space-y-5"
            data-action="submit->permissions-modal#save"
            data-permissions-modal-target="form">
        @csrf
        <div data-permissions-modal-target="content" class="max-h-[60vh] overflow-y-auto pr-2">
          <!-- Permissions checkboxes will be dynamically inserted here -->
        </div>
        <!-- Modal Footer -->
        <div class="flex flex-wrap items-center justify-end gap-3 pt-4 mt-4 border-t border-gray-200 dark:border-gray-700">
          <button type="button"
                  class="min-w-24 py-2 px-4 rounded-lg cursor-pointer border border-danger-200 bg-danger-50 hover:bg-danger-100 !text-danger-700 text-center transition"
                  data-action="click->permissions-modal#close">
            {{ __('admin.assign_permissions.modal.close_btn') }}
          </button>
          <button type="submit"
                  class="min-w-24 py-2 px-4 rounded-lg cursor-pointer border border-primary-600 bg-primary-600 hover:bg-primary-700 text-white text-center shadow-sm transition">
            {{ __('admin.assign_permissions.modal.save_btn') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</div>