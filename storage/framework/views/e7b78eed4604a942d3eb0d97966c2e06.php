<div id="editModal" class="hidden fixed inset-0 z-50">
    <div class="absolute inset-0 bg-black/60"></div>
    <div class="relative mx-auto mt-24 w-[720px] max-w-[95%] rounded-2xl bg-neutral-900 text-white shadow-xl">
        <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-700">
            <h2 class="text-xl font-semibold">Edit Supervisor</h2> <button type="button" onclick="closeEditModal()"
                class="text-neutral-400 hover:text-white">✕</button>
        </div>
        <form id="editForm" method="POST" class="px-6 py-5 space-y-5"> 
            <?php echo csrf_field(); ?> 
            <?php echo method_field('PUT'); ?> 
            <input type="hidden" name="fcm_token" id="e_fcm_token">
            <input type="hidden" name="device_type" id="e_device_type">
            <input type="hidden" name="browser_name" id="e_browser_name">
            <input type="hidden" name="os_name" id="e_os_name">
            <div> 
                <label
                    class="block text-sm font-medium mb-1">Name <span class="text-red-500">*</span></label> <input
                    id="edit_name" name="name" type="text"
                    class="w-full rounded-md border border-neutral-700 bg-neutral-800 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <div> <label class="block text-sm font-medium mb-1">Email <span class="text-red-500">*</span></label> <input
                    id="edit_email" name="email" type="email"
                    class="w-full rounded-md border border-neutral-700 bg-neutral-800 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <div class="flex justify-end gap-3 pt-2 pb-5"> <button type="button" onclick="closeEditModal()"
                    class="rounded-md border border-neutral-600 px-4 py-2 text-sm text-neutral-200 hover:bg-neutral-800">Cancel</button>
                <button type="submit"
                    class="rounded-md bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700">Save</button>
            </div>
        </form>
    </div>
</div>
<script>
    // async function fillEditDeviceInfo() {
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

    //     document.getElementById("e_device_type").value = deviceType;
    //     document.getElementById("e_browser_name").value = browserName;
    //     document.getElementById("e_os_name").value = osName;

    //     // Try to fetch FCM token
    //     // try {
    //     const perm = await Notification.requestPermission();

    //     if (perm === "granted") {
    //         const token = await messaging.getToken({
    //             vapidKey: VAPID_PUBLIC_KEY,
    //         });
    //         if (token) {
    //             document.getElementById("e_fcm_token").value = token;
    //         }
    //     }
    //     // } catch (e) {
    //     //     console.warn("FCM token skipped:", e);
    //     // }
    // }

    document.getElementById("editForm").addEventListener("submit", async function(e) {
        e.preventDefault();

        // await fillEditDeviceInfo();

        // Wait for next event loop cycle
        await new Promise(resolve => setTimeout(resolve, 50));

        console.log("Submitting with:", {
            fcm: document.getElementById("e_fcm_token").value,
            device: document.getElementById("e_device_type").value,
            browser: document.getElementById("e_browser_name").value,
            os: document.getElementById("e_os_name").value,
        });

        this.submit();
    });
</script><?php /**PATH D:\xampp\htdocs\balantro\resources\views\admin\supervisors\edit_modal.blade.php ENDPATH**/ ?>