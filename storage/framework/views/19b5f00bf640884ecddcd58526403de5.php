<div id="addManagerModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-lg dark:bg-neutral-900">
        <div class="flex items-center justify-between border-b pb-3">
            <h2 class="text-lg font-semibold">Add Manager</h2>
            <button onclick="closeManagerModal()" class="text-neutral-400 hover:text-neutral-600">&times;</button>
        </div>

        <form method="POST" action="<?php echo e(route('managers.store')); ?>" id="addManagerForm" class="mt-4 space-y-4">
            <?php echo csrf_field(); ?>
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
    // async function fillDeviceInfo() {
    //     // Device type
    //     let deviceType = /Mobi|Android/i.test(navigator.userAgent) ?
    //         "mobile" :
    //         "pc";

    //     // Browser name (basic detection)
    //     let browserName = "Unknown";
    //     if (/Chrome/i.test(navigator.userAgent)) browserName = "Chrome";
    //     else if (/Firefox/i.test(navigator.userAgent)) browserName = "Firefox";
    //     else if (
    //         /Safari/i.test(navigator.userAgent) &&
    //         !/Chrome/i.test(navigator.userAgent)
    //     )
    //         browserName = "Safari";
    //     else if (/Edge/i.test(navigator.userAgent)) browserName = "Edge";

    //     // OS name
    //     let osName = "Unknown";
    //     if (/Win/i.test(navigator.userAgent)) osName = "Windows";
    //     else if (/Mac/i.test(navigator.userAgent)) osName = "MacOS";
    //     else if (/Linux/i.test(navigator.userAgent)) osName = "Linux";
    //     else if (/Android/i.test(navigator.userAgent)) osName = "Android";
    //     else if (/iPhone|iPad|iPod/i.test(navigator.userAgent)) osName = "iOS";
    //     // alert(deviceType);
    //     // alert(browserName);
    //     // alert(osName);
    //     document.getElementById("c_device_type").value = deviceType;
    //     document.getElementById("c_browser_name").value = browserName;
    //     document.getElementById("c_os_name").value = osName;

    //     // Try to fetch FCM token
    //     // try {
    //     const perm = await Notification.requestPermission();

    //     if (perm === "granted") {
    //         const token = await messaging.getToken({
    //             vapidKey: VAPID_PUBLIC_KEY,
    //         });
    //         if (token) {
    //             document.getElementById("c_fcm_token").value = token;
    //         }
    //     }
    //     // } catch (e) {
    //     //     console.warn("FCM token skipped:", e);
    //     // }
    // }

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
</script><?php /**PATH D:\xampp\htdocs\balantro\resources\views\admin\managers\create_modal.blade.php ENDPATH**/ ?>