const STORAGE_KEY = "theme";

function getStoredTheme() {
    return localStorage.getItem(STORAGE_KEY);
}

function setStoredTheme(theme) {
    localStorage.setItem(STORAGE_KEY, theme);
}

function isDark() {
    return document.documentElement.classList.contains("dark");
}

function applyTheme(theme) {
    document.documentElement.classList.toggle("dark", theme === "dark");
    updateToggleIcons();
}

function updateToggleIcons() {
    document.querySelectorAll("[data-theme-toggle]").forEach((btn) => {
        const sunIcon = btn.querySelector('[data-icon="sun"]');
        const moonIcon = btn.querySelector('[data-icon="moon"]');
        if (!sunIcon || !moonIcon) return;

        sunIcon.classList.toggle("hidden", isDark());
        moonIcon.classList.toggle("hidden", !isDark());
    });
}

function toggleTheme() {
    const newTheme = isDark() ? "light" : "dark";
    setStoredTheme(newTheme);
    applyTheme(newTheme);
}

export function initDarkMode() {
    updateToggleIcons();

    document.querySelectorAll("[data-theme-toggle]").forEach((btn) => {
        btn.addEventListener("click", toggleTheme);
    });
}
