(function () {
    "use strict";

    const errorClass = "system-field-error";
    const invalidClass = "system-invalid-field";

    function escapeSelector(value) {
        if (window.CSS && typeof window.CSS.escape === "function") {
            return window.CSS.escape(value);
        }
        return value.replace(/([ #;&,.+*~':"!^$[\]()=>|/@])/g, "\\$1");
    }

    function fieldFor(name) {
        const candidates = [name, name.replace(/\.([^.]+)/g, "[$1]")];
        for (const candidate of candidates) {
            const field = document.querySelector(`[name="${escapeSelector(candidate)}"]`);
            if (field) return field;
        }

        // Laravel may return an array index (items.0.amount) while the form uses [].
        const wildcard = name.replace(/\.\d+(?=\.|$)/g, "[]").replace(/\.([^.]+)/g, "[$1]");
        return document.querySelector(`[name="${escapeSelector(wildcard)}"]`);
    }

    function clearField(field) {
        field.classList.remove(invalidClass);
        field.removeAttribute("aria-invalid");
        const messageId = field.getAttribute("aria-errormessage");
        if (messageId) document.getElementById(messageId)?.remove();
        field.removeAttribute("aria-errormessage");
    }

    function showFieldError(field, message) {
        clearField(field);
        const error = document.createElement("span");
        error.id = `validation-error-${Math.random().toString(36).slice(2)}`;
        error.className = errorClass;
        error.setAttribute("role", "alert");
        error.textContent = message;
        field.classList.add(invalidClass);
        field.setAttribute("aria-invalid", "true");
        field.setAttribute("aria-errormessage", error.id);

        const select2 = field.nextElementSibling?.classList.contains("select2")
            ? field.nextElementSibling
            : null;
        (select2 || field).insertAdjacentElement("afterend", error);
    }

    function revealContainer(field) {
        const modal = field.closest('[role="dialog"], .modal, [id$="-modal"], [id$="Modal"]');
        if (!modal) return;
        modal.classList.remove("hidden");
        if (modal.classList.contains("fixed")) modal.classList.add("flex");
        modal.setAttribute("aria-hidden", "false");
    }

    function applyServerErrors() {
        const payload = document.getElementById("system-validation-errors");
        if (!payload) return;

        let errors = {};
        try { errors = JSON.parse(payload.textContent || "{}"); } catch (_) { return; }

        let firstField = null;
        Object.entries(errors).forEach(([name, messages]) => {
            const field = fieldFor(name);
            if (!field) return;
            showFieldError(field, Array.isArray(messages) ? messages[0] : messages);
            revealContainer(field);
            firstField ||= field;
        });

        const summary = document.getElementById("system-validation-summary");
        const errorForm = firstField?.closest("form");

        // Layouts load the shared validation partial at the start of <body> so it
        // is available everywhere. Move server feedback beside the form that
        // owns the invalid field instead of leaving it above the application
        // header/navigation.
        if (summary && errorForm) errorForm.insertAdjacentElement("beforebegin", summary);

        const target = summary || firstField;
        target?.focus({ preventScroll: true });
        target?.scrollIntoView({ behavior: "smooth", block: "center" });
    }

    function validateForm(event) {
        const form = event.target;
        if (!(form instanceof HTMLFormElement) || form.noValidate) return;

        form.querySelectorAll(`.${invalidClass}`).forEach(clearField);
        const invalidFields = Array.from(form.elements).filter((field) =>
            typeof field.checkValidity === "function" && !field.checkValidity()
        );
        if (!invalidFields.length) return;

        event.preventDefault();
        event.stopImmediatePropagation();
        invalidFields.forEach((field) => showFieldError(field, field.validationMessage));
        revealContainer(invalidFields[0]);
        invalidFields[0].focus();
    }

    function clearOnInput(event) {
        const field = event.target;
        if (field.classList?.contains(invalidClass) && field.checkValidity()) clearField(field);
    }

    function handleInvalid(event) {
        const field = event.target;
        if (!field || typeof field.validationMessage !== "string") return;
        event.preventDefault();
        showFieldError(field, field.validationMessage);
        revealContainer(field);
        window.setTimeout(() => field.focus(), 0);
    }

    document.addEventListener("submit", validateForm, true);
    document.addEventListener("invalid", handleInvalid, true);
    document.addEventListener("input", clearOnInput, true);
    document.addEventListener("change", clearOnInput, true);
    document.addEventListener("DOMContentLoaded", applyServerErrors);
})();