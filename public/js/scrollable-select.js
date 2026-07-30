(function () {
    function initScrollableSelects() {
        document.querySelectorAll("select[data-scrollable-select]").forEach(function (select) {
            if (select.dataset.scrollableSelectReady) return;
            select.dataset.scrollableSelectReady = "true";

            var wrapper = document.createElement("div");
            wrapper.className = "scrollable-select";
            select.parentNode.insertBefore(wrapper, select);
            wrapper.appendChild(select);

            var button = document.createElement("button");
            button.type = "button";
            button.className = "scrollable-select__button";
            button.setAttribute("aria-haspopup", "listbox");
            button.setAttribute("aria-expanded", "false");

            var list = document.createElement("div");
            list.className = "scrollable-select__list";
            list.setAttribute("role", "listbox");
            list.hidden = true;

            function close() {
                list.hidden = true;
                button.setAttribute("aria-expanded", "false");
            }

            function sync() {
                button.textContent = select.options[select.selectedIndex] ? select.options[select.selectedIndex].text : "";
                list.querySelectorAll(".scrollable-select__option").forEach(function (item, index) {
                    item.setAttribute("aria-selected", String(index === select.selectedIndex));
                });
            }

            Array.from(select.options).forEach(function (option) {
                var item = document.createElement("button");
                item.type = "button";
                item.className = "scrollable-select__option";
                item.textContent = option.text;
                item.setAttribute("role", "option");
                item.addEventListener("click", function () {
                    select.value = option.value;
                    select.dispatchEvent(new Event("change", { bubbles: true }));
                    sync();
                    close();
                    button.focus();
                });
                list.appendChild(item);
            });

            button.addEventListener("click", function () {
                var opening = list.hidden;
                document.querySelectorAll(".scrollable-select__list:not([hidden])").forEach(function (openList) {
                    openList.hidden = true;
                    if (openList.previousElementSibling) openList.previousElementSibling.setAttribute("aria-expanded", "false");
                });

                list.hidden = !opening;
                button.setAttribute("aria-expanded", String(opening));
                if (!opening) return;

                var rect = button.getBoundingClientRect();
                var below = window.innerHeight - rect.bottom - 20;
                var above = rect.top - 20;
                var openAbove = below < 240 && above > below;
                list.style.maxHeight = Math.max(120, Math.min(320, openAbove ? above : below)) + "px";
                list.style.top = openAbove ? "auto" : "calc(100% + .25rem)";
                list.style.bottom = openAbove ? "calc(100% + .25rem)" : "auto";

                var selected = list.querySelector('[aria-selected="true"]');
                (selected || list.firstElementChild).focus({ preventScroll: true });
                if (selected) selected.scrollIntoView({ block: "nearest" });
            });

            wrapper.addEventListener("keydown", function (event) {
                if (event.key === "Escape") {
                    close();
                    button.focus();
                }
            });
            document.addEventListener("click", function (event) {
                if (!wrapper.contains(event.target)) close();
            });

            wrapper.append(button, list);
            sync();
        });
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", initScrollableSelects);
    } else {
        initScrollableSelects();
    }
    document.addEventListener("turbo:load", initScrollableSelects);
    document.addEventListener("turbo:frame-render", initScrollableSelects);
})();