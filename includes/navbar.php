<div class="flex-1 flex flex-col min-w-0 overflow-x-hidden">

    <nav
        class="
            bg-white dark:bg-slate-800
            border-b border-slate-100 dark:border-slate-700/50
            px-6 py-4
            flex
            justify-between
            items-center
            gap-4
            sticky top-0 z-10
        "
    >

        <div class="flex items-center gap-4">
            <button 
                id="btn-toggle-sidebar" 
                class="w-10 h-10 rounded-xl border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors duration-200 focus:outline-none"
            >
                <i class="fa-solid fa-bars text-lg"></i>
            </button>

            <div>
                <h2 class="text-xl font-bold text-slate-800 dark:text-white tracking-tight">
                    MyFinance Dashboard
                </h2>
                <p class="text-slate-400 dark:text-slate-500 text-xs mt-0.5 hidden sm:block">
                    <?= $lang['managed_by'] ?? 'Managed by'; ?> Nur Islami Sabila
                </p>
            </div>
        </div>

        <div class="flex items-center gap-4">

            <div
                class="
                    hidden lg:flex
                    items-center
                    gap-2
                    text-xs
                    font-medium
                    tracking-wide
                    text-slate-400
                    dark:text-slate-400
                    bg-slate-50 dark:bg-slate-700/30
                    px-3 py-2 rounded-xl
                    border border-slate-100 dark:border-slate-700/30
                "
            >
                <i class="fa-regular fa-calendar text-slate-400"></i>
                <span>
                    <?php 
                    if (($current_lang ?? 'id') === 'en') {
                        echo date('F d, Y');
                    } else {
                        echo date('d F Y');
                    }
                    ?>
                </span>
            </div>

            <div class="flex items-center border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden p-0.5 gap-0.5 bg-slate-50 dark:bg-slate-900">
                <a 
                    href="?lang=id" 
                    class="px-2.5 py-1.5 text-xs font-bold rounded-lg transition-all <?= ($current_lang ?? 'id') === 'id' ? 'bg-violet-500 text-white' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-200' ?>"
                >
                    ID
                </a>
                <a 
                    href="?lang=en" 
                    class="px-2.5 py-1.5 text-xs font-bold rounded-lg transition-all <?= ($current_lang ?? 'id') === 'en' ? 'bg-violet-500 text-white' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-200' ?>"
                >
                    EN
                </a>
            </div>

            <button
                id="darkButton"
                class="
                    w-10
                    h-10
                    rounded-xl
                    bg-violet-500
                    hover:bg-violet-600
                    text-white
                    flex
                    items-center
                    justify-center
                    transition-colors
                    duration-200
                    shadow-sm shadow-violet-500/20
                "
            >
                <i
                    id="darkIcon"
                    class="fa-solid fa-moon text-sm"
                ></i>
            </button>

        </div>

    </nav>

    <div class="p-6 flex-1 bg-slate-50/50 dark:bg-slate-900/40">

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('btn-toggle-sidebar');
        const sidebarBrand = document.getElementById('sidebar-brand');
        const sidebarTexts = document.querySelectorAll('.sidebar-text');

        const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';

        function applySidebarState(collapsed) {
            if (collapsed) {
                sidebar.classList.remove('w-72', 'p-6');
                sidebar.classList.add('w-20', 'px-3', 'py-6');
                if(sidebarBrand) sidebarBrand.classList.add('opacity-0', 'scale-75', 'w-0');
                sidebarTexts.forEach(el => el.classList.add('opacity-0', 'hidden'));
            } else {
                sidebar.classList.remove('w-20', 'px-3', 'py-6');
                sidebar.classList.add('w-72', 'p-6');
                if(sidebarBrand) sidebarBrand.classList.remove('opacity-0', 'scale-75', 'w-0');
                setTimeout(() => {
                    if (!sidebar.classList.contains('w-20')) {
                        sidebarTexts.forEach(el => el.classList.remove('opacity-0', 'hidden'));
                    }
                }, 150);
            }
        }

        applySidebarState(isCollapsed);

        toggleBtn.addEventListener('click', function() {
            const currentlyCollapsed = sidebar.classList.contains('w-20');
            const newState = !currentlyCollapsed;
            
            applySidebarState(newState);
            localStorage.setItem('sidebar-collapsed', newState);
        });
    });
    </script>