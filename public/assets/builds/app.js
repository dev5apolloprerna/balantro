// app/javascript/app.js
function initApp() {
  document.querySelectorAll(".sidebar-menu .dropdown").forEach((dropdown) => {
    dropdown.removeEventListener("click", dropdown._listener || (() => {
    }));
    dropdown._listener = function() {
      const submenu = this.querySelector(".sidebar-submenu");
      this.parentNode.querySelectorAll(".dropdown").forEach((sibling) => {
        if (sibling !== this) {
          sibling.querySelector(".sidebar-submenu")?.style.setProperty("display", "none");
          sibling.classList.remove("dropdown-open", "open");
        }
      });
      if (submenu) submenu.style.display = submenu.style.display === "block" ? "none" : "block";
      this.classList.toggle("dropdown-open");
    };
    dropdown.addEventListener("click", dropdown._listener);
  });
  const sidebar = document.querySelector(".sidebar");
  const dashboardMain = document.querySelector(".dashboard-main");
  const sidebarToggle = document.querySelector(".sidebar-toggle");
  const sidebarMobileToggle = document.querySelector(".sidebar-mobile-toggle");
  const sidebarCloseBtn = document.querySelector(".sidebar-close-btn");
  if (sidebarToggle && !sidebarToggle._bound) {
    sidebarToggle.addEventListener("click", function() {
      const isActive = this.classList.toggle("active");
      sidebar?.classList.toggle("active", isActive);
      dashboardMain?.classList.toggle("active", isActive);
      localStorage.setItem("sidebar-collapsed", isActive ? "true" : "false");
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
  const savedState = localStorage.getItem("sidebar-collapsed");
  const collapsed = savedState === "true";
  sidebar?.classList.toggle("active", collapsed);
  dashboardMain?.classList.toggle("active", collapsed);
  sidebarToggle?.classList.toggle("active", collapsed);
  const currentUrl = window.location.href;
  document.querySelectorAll("ul#sidebar-menu a").forEach((link) => {
    if (link.href === currentUrl) {
      link.classList.add("active-page");
      let parent = link.parentElement;
      parent?.classList.add("active-page");
      while (parent && parent.tagName !== "BODY") {
        if (parent.tagName === "LI") parent.classList.add("show", "open");
        parent = parent.parentElement;
      }
    }
  });
  const themeToggleBtn = document.getElementById("theme-toggle");
  const themeToggleDarkIcon = document.getElementById("theme-toggle-dark-icon");
  const themeToggleLightIcon = document.getElementById("theme-toggle-light-icon");
  const setThemeIcon = () => {
    const prefersDark = window.matchMedia("(prefers-color-scheme: dark)").matches;
    const isDark = localStorage.getItem("color-theme") === "dark" || !("color-theme" in localStorage) && prefersDark;
    document.documentElement.classList.toggle("dark", isDark);
    themeToggleDarkIcon?.classList.toggle("hidden", isDark);
    themeToggleLightIcon?.classList.toggle("hidden", !isDark);
  };
  setThemeIcon();
  if (themeToggleBtn && !themeToggleBtn._bound) {
    themeToggleBtn.addEventListener("click", () => {
      const isDark = document.documentElement.classList.contains("dark");
      document.documentElement.classList.toggle("dark", !isDark);
      localStorage.setItem("color-theme", isDark ? "light" : "dark");
      setThemeIcon();
    });
    themeToggleBtn._bound = true;
  }
  const dropdownToggle = document.querySelector('[data-dropdown-toggle="dropdownProfile"]');
  const dropdownMenu = document.getElementById("dropdownProfile");
  if (dropdownToggle && dropdownMenu && !dropdownToggle._bound) {
    const toggleMenu = (e) => {
      e.stopPropagation();
      dropdownMenu.classList.toggle("hidden");
    };
    const closeMenu = (e) => {
      if (!dropdownMenu.contains(e.target) && !dropdownToggle.contains(e.target)) {
        dropdownMenu.classList.add("hidden");
      }
    };
    dropdownToggle.addEventListener("click", toggleMenu);
    document.addEventListener("click", closeMenu);
    dropdownToggle._bound = true;
  }
}
document.addEventListener("DOMContentLoaded", initApp);
document.addEventListener("turbo:load", initApp);
document.addEventListener("turbo:render", initApp);
document.addEventListener("turbo:frame-render", initApp);
//# sourceMappingURL=/assets/app.js.map
