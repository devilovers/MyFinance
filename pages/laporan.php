<?php
include '../config/koneksi.php';
include '../helpers/finance_helper.php';

$bulan_pilihan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun_pilihan = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

$q_pemasukan = mysqli_query($conn, "
    SELECT IFNULL(SUM(jumlah), 0) AS total 
    FROM transaksi 
    WHERE jenis = 'Pemasukan' 
    AND MONTH(tanggal) = '$bulan_pilihan' 
    AND YEAR(tanggal) = '$tahun_pilihan'
");
$res_pemasukan = mysqli_fetch_assoc($q_pemasukan);
$pemasukan_bulan_ini = $res_pemasukan['total'];

$q_pengeluaran = mysqli_query($conn, "
    SELECT IFNULL(SUM(jumlah), 0) AS total 
    FROM transaksi 
    WHERE jenis = 'Pengeluaran' 
    AND MONTH(tanggal) = '$bulan_pilihan' 
    AND YEAR(tanggal) = '$tahun_pilihan'
");
$res_pengeluaran = mysqli_fetch_assoc($q_pengeluaran);
$pengeluaran_bulan_ini = $res_pengeluaran['total'];

$bersih_bulan_ini = $pemasukan_bulan_ini - $pengeluaran_bulan_ini;

$total_tabungan = totalTabungan($conn);
$total_investasi = totalInvestasi($conn);

$log_transaksi = mysqli_query($conn, "
    SELECT * FROM transaksi 
    WHERE MONTH(tanggal) = '$bulan_pilihan' 
    AND YEAR(tanggal) = '$tahun_pilihan'
    ORDER BY tanggal DESC, id DESC
");

$bulan_nama = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
    '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
    '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];

include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/navbar.php';
?>

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white tracking-tight">
            Laporan & Analisis Bulanan
        </h1>
        <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
            Pantau rincian arus kas masuk, keluar, dan rasio keuangan Anda secara periodik.
        </p>
    </div>
</div>

<div class="p-6 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/50 rounded-2xl shadow-sm mb-6">
    <form method="GET" action="" class="flex flex-col sm:flex-row gap-3">
        <div class="flex-1 grid grid-cols-2 gap-3">
            <div>
                <label class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block mb-1.5">Pilih Bulan</label>
                <select 
                    name="bulan" 
                    class="w-full px-3 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400/20 focus:border-violet-400 transition-all"
                >
                    <?php foreach ($bulan_nama as $key => $nama): ?>
                        <option value="<?= $key ?>" <?= $bulan_pilihan == $key ? 'selected' : '' ?>>
                            <?= $nama ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block mb-1.5">Pilih Tahun</label>
                <select 
                    name="tahun" 
                    class="w-full px-3 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400/20 focus:border-violet-400 transition-all"
                >
                    <?php 
                    $tahun_sekarang = date('Y');
                    for ($i = $tahun_sekarang - 3; $i <= $tahun_sekarang + 3; $i++): 
                    ?>
                        <option value="<?= $i ?>" <?= $tahun_pilihan == $i ? 'selected' : '' ?>>
                            <?= $i ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
        </div>
        <div class="flex items-end">
            <button 
                type="submit" 
                class="w-full sm:w-auto px-6 py-2.5 bg-violet-500 hover:bg-violet-600 text-white rounded-xl font-medium text-sm shadow-sm shadow-violet-500/10 transition-colors duration-150 h-[42px]"
            >
                Terapkan Filter
            </button>
        </div>
    </form>
</div>

<div class="grid md:grid-cols-3 gap-6 mb-6">
    <div class="p-5 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/50 rounded-2xl shadow-sm flex items-center gap-4">
        <span class="w-12 h-12 rounded-xl bg-green-50 dark:bg-green-950/30 text-green-500 flex items-center justify-center text-lg shrink-0">
            <i class="fa-solid fa-arrow-down-long"></i>
        </span>
        <div class="min-w-0">
            <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Total Pemasukan</p>
            <h3 class="text-lg font-bold text-slate-800 dark:text-white tracking-tight mt-0.5 truncate"><?= rupiah($pemasukan_bulan_ini) ?></h3>
        </div>
    </div>

    <div class="p-5 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/50 rounded-2xl shadow-sm flex items-center gap-4">
        <span class="w-12 h-12 rounded-xl bg-red-50 dark:bg-red-950/30 text-red-500 flex items-center justify-center text-lg shrink-0">
            <i class="fa-solid fa-arrow-up-long"></i>
        </span>
        <div class="min-w-0">
            <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Total Pengeluaran</p>
            <h3 class="text-lg font-bold text-slate-800 dark:text-white tracking-tight mt-0.5 truncate"><?= rupiah($pengeluaran_bulan_ini) ?></h3>
        </div>
    </div>

    <div class="p-5 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/50 rounded-2xl shadow-sm flex items-center gap-4">
        <span class="w-12 h-12 rounded-xl <?= $bersih_bulan_ini >= 0 ? 'bg-blue-50 dark:bg-blue-950/30 text-blue-500' : 'bg-rose-50 dark:bg-rose-950/30 text-rose-500' ?> flex items-center justify-center text-lg shrink-0">
            <i class="fa-solid fa-scale-balanced"></i>
        </span>
        <div class="min-w-0">
            <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Selisih Bersih</p>
            <h3 class="text-lg font-bold tracking-tight mt-0.5 truncate <?= $bersih_bulan_ini >= 0 ? 'text-blue-500' : 'text-rose-500' ?>">
                <?= rupiah($bersih_bulan_ini) ?>
            </h3>
        </div>
    </div>
</div>

<div class="grid lg:grid-cols-3 gap-6">
    <div class="p-6 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/50 rounded-2xl shadow-sm flex flex-col justify-between h-fit">
        <div>
            <h3 class="text-sm font-bold text-slate-800 dark:text-white tracking-tight mb-1">Rasio Alokasi Dana</h3>
            <p class="text-xs text-slate-400 dark:text-slate-500">Perbandingan persentase arus dana masuk dan keluar.</p>
        </div>
        <div class="py-6 flex items-center justify-center relative">
            <div class="w-48 h-48">
                <canvas id="reportChart"></canvas>
            </div>
        </div>
        <div class="space-y-2 border-t border-slate-100 dark:border-slate-800/60 pt-4">
            <div class="flex items-center justify-between text-xs font-medium">
                <div class="flex items-center gap-2 text-slate-600 dark:text-slate-400">
                    <span class="w-2.5 h-2.5 rounded-full bg-green-400 shrink-0"></span>
                    <span>Pemasukan</span>
                </div>
                <span class="text-slate-800 dark:text-white font-semibold"><?= rupiah($pemasukan_bulan_ini) ?></span>
            </div>
            <div class="flex items-center justify-between text-xs font-medium">
                <div class="flex items-center gap-2 text-slate-600 dark:text-slate-400">
                    <span class="w-2.5 h-2.5 rounded-full bg-red-400 shrink-0"></span>
                    <span>Pengeluaran</span>
                </div>
                <span class="text-slate-800 dark:text-white font-semibold"><?= rupiah($pengeluaran_bulan_ini) ?></span>
            </div>
        </div>
    </div>

    <div class="lg:col-span-2 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/50 rounded-2xl shadow-sm overflow-hidden flex flex-col">
        <div class="p-6 border-b border-slate-100 dark:border-slate-800/60">
            <h3 class="text-sm font-bold text-slate-800 dark:text-white tracking-tight mb-1">Log Aliran Transaksi</h3>
            <p class="text-xs text-slate-400 dark:text-slate-500">Daftar mutasi rekening keuangan yang tercatat pada periode ini.</p>
        </div>
        <div class="overflow-x-auto flex-1">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/20">
                        <th class="px-6 py-3.5 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Tanggal</th>
                        <th class="px-6 py-3.5 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Kategori</th>
                        <th class="px-6 py-3.5 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Jenis</th>
                        <th class="px-6 py-3.5 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 text-right">Jumlah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                    <?php if (mysqli_num_rows($log_transaksi) > 0): ?>
                        <?php while ($log = mysqli_fetch_assoc($log_transaksi)): ?>
                            <tr class="hover:bg-slate-50/40 dark:hover:bg-slate-800/20 transition-colors duration-150">
                                <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400 whitespace-nowrap">
                                    <?= date('d M Y', strtotime($log['tanggal'])) ?>
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-slate-800 dark:text-slate-200 whitespace-nowrap">
                                    <?= htmlspecialchars($log['kategori']) ?>
                                </td>
                                <td class="px-6 py-4 text-sm whitespace-nowrap">
                                    <?php if (strtolower($log['jenis']) == 'pemasukan'): ?>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-medium bg-green-50 text-green-700 dark:bg-green-950/30 dark:text-green-400">
                                            Masuk
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-medium bg-red-50 text-red-700 dark:bg-red-950/30 dark:text-red-400">
                                            Keluar
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-sm font-bold text-right whitespace-nowrap <?= strtolower($log['jenis']) == 'pemasukan' ? 'text-green-500' : 'text-red-500' ?>">
                                    <?= strtolower($log['jenis']) == 'pemasukan' ? '+' : '-' ?> <?= rupiah($log['jumlah']) ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <span class="w-10 h-10 rounded-xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-400 mx-auto mb-3 text-base">
                                    <i class="fa-solid fa-folder-open"></i>
                                </span>
                                <p class="text-sm font-medium text-slate-400 dark:text-slate-500">
                                    Tidak ada log transaksi masuk atau keluar pada bulan ini.
                                </p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
const reportCtx = document.getElementById('reportChart');

const dataPemasukan = <?= $pemasukan_bulan_ini ?>;
const dataPengeluaran = <?= $pengeluaran_bulan_ini ?>;

new Chart(reportCtx, {
    type: 'pie',
    data: {
        labels: ['Pemasukan', 'Pengeluaran'],
        datasets: [{
            data: [
                dataPemasukan == 0 && dataPengeluaran == 0 ? 1 : dataPemasukan, 
                dataPengeluaran
            ],
            backgroundColor: [
                dataPemasukan == 0 && dataPengeluaran == 0 ? '#cbd5e1' : '#34d399',
                '#f87171'
            ],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        }
    }
});
</script>

<?php
include '../includes/footer.php';
?>