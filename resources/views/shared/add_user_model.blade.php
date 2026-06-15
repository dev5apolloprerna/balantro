<div id="add-user-modal"
     data-add-user-target="modal"
     tabindex="-1"
     aria-hidden="true"
     class="hidden fixed inset-0 z-50 flex justify-center items-center w-full overflow-x-hidden overflow-y-auto bg-black/50 dark:bg-white/30">
  <div class="relative p-4 w-full max-w-2xl max-h-full">
    <div class="relative bg-white dark:bg-dark-2 rounded-2xl shadow-lg">
      <!-- Modal Header -->
      <div class="flex items-center justify-between p-4 md:p-5 border-b border-gray-200 dark:border-gray-600 rounded-t">
        <h6 class="font-semibold text-gray-900 dark:text-white">
          @if(isset($role))
            {{ __("admin.common.add.add_{$role}") }}
          @else
            {{ __('admin.common.add.add_user') }}
          @endif
        </h6>
        <button type="button"
                id="add-user-cancel"
                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer"
                data-action="click->add-user#cancel">
          <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
          </svg>
        </button>
      </div>
      <!-- Modal Body -->
      <div class="p-4 md:p-5 space-y-4">
        <form id="add-user-form" data-add-user-target="form" data-action="submit->add-user#submit">
          @csrf
          <div class="mb-4">
            <label for="add-user-name" class="block text-sm font-medium font-semibold text-neutral-600 dark:text-neutral-200 mb-2">
              {{ __('common.add.name_label') }}
            </label>
            <input type="text" name="name" id="add-user-name" placeholder="{{ __('common.add.name_placeholder') }}"
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-500 rounded-lg focus:outline-none focus:ring-0 focus:ring-blue-500 focus:border-blue-500 dark:text-white placeholder-gray-500"
                  data-add-user-target="nameField">
            <p id="name-error" 
               data-add-user-target="nameError" 
               class="hidden mt-1 text-sm !text-danger-600 dark:text-danger-500">
            </p>
          </div>
          <div class="mb-4">
            <label for="add-user-email" class="block text-sm font-medium font-semibold text-neutral-600 dark:text-neutral-200 mb-2">
              {{ __('common.add.email_label') }}
            </label>
            <input type="email" name="email" id="add-user-email" placeholder="{{ __('common.add.email_placeholder') }}"
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-500 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:text-white placeholder-gray-500"
                  data-add-user-target="emailField">
            <p id="email-error" 
               data-add-user-target="emailError" 
               class="hidden mt-1 text-sm !text-danger-600">
            </p>
          </div>
          <!-- Modal Footer -->
          <div class="flex items-center justify-end gap-4 p-2 md:p-2 border-t border-gray-200 dark:border-gray-600 rounded-b">
            <button type="button" class="w-24 py-2 px-4 rounded-lg cursor-pointer border border-danger-600 bg-hover-danger-200 !text-danger-600 text-center"
                data-action="click->add-user#cancel">
              {{ __('common.add.cancel') }}
            </button>
            <button type="submit" class="w-24 py-2 px-4 rounded-lg cursor-pointer border border-primary-600 bg-primary-600 hover:bg-primary-700 text-white text-center"
                data-add-user-target="submitButton">
              {{ __('common.add.save_button') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>