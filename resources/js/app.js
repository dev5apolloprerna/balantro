import "./bootstrap";
import Echo from "laravel-echo";
import Pusher from "pusher-js";

document.addEventListener("DOMContentLoaded", function () {
    // Your existing initApp function code here
    function initApp() {
        // 1. Dropdown menu functionality
        document
            .querySelectorAll(".sidebar-menu .dropdown")
            .forEach((dropdown) => {
                dropdown.removeEventListener(
                    "click",
                    dropdown._listener || (() => {})
                );
                dropdown._listener = function () {
                    const submenu = this.querySelector(".sidebar-submenu");
                    this.parentNode
                        .querySelectorAll(".dropdown")
                        .forEach((sibling) => {
                            if (sibling !== this) {
                                sibling
                                    .querySelector(".sidebar-submenu")
                                    ?.style.setProperty("display", "none");
                                sibling.classList.remove(
                                    "dropdown-open",
                                    "open"
                                );
                            }
                        });
                    if (submenu)
                        submenu.style.display =
                            submenu.style.display === "block"
                                ? "none"
                                : "block";
                    this.classList.toggle("dropdown-open");
                };
                dropdown.addEventListener("click", dropdown._listener);
            });

        // 2. Sidebar toggle functionality
        const sidebar = document.querySelector(".sidebar");
        const dashboardMain = document.querySelector(".dashboard-main");
        const sidebarToggle = document.querySelector(".sidebar-toggle");
        const sidebarMobileToggle = document.querySelector(
            ".sidebar-mobile-toggle"
        );
        const sidebarCloseBtn = document.querySelector(".sidebar-close-btn");

        if (sidebarToggle && !sidebarToggle._bound) {
            sidebarToggle.addEventListener("click", function () {
                const isActive = this.classList.toggle("active");
                sidebar?.classList.toggle("active", isActive);
                dashboardMain?.classList.toggle("active", isActive);
                localStorage.setItem(
                    "sidebar-collapsed",
                    isActive ? "true" : "false"
                );
            });
            sidebarToggle._bound = true;
        }

        if (sidebarMobileToggle && !sidebarMobileToggle._bound) {
            sidebarMobileToggle.addEventListener("click", () => {
                sidebar?.classList.add("sidebar-open");
                document.body.classList.add("overlay-active");
            });
            sidebarMobileToggle._bound = true;
        }

        if (sidebarCloseBtn && !sidebarCloseBtn._bound) {
            sidebarCloseBtn.addEventListener("click", () => {
                sidebar?.classList.remove("sidebar-open");
                document.body.classList.remove("overlay-active");
            });
            sidebarCloseBtn._bound = true;
        }

        // 3. Restore sidebar state from localStorage
        const savedState = localStorage.getItem("sidebar-collapsed");
        const collapsed = savedState === "true";
        sidebar?.classList.toggle("active", collapsed);
        dashboardMain?.classList.toggle("active", collapsed);
        sidebarToggle?.classList.toggle("active", collapsed);

        // 4. Active page highlighting
        const currentUrl = window.location.href;
        document.querySelectorAll("ul#sidebar-menu a").forEach((link) => {
            if (link.href === currentUrl) {
                link.classList.add("active-page");
                let parent = link.parentElement;
                parent?.classList.add("active-page");
                while (parent && parent.tagName !== "BODY") {
                    if (parent.tagName === "LI")
                        parent.classList.add("show", "open");
                    parent = parent.parentElement;
                }
            }
        });

        // 5. Theme toggle functionality
        const themeToggleBtn = document.getElementById("theme-toggle");
        const themeToggleDarkIcon = document.getElementById(
            "theme-toggle-dark-icon"
        );
        const themeToggleLightIcon = document.getElementById(
            "theme-toggle-light-icon"
        );

        const setThemeIcon = () => {
            // const prefersDark = window.matchMedia(
            //     "(prefers-color-scheme: dark)"
            // ).matches;
            // const isDark =
            //     localStorage.getItem("color-theme") === "dark" ||
            //     (!("color-theme" in localStorage) && prefersDark);
            //const savedTheme = localStorage.getItem("color-theme");
            const savedTheme =
                localStorage.getItem("color-theme") ||
                localStorage.getItem("theme");
            const isDark = savedTheme ? savedTheme === "dark" : true;
            document.documentElement.classList.toggle("dark", isDark);
            themeToggleDarkIcon?.classList.toggle("hidden", isDark);
            themeToggleLightIcon?.classList.toggle("hidden", !isDark);
        };

        setThemeIcon();

        if (themeToggleBtn && !themeToggleBtn._bound) {
            themeToggleBtn.addEventListener("click", () => {
                const isDark =
                    document.documentElement.classList.contains("dark");
                document.documentElement.classList.toggle("dark", !isDark);
                // localStorage.setItem("color-theme", isDark ? "light" : "dark");
                const nextTheme = isDark ? "light" : "dark";
                localStorage.setItem("color-theme", nextTheme);
                localStorage.setItem("theme", nextTheme);
                setThemeIcon();
            });
            themeToggleBtn._bound = true;
        }

        // 6. Profile dropdown functionality
        const dropdownToggle = document.querySelector(
            '[data-dropdown-toggle="dropdownProfile"]'
        );
        const dropdownMenu = document.getElementById("dropdownProfile");

        if (dropdownToggle && dropdownMenu && !dropdownToggle._bound) {
            const toggleMenu = (e) => {
                e.stopPropagation();
                dropdownMenu.classList.toggle("hidden");
            };

            const closeMenu = (e) => {
                if (
                    !dropdownMenu.contains(e.target) &&
                    !dropdownToggle.contains(e.target)
                ) {
                    dropdownMenu.classList.add("hidden");
                }
            };

            dropdownToggle.addEventListener("click", toggleMenu);
            document.addEventListener("click", closeMenu);
            dropdownToggle._bound = true;
        }
    }

    // Initialize on different events
    initApp();

    // For Livewire/Turbo
    // document.addEventListener('turbo:load', initApp);
    // document.addEventListener('turbo:render', initApp);
    // document.addEventListener('turbo:frame-render', initApp);

    // For Livewire
    // window.addEventListener('livewire:load', initApp);
    // window.addEventListener('livewire:navigated', initApp);

    document.addEventListener("DOMContentLoaded", initApp);
    document.addEventListener("turbo:load", initApp); // For Turbo Drive
    document.addEventListener("turbo:render", initApp); // For Turbo Frames
    document.addEventListener("turbo:frame-render", initApp); // For Turbo Frame updates
    window.addEventListener("livewire:load", initApp); // For Livewire
    window.addEventListener("livewire:navigated", initApp); // For Livewire page navigation
});

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: "pusher",
    key: import.meta.env.VITE_PUSHER_APP_KEY ?? "local",
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? "mt1",
    wsHost: import.meta.env.VITE_PUSHER_HOST ?? window.location.hostname,
    wsPort: Number(import.meta.env.VITE_PUSHER_PORT ?? 6001),
    wssPort: Number(import.meta.env.VITE_PUSHER_PORT ?? 6001),
    forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? "http") === "https",
    enabledTransports: ["ws", "wss"],
    // If you use sanctum + session guard:
    authEndpoint: "/broadcasting/auth",
    withCredentials: true,
    auth: {
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                ?.content,
        },
    },
});

// If using ES modules
export function initSidebar() {
    // Expose specific functions if needed
    return {
        initApp: initApp,
    };
}
