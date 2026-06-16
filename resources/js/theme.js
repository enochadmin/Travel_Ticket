export function initTheme() {
    const root = document.documentElement;
    const storedTheme = localStorage.getItem('theme');

    if (storedTheme === 'dark') {
        root.classList.add('dark');
    } else if (storedTheme === 'light') {
        root.classList.remove('dark');
    }

    const toggles = document.querySelectorAll('[data-theme-toggle]');
    toggles.forEach((toggle) => {
        if (toggle.dataset.bound === 'true') {
            return;
        }

        toggle.dataset.bound = 'true';
        toggle.addEventListener('click', () => {
            const isDark = root.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            window.dispatchEvent(new Event('themechange'));
        });
    });
}

// Apply before paint when loaded via Vite
if (typeof document !== 'undefined') {
    const storedTheme = localStorage.getItem('theme');
    if (storedTheme === 'dark') {
        document.documentElement.classList.add('dark');
    }
}
