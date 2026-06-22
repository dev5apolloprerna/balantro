<div id="addManagerModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm">
    <div class="balantro-modal-panel w-full max-w-md rounded-2xl bg-white p-6 text-slate-900 shadow-xl dark:bg-slate-900 dark:text-white">
        <div class="flex items-center justify-between border-b border-slate-200 pb-3 dark:border-slate-700">
            <h2 class="text-lg font-semibold">Add Manager</h2>
            <button onclick="closeManagerModal()" class="rounded-full p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-slate-800 dark:hover:text-white">&times;</button>
        </div>

        <form method="POST" action="{{ route('managers.store') }}" id="addManagerForm" class="mt-4 space-y-4">
            @csrf
            <input type="hidden" name="fcm_token" id="c_fcm_token">
            <input type="hidden" name="device_type" id="c_device_type">
            <input type="hidden" name="browser_name" id="c_browser_name">
            <input type="hidden" name="os_name" id="c_os_name">

            <div>
                <label class="block text-sm font-medium">Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" required
                    class="mt-1 w-full rounded-md border border-neutral-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-primary-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white" />
            </div>

            <div>
                <label class="block text-sm font-medium">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" required
                    class="mt-1 w-full rounded-md border border-neutral-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-primary-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white" />
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeManagerModal()"
                    class="rounded-md border border-neutral-300 px-4 py-2 text-sm font-medium text-neutral-600 hover:bg-neutral-100 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-800">
                    Cancel
                </button>
                <button type="submit"
                    class="rounded-md bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById("addManagerForm").addEventListener("submit", async function(e) {
        e.preventDefault();

        // await fillDeviceInfo();

        // Wait for next event loop cycle
        await new Promise(resolve => setTimeout(resolve, 50));

        console.log("Submitting with:", {
            fcm: document.getElementById("c_fcm_token").value,
            device: document.getElementById("c_device_type").value,
            browser: document.getElementById("c_browser_name").value,
            os: document.getElementById("c_os_name").value,
        });

        this.submit();
    });
</script>