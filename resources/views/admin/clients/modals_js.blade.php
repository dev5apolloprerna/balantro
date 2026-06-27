{{-- resources/views/admin/clients/assets/modals_js.blade.php --}}
<script>
    (function() {
        const $ = (sel, ctx = document) => ctx.querySelector(sel);
        const $$ = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        // —— Modal Helpers (native <dialog>) ——
        function openModal(id) {
            const dlg = document.getElementById(`modal-${id}`);
            if (!dlg) return;
            if (typeof dlg.showModal === 'function') dlg.showModal();
            else dlg.setAttribute('open', ''); // very old browsers fallback
        }

        function closeModal(btn) {
            const dlg = btn.closest('dialog');
            if (dlg?.open) dlg.close();
        }

        // –— ROUTES ——
        const R = window.CLIENT_ROUTES || {};
        const route = (tpl, id) => (tpl || '').replace('__ID__', id);

        // –— Assign Users modal elements ——
        const auDlg = $('#modal-assign-users');
        const auClientId = $('#au-client-id');
        const auManager = $('#au-manager');
        const auSupervisor = $('#au-supervisor');
        const auDeo = $('#au-deo');
        const auSave = $('#au-save');

        async function loadSupervisors(managerId) {
            if (!managerId) {
                auSupervisor.innerHTML = `<option value="">Select Supervisor (choose manager first)</option>`;
                auSupervisor.disabled = true;
                auDeo.innerHTML =
                    `<option value="">Select Data Entry Operator (choose supervisor first)</option>`;
                auDeo.disabled = true;
                return;
            }
            const url = route(R.mgrSup, managerId);
            const res = await fetch(url, {
                headers: {
                    'Accept': 'application/json'
                }
            });
            const data = await res.json();
            auSupervisor.innerHTML = `<option value="">Select Supervisor</option>` + data.map(s =>
                `<option value="${s.id}">${s.name}</option>`).join('');
            auSupervisor.disabled = false;
            // reset deo
            auDeo.innerHTML = `<option value="">Select Data Entry Operator (choose supervisor first)</option>`;
            auDeo.disabled = true;
        }

        async function loadDeos(supervisorId) {
            if (!supervisorId) {
                auDeo.innerHTML =
                    `<option value="">Select Data Entry Operator (choose supervisor first)</option>`;
                auDeo.disabled = true;
                return;
            }
            const url = route(R.supDeo, supervisorId);
            const res = await fetch(url, {
                headers: {
                    'Accept': 'application/json'
                }
            });
            const data = await res.json();
            auDeo.innerHTML = `<option value="">Select Data Entry Operator</option>` + data.map(d =>
                `<option value="${d.id}">${d.name}</option>`).join('');
            auDeo.disabled = false;
        }

        // open buttons
        document.addEventListener('click', async (e) => {
            const opener = e.target.closest('[data-modal-open]');
            if (opener) {
                const which = opener.getAttribute(
                    'data-modal-open'
                ); // assign-users | assign-groups | assign-permissions | create-edit-client
                const clientId = opener.getAttribute('data-client-id');
                if (which === 'assign-users') {
                    // preload
                    auClientId.value = clientId;
                    const managerId = opener.getAttribute('data-manager-id') || '';
                    const supervisorId = opener.getAttribute('data-supervisor-id') || '';
                    const deoId = opener.getAttribute('data-deo-id') || '';

                    // set selects
                    if (managerId) auManager.value = managerId;
                    else auManager.value = '';
                    await loadSupervisors(auManager.value);
                    if (supervisorId) {
                        auSupervisor.value = supervisorId;
                        await loadDeos(supervisorId);
                        if (deoId) auDeo.value = deoId;
                    }
                }

                if (which === 'assign-groups') {
                    $('#ag-client-id').value = clientId;
                    await hydrateGroups(clientId);
                }

                if (which === 'assign-permissions') {
                    $('#ap-client-id').value = clientId;
                    await hydratePermissions(clientId);
                }

                openModal(which);
            }

            // close buttons
            const closer = e.target.closest('[data-modal-close]');
            if (closer) closeModal(closer);
        });

        // cascading selects
        if (auManager) {
            auManager.addEventListener('change', async () => {
                await loadSupervisors(auManager.value);
            });
        }
        if (auSupervisor) {
            auSupervisor.addEventListener('change', async () => {
                await loadDeos(auSupervisor.value);
            });
        }

        // save Assign Users
        if (auSave) {
            auSave.addEventListener('click', async () => {
                const clientId = auClientId.value;
                const url = route(R.assignUsers, clientId);
                const fd = new FormData();
                if (auManager.value) fd.append('manager_ids[]', auManager.value);
                if (auSupervisor.value) fd.append('supervisor_ids[]', auSupervisor.value);
                if (auDeo.value) fd.append('data_entry_operator_ids[]', auDeo.value);

                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json'
                    },
                    body: fd
                });
                if (!res.ok) return showToast('Failed to assign users','error');
                auDlg.close();
                location.reload();
            });
        }

        // —— Assign Groups ——
        async function hydrateGroups(clientId) {
            const url = route(R.getGroups, clientId);
            const res = await fetch(url, {
                headers: {
                    'Accept': 'application/json'
                }
            });
            const data = await res.json();
            const wrap = $('#ag-groups-list');
            const assigned = new Set(data.assigned_group_ids || []);
            wrap.innerHTML = (data.groups || []).map(g => {
                const checked = assigned.has(g.id) ? 'checked' : '';
                return `
            <label class="flex items-center gap-3 rounded-lg border p-2 dark:border-neutral-700">
              <input type="checkbox" class="h-4 w-4" name="group_ids[]" value="${g.id}" ${checked}>
              <span class="flex-1">
                <span class="font-medium">${g.name}</span>
                <span class="ml-2 text-xs text-neutral-500">(${g.permissions_count} perms)</span>
              </span>
            </label>`;
            }).join('');
        }

        const agSave = $('#ag-save');
        if (agSave) {
            agSave.addEventListener('click', async () => {
                const clientId = $('#ag-client-id').value;
                const url = route(R.assignGroups, clientId);
                const checks = $$('#ag-groups-list input[type="checkbox"]:checked');
                const fd = new FormData();
                checks.forEach(c => fd.append('group_ids[]', c.value));
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json'
                    },
                    body: fd
                });
                if (!res.ok) return showToast('Failed to save groups','error');
                $('#modal-assign-groups').close();
                location.reload();
            });
        }

        // —— Assign Permissions ——
        async function hydratePermissions(clientId) {
            const url = route(R.getPermissions, clientId);
            const res = await fetch(url, {
                headers: {
                    'Accept': 'application/json'
                }
            });
            const data = await res.json();

            const s = v => String(v); // normalize all ids to string keys
            const allowedBox = document.querySelector('#ap-allowed');
            // const deniedBox = document.querySelector('#ap-denied');

            // normalize all incoming arrays to string Sets
            const groupSet = new Set((data.group_permission_ids || []).map(s));
            const assigned = new Set((data.assigned_permissions || []).map(s));
            const denied = new Set((data.denied_permissions || []).map(s));

            allowedBox.innerHTML = '';
            // deniedBox.innerHTML = '';

            (data.permissions || []).forEach(p => {
                const key = s(p.id);
                const isGroup = groupSet.has(key);
                const isAllowed = assigned.has(key); // already includes group ids from server
                const isDenied = denied.has(key);

                // Left: Allowed (group perms are shown as checked+disabled)
                const leftDisabled = isGroup ? 'disabled' : '';
                const leftChecked = isAllowed ? 'checked' : '';

                const left = `
                    <label class="flex items-center gap-3 rounded-lg border p-2 dark:border-neutral-700 ${leftDisabled ? 'opacity-60' : ''}">
                        <input type="checkbox" class="h-4 w-4" name="permission_ids[]" value="${p.id}" ${leftChecked} ${leftDisabled}>
                        <span class="flex-1">
                        <span class="font-medium">${p.name || (p.action + ':' + p.subject)}</span>
                        ${isGroup ? '<span class="ml-2 text-xs text-neutral-500">(from groups)</span>' : ''}
                        </span>
                    </label>
                    `;

                // Right: Denied (only for group perms)
                const rightDisabled = isGroup ? '' : 'disabled';
                // const rightChecked = isDenied ? 'checked' : '';

                // const right = `
                //     <label class="flex items-center gap-3 rounded-lg border p-2 dark:border-neutral-700 ${rightDisabled ? 'opacity-60' : ''}">
                //         <input type="checkbox" class="h-4 w-4" name="denied_permission_ids[]" value="${p.id}" ${rightChecked} ${rightDisabled}>
                //         <span class="flex-1">
                //         <span class="font-medium">${p.name || (p.action + ':' + p.subject)}</span>
                //         ${!isGroup ? '<span class="ml-2 text-xs text-neutral-500">(only for group perms)</span>' : ''}
                //         </span>
                //     </label>
                //     `;

                allowedBox.insertAdjacentHTML('beforeend', left);
                // deniedBox.insertAdjacentHTML('beforeend', right);
            });
        }


        const apSave = $('#ap-save');
        if (apSave) {
            apSave.addEventListener('click', async () => {
                const clientId = $('#ap-client-id').value;
                const url = route(R.assignPermissions, clientId);

                const allowed = $$('#ap-allowed input[type="checkbox"]:checked').map(i => i.value);
                const denied = $$('#ap-denied input[type="checkbox"]:checked').map(i => i.value);
                const fd = new FormData();
                allowed.forEach(id => fd.append('permission_ids[]', id));
                denied.forEach(id => fd.append('denied_permission_ids[]', id));

                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json'
                    },
                    body: fd
                });
                if (!res.ok) return showToast('Failed to save permissions','error');
                $('#modal-assign-permissions').close();
                location.reload();
            });
        }
    })();

    (function() {
        const $ = (sel, ctx = document) => ctx.querySelector(sel);
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const R = window.CLIENT_ROUTES || {};
        const route = (tpl, id) => (tpl || '').replace('__ID__', id);

        function openModal(id) {
            const dlg = document.getElementById(`modal-${id}`);
            if (!dlg) return;
            if (typeof dlg.showModal === 'function') dlg.showModal();
            else dlg.setAttribute('open', '');
        }

        function closeModal(btn) {
            const dlg = btn.closest('dialog');
            if (dlg?.open) dlg.close();
        }

        // ---- Client Form controls
        const cfDlg = $('#modal-client-form');
        const cfId = $('#cf-client-id');
        const cfName = $('#cf-name');
        const cfEmail = $('#cf-email');
        const cfGuid = $('#cf-guid');

        const cfcmToken = $("#c_fcm_token");
        const cDeviceType = $("#c_device_type");
        const cBrowserName = $("#c_browser_name");
        const cOsName = $("#c_os_name");

        const cfBizType = $('#cf-business-type');
        const cfMobile = $('#cf-mobile');
        const cfWhatsApp = $('#cf-whatsapp');
        const cfPan = $('#cf-pan');
        const cfGst = $('#cf-gst');
        const cfAddress = $('#cf-address');
        const cfisStockManagement = $('#cf-isStockManagement');        
        const cfSave = $('#cf-save');

        function resetClientForm() {
            cfId.value = '';
            cfName.value = '';
            cfEmail.value = '';
            cfGuid.value = '';
            cfBizType.value = '';
            cfMobile.value = '';
            cfWhatsApp.value = '';
            cfPan.value = '';
            cfGst.value = '';
            cfAddress.value = '';
            cfisStockManagement.value = '';
        }

        async function fillClientForm(id) {
            resetClientForm();
            const url = route(R.clientEdit, id);
            const res = await fetch(url, {
                headers: {
                    'Accept': 'application/json'
                }
            });
            if (!res.ok) return showToast('Failed to load client','error');
            const c = await res.json();
            cfId.value = c.id || '';
            cfName.value = c.name || '';
            cfEmail.value = c.email || '';
            cfGuid.value = c.guid || '';
            cfBizType.value = c.profile?.business_type || '';
            cfMobile.value = c.profile?.mobile_no || '';
            cfWhatsApp.value = c.profile?.whatsapp_no || '';
            cfPan.value = c.profile?.pan_no || '';
            cfGst.value = c.profile?.gst_no || '';
            cfAddress.value = c.profile?.address || '';
            cfisStockManagement.value = c.isStockManagement || 0;
        }

        // Openers
        document.addEventListener('click', async (e) => {
            const opener = e.target.closest('[data-modal-open]');
            if (!opener) return;

            const which = opener.getAttribute('data-modal-open'); // 'client-form', etc.
            if (which === 'client-form') {
                const id = opener.getAttribute('data-client-id');
                if (id) {
                    await fillClientForm(id);
                } else {
                    resetClientForm();
                }
            }
            openModal(which);
        });

        // Closers (already handled globally in your file; included here if not)
        document.addEventListener('click', (e) => {
            const closer = e.target.closest('[data-modal-close]');
            if (closer) closeModal(closer);
        });

        // Save
        if (cfSave) {
            cfSave.addEventListener('click', async () => {
                
             cfSave.disabled = true;
            const originalText = cfSave.innerHTML;

            cfSave.innerHTML = `
                <svg class="animate-spin h-4 w-4 inline-block mr-2"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25"
                        cx="12"
                        cy="12"
                        r="10"
                        stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75"
                        fill="currentColor"
                        d="M4 12a8 8 0 018-8v8z"></path>
                </svg>
                Saving...
            `;
                //await fillDeviceInfo();
                const id = cfId.value;
                const fd = new FormData();
                fd.append('name', cfName.value.trim());
                fd.append('email', cfEmail.value.trim());
                fd.append('guid', cfGuid.value.trim());

                fd.append('fcm_token', cfcmToken.value.trim());
                fd.append('device_type', cDeviceType.value.trim());
                fd.append('browser_name', cBrowserName.value.trim());
                fd.append('os_name', cOsName.value.trim());

                // Optional profile fields (if your controller accepts them)
                fd.append('profile[business_type]', cfBizType.value);
                fd.append('profile[mobile_no]', cfMobile.value);
                fd.append('profile[whatsapp_no]', cfWhatsApp.value);
                fd.append('profile[pan_no]', cfPan.value);
                fd.append('profile[gst_no]', cfGst.value);
                fd.append('profile[address]', cfAddress.value);
                fd.append('isStockManagement', cfisStockManagement.value.trim());
                    
                const opts = {
                    method: id ? 'POST' : 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json'
                    },
                    body: fd
                };

                // For update, send method spoof
                let url;
                if (id) {
                    url = route(R.clientUpdate, id);
                    fd.append('_method', 'PUT');
                } else {
                    url = R.clientStore;
                }

                try {
                    const res = await fetch(url, opts);
                    if (!res.ok) {
                        const data = await res.json(); // Assuming the server returns a JSON response

                        // Clear previous error messages
                        document.getElementById('cf-name-error').textContent = '';
                        document.getElementById('cf-email-error').textContent = '';
                        document.getElementById('cf-guid-error').textContent = '';
                        document.getElementById('cf-mobile-error').textContent = '';
                        document.getElementById('cf-whatsapp-error').textContent = '';

                        // Display errors for each field, including Laravel's nested profile keys.
                        if (data.errors) {
                            if (data.errors.name) document.getElementById('cf-name-error').textContent =
                                data.errors.name[0];
                            if (data.errors.email) document.getElementById('cf-email-error').textContent =
                                data.errors.email[0];
                            if (data.errors.guid) document.getElementById('cf-guid-error').textContent =
                                data.errors.guid[0];
                            if (data.errors['profile.mobile_no']) document.getElementById('cf-mobile-error')
                                .textContent = data.errors['profile.mobile_no'][0];
                            if (data.errors['profile.whatsapp_no']) document.getElementById('cf-whatsapp-error')
                                .textContent = data.errors['profile.whatsapp_no'][0];
                        }
                        return;
                    }
                    cfDlg.close();
                    location.reload();
                } catch (error) {
                    showToast('Unable to save client. Please try again.', 'error');
                } finally {
                    cfSave.disabled = false;
                    cfSave.innerHTML = originalText;
                }
            });
        }
    })();
    const routes = {
        destroy: "{{ url('admin/clients') }}/__ID__",
    };

    document.addEventListener('click', (e) => {
        let btn;

        btn = e.target.closest('[data-open-delete]');
        if (btn) {
            const id = btn.dataset.id;
            const form = document.getElementById('deoDeleteForm');
            form.action = routes.destroy.replace('__ID__', id);
            openModal('deoDelete');
            return;
        }
    });

    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
        document.documentElement.classList.add('overflow-hidden');
    }

    function closeModal(id) {
        const el = document.getElementById(id);
        if (!el) return;
        el.classList.add('hidden');
        document.documentElement.classList.remove('overflow-hidden');
    }
</script>
<script src="https://www.gstatic.com/firebasejs/10.12.3/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.12.3/firebase-messaging-compat.js"></script>
<script src="{{ asset('assets/firebase/firebase.js') }}"></script>
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

    
</script>
