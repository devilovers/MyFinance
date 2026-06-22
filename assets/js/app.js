const btn = document.getElementById('darkButton');
const icon = document.getElementById('darkIcon');

const prefersDark = window.matchMedia(
    '(prefers-color-scheme: dark)'
).matches;

function setTheme(theme) {
    if (theme === 'dark') {
        document.documentElement.classList.add('dark');

        if (icon) {
            icon.classList.remove('fa-moon');
            icon.classList.add('fa-sun');
        }
    } else {
        document.documentElement.classList.remove('dark');

        if (icon) {
            icon.classList.remove('fa-sun');
            icon.classList.add('fa-moon');
        }
    }
}

let savedTheme = localStorage.getItem('theme');

if (!savedTheme) {
    savedTheme = prefersDark ? 'dark' : 'light';
    localStorage.setItem('theme', savedTheme);
}

setTheme(savedTheme);

btn?.addEventListener('click', () => {
    const isDark =
        document.documentElement.classList.contains('dark');

    const newTheme = isDark ? 'light' : 'dark';

    localStorage.setItem('theme', newTheme);
    setTheme(newTheme);
});