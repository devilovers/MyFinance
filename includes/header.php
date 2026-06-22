<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>MyFinance</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#A78BFA',
                        secondary: '#C4B5FD',
                        soft: '#F5F3FF'
                    }
                }
            }
        }
    </script>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    />

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css"
    >

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >
</head>

<body
    class="
        bg-violet-50
        dark:bg-slate-900
        text-slate-800
        dark:text-slate-100
        font-[Poppins]
        transition-all
        duration-300
    "
>

<div class="flex min-h-screen">