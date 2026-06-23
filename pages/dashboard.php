<?php
include '../config/koneksi.php';
include '../helpers/finance_helper.php';

$saldo = totalSaldo($conn);
$pemasukan = totalPemasukan($conn);
$pengeluaran = totalPengeluaran($conn);
$tabungan = totalTabungan($conn);
$investasi = totalInvestasi($conn);
$utang = totalUtang($conn);
$notifikasi = utangJatuhTempo($conn);

include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/navbar.php';
?>

<div class="grid lg:grid-cols-3 md:grid-cols-2 gap-6">

    <div class="p-6 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/50 rounded-2xl shadow-sm transition-all duration-200 hover:shadow-md">
        <div class="flex items-center justify-between">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                Total Saldo
            </p>
            <span class="w-7 h-7 rounded-lg bg-violet-50 dark:bg-violet-950/30 flex items-center justify-center text-violet-500 text-sm">
                <i class="fa-solid fa-scale-balanced"></i>
            </span>
        </div>
        <h1 class="text-2xl font-bold mt-4 text-violet-500 tracking-tight">
            <?= rupiah($saldo); ?>
        </h1>
    </div>

    <div class="p-6 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/50 rounded-2xl shadow-sm transition-all duration-200 hover:shadow-md">
        <div class="flex items-center justify-between">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                Pemasukan
            </p>
            <span class="w-7 h-7 rounded-lg bg-green-50 dark:bg-green-950/30 flex items-center justify-center text-green-500 text-sm">
                <i class="fa-solid fa-arrow-trend-up"></i>
            </span>
        </div>
        <h1 class="text-2xl font-bold mt-4 text-green-500 tracking-tight">
            <?= rupiah($pemasukan); ?>
        </h1>
    </div>

    <div class="p-6 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/50 rounded-2xl shadow-sm transition-all duration-200 hover:shadow-md">
        <div class="flex items-center justify-between">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                Pengeluaran
            </p>
            <span class="w-7 h-7 rounded-lg bg-red-50 dark:bg-red-950/30 flex items-center justify-center text-red-500 text-sm">
                <i class="fa-solid fa-arrow-trend-down"></i>
            </span>
        </div>
        <h1 class="text-2xl font-bold mt-4 text-red-500 tracking-tight">
            <?= rupiah($pengeluaran); ?>
        </h1>
    </div>

    <div class="p-6 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/50 rounded-2xl shadow-sm transition-all duration-200 hover:shadow-md">
        <div class="flex items-center justify-between">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                Tabungan
            </p>
            <span class="w-7 h-7 rounded-lg bg-blue-50 dark:bg-blue-950/30 flex items-center justify-center text-blue-500 text-sm">
                <i class="fa-solid fa-piggy-bank"></i>
            </span>
        </div>
        <h1 class="text-2xl font-bold mt-4 text-blue-500 tracking-tight">
            <?= rupiah($tabungan); ?>
        </h1>
    </div>

    <div class="p-6 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/50 rounded-2xl shadow-sm transition-all duration-200 hover:shadow-md">
        <div class="flex items-center justify-between">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                Investasi
            </p>
            <span class="w-7 h-7 rounded-lg bg-yellow-50 dark:bg-yellow-950/30 flex items-center justify-center text-yellow-500 text-sm">
                <i class="fa-solid fa-chart-pie"></i>
            </span>
        </div>
        <h1 class="text-2xl font-bold mt-4 text-yellow-500 tracking-tight">
            <?= rupiah($investasi); ?>
        </h1>
    </div>

    <div class="p-6 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/50 rounded-2xl shadow-sm transition-all duration-200 hover:shadow-md">
        <div class="flex items-center justify-between">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                Utang
            </p>
            <span class="w-7 h-7 rounded-lg bg-orange-50 dark:bg-orange-950/30 flex items-center justify-center text-orange-500 text-sm">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </span>
        </div>
        <h1 class="text-2xl font-bold mt-4 text-orange-500 tracking-tight">
            <?= rupiah($utang); ?>
        </h1>
    </div>

</div>

<div class="p-6 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/50 rounded-2xl shadow-sm mt-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-base font-bold text-slate-800 dark:text-white tracking-tight">
            Alokasi Keuangan & Penggunaan Dana
        </h2>
    </div>

    <div class="relative w-full mx-auto max-h-[320px] flex justify-center">
        <canvas id="financeChart"></canvas>
    </div>
</div>

<?php if (mysqli_num_rows($notifikasi) > 0): ?>

<div class="p-6 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/50 rounded-2xl shadow-sm mt-6">

    <h2 class="text-base font-bold text-red-500 mb-4 tracking-tight flex items-center gap-2">
        <i class="fa-solid fa-bell text-sm"></i> Pengingat Utang Berdasarkan Jatuh Tempo
    </h2>

    <div class="space-y-4">

        <?php while ($u = mysqli_fetch_assoc($notifikasi)) : ?>

            <?php
            $hariIni = date('Y-m-d');
            $jatuhTempo = $u['jatuh_tempo'];

            $selisih = floor(
                (strtotime($jatuhTempo) - strtotime($hariIni))
                / (60 * 60 * 24)
            );
            ?>

            <div class="bg-slate-50/60 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-700/60 p-5 rounded-xl flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                
                <div>
                    <h3 class="font-bold text-slate-800 dark:text-slate-200 text-sm">
                        <?= htmlspecialchars($u['nama_utang']) ?>
                    </h3>

                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-1.5 text-xs text-slate-500 dark:text-slate-400">
                        <p>
                            Jumlah: <span class="font-semibold text-slate-700 dark:text-slate-300"><?= rupiah($u['jumlah']) ?></span>
                        </p>
                        <span class="hidden md:inline text-slate-300 dark:text-slate-600">•</span>
                        <p>
                            Jatuh Tempo: <span class="font-semibold text-slate-700 dark:text-slate-300"><?= date('d F Y', strtotime($u['jatuh_tempo'])) ?></span>
                        </p>
                    </div>
                </div>

                <div class="text-left md:text-right shrink-0">
                    <?php if ($selisih < 0): ?>
                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-semibold bg-red-50 text-red-600 border border-red-100 dark:bg-red-950/30 dark:text-red-400 dark:border-red-900/40">
                            Lewat jatuh tempo
                        </span>
                    <?php elseif ($selisih == 0): ?>
                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-semibold bg-orange-50 text-orange-600 border border-orange-100 dark:bg-orange-950/30 dark:text-orange-400 dark:border-orange-900/40">
                            Jatuh tempo hari ini
                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-100 dark:bg-amber-950/20 dark:text-amber-400 dark:border-amber-900/40">
                            <?= $selisih ?> hari lagi
                        </span>
                    <?php endif; ?>
                </div>

            </div>

        <?php endwhile; ?>

    </div>

</div>

<?php endif; ?>

<script>
const ctx = document.getElementById('financeChart');

new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: [
            'Sisa Saldo',
            'Pengeluaran',
            'Tabungan',
            'Investasi'
        ],
        datasets: [{
            data: [
                <?= $saldo ?>,
                <?= $pengeluaran ?>,
                <?= $tabungan ?>,
                <?= $investasi ?>
            ],
            backgroundColor: [
                '#86EFAC',
                '#FCA5A5',
                '#93C5FD',
                '#FDE047'
            ],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 20,
                    usePointStyle: true,
                    font: {
                        size: 11,
                        weight: '500'
                    }
                }
            }
        }
    }
});
</script>

<?php
include '../includes/footer.php';
?>