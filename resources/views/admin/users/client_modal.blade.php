<div id="client-modal" data-client-modal-target="modal" data-action="keydown@window->client-modal#handleKeydown click->client-modal#closeBackground" 
    class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center">
    <div class="bg-white dark:bg-neutral-800 rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between p-4 md:p-5 border-b border-gray-200 dark:border-gray-600 rounded-t">
            <span class="text-xl font-semibold text-gray-900 dark:text-white">@lang('client_modal.title')</span>
            <button type="button" data-action="client-modal#close" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
            </button>
        </div>
        <div class="p-6 space-y-6">
            <form action="{{ route('admin.clients.store') }}" method="POST" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-gray-700 dark:text-gray-300 mb-2 required">
                            @lang('client_modal.form.client_name.label')
                        </label>
                        <input type="text" name="name" id="name" placeholder="@lang('client_modal.form.client_name.placeholder')" 
                               class="form-control border border-neutral-300 bg-custom-input dark:bg-dark-2 @error('name') border-red-500 @enderror" required>
                        @error('name')
                            <p class="form-error text-sm text-red-600 dark:text-red-400 mt-1">@lang('client_modal.form.client_name.label') {{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-gray-700 dark:text-gray-300 mb-2 required">
                            @lang('client_modal.form.email.label')
                        </label>
                        <input type="email" name="email" id="email" placeholder="@lang('client_modal.form.email.placeholder')" 
                               class="form-control border border-neutral-300 bg-custom-input dark:bg-dark-2 @error('email') border-red-500 @enderror" required>
                        @error('email')
                            <p class="form-error text-sm text-red-600 dark:text-red-400 mt-1">@lang('client_modal.form.email.label') {{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="business_type" class="block text-gray-700 dark:text-gray-300 mb-2">
                            @lang('client_modal.form.business_type.label')
                        </label>
                        <select name="business_type" id="business_type" class="form-control border border-neutral-300 bg-custom-input text-neutral-900 dark:bg-dark-2 dark:text-white dark:border-neutral-700 @error('profile.business_type') border-red-500 @enderror">
                            <option value="">@lang('client_modal.form.business_type.prompt')</option>
                            @foreach($businessTypes as $key => $value)
                                <option value="{{ $key }}" @if(old('profile.business_type') == $key) selected @endif>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                        @error('profile.business_type')
                            <p class="form-error text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="pan_no" class="block text-gray-700 dark:text-gray-300 mb-2">
                            @lang('client_modal.form.pan_no.label')
                        </label>
                        <input type="text" name="profile[pan_no]" id="pan_no" placeholder="@lang('client_modal.form.pan_no.placeholder')" 
                               class="form-control border border-neutral-300 bg-custom-input dark:bg-dark-2 @error('profile.pan_no') border-red-500 @enderror">
                        @error('profile.pan_no')
                            <p class="form-error text-sm text-red-600 dark:text-red-400 mt-1">@lang('client_modal.form.pan_no.label') {{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="gst_no" class="block text-gray-700 dark:text-gray-300 mb-2">
                            @lang('client_modal.form.gst_no.label')
                        </label>
                        <input type="text" name="profile[gst_no]" id="gst_no" placeholder="@lang('client_modal.form.gst_no.placeholder')" 
                               class="form-control border border-neutral-300 bg-custom-input dark:bg-dark-2 @error('profile.gst_no') border-red-500 @enderror">
                        @error('profile.gst_no')
                            <p class="form-error text-sm text-red-600 dark:text-red-400 mt-1">@lang('client_modal.form.gst_no.label') {{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="mobile_no" class="block text-gray-700 dark:text-gray-300 mb-2">
                            @lang('client_modal.form.mobile_no.label')
                        </label>
                        <input type="text" name="profile[mobile_no]" id="mobile_no" placeholder="@lang('client_modal.form.mobile_no.placeholder')" 
                               class="form-control border border-neutral-300 bg-custom-input dark:bg-dark-2 @error('profile.mobile_no') border-red-500 @enderror">
                        @error('profile.mobile_no')
                            <p class="form-error text-sm text-red-600 dark:text-red-400 mt-1">@lang('client_modal.form.mobile_no.label') {{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="address" class="block text-gray-700 dark:text-gray-300 mb-2">
                        @lang('client_modal.form.address.label')
                    </label>
                    <textarea name="profile[address]" id="address" placeholder="@lang('client_modal.form.address.placeholder')" 
                              class="form-control border border-neutral-300 bg-custom-input dark:bg-dark-2 @error('profile.address') border-red-500 @enderror"></textarea>
                    @error('profile.address')
                        <p class="form-error text-sm text-red-600 dark:text-red-400 mt-1">@lang('client_modal.form.address.label') {{ $message }}</p>
                    @enderror
                </div>
                        
                <div class="flex justify-end space-x-3">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-800 text-white font-semibold py-2 px-4 rounded cursor-pointer">
                        @lang('client_modal.buttons.submit')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
