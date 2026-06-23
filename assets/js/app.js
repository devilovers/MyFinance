const btn = document.getElementById('darkButton');
const icon = document.getElementById('darkIcon');

function updateIconTheme(theme) {
    if (!icon) return;
    if (theme === 'dark') {
        icon.classList.remove('fa-moon');
        icon.classList.add('fa-sun');
    } else {
        icon.classList.remove('fa-sun');
        icon.classList.add('fa-moon');
    }
}

const activeTheme = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
updateIconTheme(activeTheme);

btn?.addEventListener('click', () => {
    const isDark = document.documentElement.classList.contains('dark');
    const newTheme = isDark ? 'light' : 'dark';

    localStorage.setItem('theme', newTheme);
    
    if (newTheme === 'dark') {
        document.documentElement.classList.add('dark');
        document.documentElement.style.backgroundColor = '#020617';
    } else {
        document.documentElement.classList.remove('dark');
        document.documentElement.style.backgroundColor = '#f8fafc';
    }
    
    updateIconTheme(newTheme);
});

document.querySelectorAll('button, .btn-animate, input[type="submit"]').forEach(button => {
    button.addEventListener('mousedown', () => {
        button.style.transform = 'scale(0.95)';
        button.style.transition = 'transform 0.05s ease';
    });
    button.addEventListener('mouseup', () => {
        button.style.transform = '';
    });
    button.addEventListener('mouseleave', () => {
        button.style.transform = '';
    });
});