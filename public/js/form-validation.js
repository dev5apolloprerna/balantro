(function () {
    "use strict";

    const errorClass = "system-field-error";
    const invalidClass = "system-invalid-field";

    function isValidatableField(field) {
        return field instanceof HTMLElement
            && typeof field.checkValidity === "function"
            && !field.disabled
            && !["button", "hidden", "reset", "submit"].includes(field.type);
    }

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

    function validationAnchor(field) {
        if (field.type === "radio" && field.name && field.form) {
            return Array.from(field.form.elements).find((candidate) =>
                candidate.type === "radio" && candidate.name === field.name
            ) || field;
        }

        return field;
    }

    function showFieldError(field, message) {
        field = validationAnchor(field);
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
        if (!(form instanceof HTMLFormElement) || form.noValidate || event.submitter?.formNoValidate) return;

        form.querySelectorAll(`.${invalidClass}`).forEach(clearField);
        form.querySelectorAll(`.${errorClass}`).forEach((error) => error.remove());

        // Reading ValidityState avoids dispatching an `invalid` event for every
        // field while scanning. That event is handled separately for callers of
        // reportValidity(), and used to cause duplicate messages and erratic
        // focus changes during a normal submit.

        form.querySelectorAll(`.${invalidClass}`).forEach(clearField);
        const invalidFields = Array.from(form.elements).filter((field) =>
            isValidatableField(field) && !field.validity.valid
        );
        if (!invalidFields.length) return;

        event.preventDefault();
        event.stopImmediatePropagation();
        
        const renderedGroups = new Set();
        invalidFields.forEach((field) => {
            const group = field.type === "radio" ? `radio:${field.name}` : field;
            if (renderedGroups.has(group)) return;
            renderedGroups.add(group);
            showFieldError(field, field.validationMessage);
        });

        const firstField = validationAnchor(invalidFields[0]);
        revealContainer(firstField);
        firstField.focus({ preventScroll: true });
        firstField.scrollIntoView({ behavior: "smooth", block: "center" });
    }

    function clearOnInput(event) {
        const field = event.target;
        if (!isValidatableField(field) || !field.validity.valid) return;

        const anchor = validationAnchor(field);
        if (anchor.classList.contains(invalidClass)) clearField(anchor);
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