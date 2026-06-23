<div class="flex-1">

    <nav
        class="
            bg-white dark:bg-slate-800
            shadow-md
            px-6 py-4
            flex
            justify-between
            items-center
            gap-4
        "
    >

        <div>
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white">
                MyFinance Dashboard
            </h2>

            <p class="text-gray-500 dark:text-gray-400 text-sm">
                Kelola keuangan dengan mudah.
            </p>
        </div>

        <div class="flex items-center gap-4">

            <div class="relative hidden md:block">

                <i
                    class="
                        fa-solid fa-magnifying-glass
                        absolute
                        left-4
                        top-1/2
                        -translate-y-1/2
                        text-gray-400
                    "
                ></i>

                <input
                    type="text"
                    id="searchGlobal"
                    placeholder="Pencarian..."
                    class="
                        w-72
                        pl-11
                        pr-4
                        py-3
                        rounded-2xl
                        border
                        border-gray-200
                        dark:border-slate-700
                        bg-gray-50
                        dark:bg-slate-700
                        dark:text-white
                        focus:outline-none
                        focus:ring-2
                        focus:ring-violet-400
                        transition
                    "
                >

            </div>

            <div
                class="
                    hidden lg:flex
                    items-center
                    gap-2
                    text-gray-500
                    dark:text-gray-300
                "
            >
                <i class="fa-regular fa-calendar"></i>

                <span>
                    <?= date('d F Y'); ?>
                </span>
            </div>

            <button
                id="darkButton"
                class="
                    w-11
                    h-11
                    rounded-full
                    bg-violet-500
                    hover:bg-violet-600
                    text-white
                    flex
                    items-center
                    justify-center
                    transition
                "
            >
                <i
                    id="darkIcon"
                    class="fa-solid fa-moon"
                ></i>
            </button>

        </div>

    </nav>

    <div class="p-6">