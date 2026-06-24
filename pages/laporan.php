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

$months_list = [
    '01' => $lang['januari'] ?? 'Januari',
    '02' => $lang['februari'] ?? 'Februari',
    '03' => $lang['maret'] ?? 'Maret',
    '04' => $lang['april'] ?? 'April',
    '05' => $lang['mei'] ?? 'Mei',
    '06' => $lang['juni'] ?? 'Juni',
    '07' => $lang['juli'] ?? 'Juli',
    '08' => $lang['agustus'] ?? 'Agustus',
    '09' => $lang['september'] ?? 'September',
    '10' => $lang['oktober'] ?? 'Oktober',
    '11' => $lang['november'] ?? 'November',
    '12' => $lang['desember'] ?? 'Desember'
];

include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/navbar.php';
?>

<div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white tracking-tight">
            <?= $lang['laporan_keuangan_bulanan'] ?? 'Laporan Keuangan Bulanan'; ?>
        </h1>
        <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
            <?= $lang['sub_laporan'] ?? 'Analisis komprehensif arus kas masuk, keluar, dan alokasi dana.'; ?>
        </p>
    </div>

    <form method="GET" class="flex flex-wrap items-center gap-2.5 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/50 p-2 rounded-2xl shadow-sm">
        <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 pl-2 hidden sm:inline">
            <?= $lang['pilih_bulan_tahun'] ?? 'Pilih Bulan & Tahun'; ?>:
        </span>
        
        <select name="bulan" class="px-3 py-2 text-xs font-medium rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white focus:outline-none focus:border-violet-400 transition-colors">
            <?php foreach($months_list as $num => $name): ?>
                <option value="<?= $num ?>" <?= $bulan_pilihan == $num ? 'selected' : '' ?>><?= $name ?></option>
            <?php endforeach; ?>
        </select>

        <select name="tahun" class="px-3 py-2 text-xs font-medium rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white focus:outline-none focus:border-violet-400 transition-colors">
            <?php 
            $tahun_sekarang = date('Y');
            for($y = $tahun_sekarang - 3; $y <= $tahun_sekarang + 3; $y++): 
            ?>
                <option value="<?= $y ?>" <?= $tahun_pilihan == $y ? 'selected' : '' ?>><?= $y ?></option>
            <?php endfor; ?>
        </select>

        <button type="submit" class="bg-violet-500 hover:bg-violet-600 text-white px-4 py-2 rounded-xl font-semibold text-xs transition-colors shadow-sm shadow-violet-500/10">
            <?= $lang['tampilkan'] ?? 'Tampilkan'; ?>
        </button>
    </form>
</div>

<div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 flex flex-col gap-6">
        <div class="p-6 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/50 rounded-2xl shadow-sm">
            <h2 class="text-base font-bold text-slate-800 dark:text-white tracking-tight mb-5">
                <?= $lang['ringkasan_bulan_ini'] ?? 'Ringkasan Bulan Ini'; ?> (<?= $months_list[$bulan_pilihan] ?> <?= $tahun_pilihan ?>)
            </h2>

            <div class="grid sm:grid-cols-3 gap-4">
                <div class="p-4 rounded-xl bg-green-50/50 dark:bg-green-950/10 border border-green-100/50 dark:border-green-900/20">
                    <p class="text-[10px] font-bold text-green-600 dark:text-green-400 uppercase tracking-wider"><?= $lang['pemasukan'] ?? 'Pemasukan'; ?></p>
                    <h3 class="text-lg font-bold text-green-600 dark:text-green-400 mt-2 tracking-tight"><?= rupiah($pemasukan_bulan_ini) ?></h3>
                </div>

                <div class="p-4 rounded-xl bg-red-50/50 dark:bg-red-950/10 border border-slate-100/50 dark:border-red-900/20">
                    <p class="text-[10px] font-bold text-red-500 dark:text-red-400 uppercase tracking-wider"><?= $lang['pengeluaran'] ?? 'Pengeluaran'; ?></p>
                    <h3 class="text-lg font-bold text-red-500 dark:text-red-400 mt-2 tracking-tight"><?= rupiah($pengeluaran_bulan_ini) ?></h3>
                </div>

                <div class="p-4 rounded-xl <?= $bersih_bulan_ini >= 0 ? 'bg-violet-50/50 dark:bg-violet-950/10 border border-violet-100/50' : 'bg-orange-50/50 dark:bg-orange-950/10 border border-orange-100/50' ?> dark:border-slate-800/50">
                    <p class="text-[10px] font-bold <?= $bersih_bulan_ini >= 0 ? 'text-violet-500 dark:text-violet-400' : 'text-orange-500 dark:text-orange-400' ?> uppercase tracking-wider"><?= $lang['sisa_kas'] ?? 'Sisa Kas (Net)'; ?></p>
                    <h3 class="text-lg font-bold <?= $bersih_bulan_ini >= 0 ? 'text-violet-500 dark:text-violet-400' : 'text-orange-500 dark:text-orange-400' ?> mt-2 tracking-tight"><?= rupiah($bersih_bulan_ini) ?></h3>
                </div>
            </div>
        </div>

        <div class="p-6 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/50 rounded-2xl shadow-sm">
            <h2 class="text-base font-bold text-slate-800 dark:text-white tracking-tight mb-5">
                <?= $lang['total_saldo'] ?? 'Total Saldo'; ?> & Asset Accumulation
            </h2>
            <div class="grid sm:grid-cols-2 gap-4">
                <div class="p-4 rounded-xl bg-slate-50/60 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-700/60 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider"><?= $lang['tabungan'] ?? 'Tabungan'; ?></p>
                        <h3 class="text-base font-bold text-slate-800 dark:text-white mt-1.5 tracking-tight"><?= rupiah($total_tabungan) ?></h3>
                    </div>
                    <span class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-950/30 flex items-center justify-center text-blue-500 text-sm"><i class="fa-solid fa-piggy-bank"></i></span>
                </div>

                <div class="p-4 rounded-xl bg-slate-50/60 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-700/60 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider"><?= $lang['investasi'] ?? 'Investasi'; ?></p>
                        <h3 class="text-base font-bold text-slate-800 dark:text-white mt-1.5 tracking-tight"><?= rupiah($total_investasi) ?></h3>
                    </div>
                    <span class="w-8 h-8 rounded-lg bg-yellow-50 dark:bg-yellow-950/30 flex items-center justify-center text-yellow-500 text-sm"><i class="fa-solid fa-chart-pie"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="p-6 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/50 rounded-2xl shadow-sm flex flex-col justify-between">
        <h2 class="text-base font-bold text-slate-800 dark:text-white tracking-tight mb-4">
            <?= $lang['grafik_perbandingan'] ?? 'Grafik Perbandingan'; ?>
        </h2>
        <div class="relative w-full flex justify-center items-center flex-1 max-h-[240px]">
            <canvas id="reportChart"></canvas>
        </div>
    </div>
</div>

<div class="mt-6 p-6 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/50 rounded-2xl shadow-sm">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-base font-bold text-slate-800 dark:text-white tracking-tight">
            <?= $lang['log_transaksi_bulan_ini'] ?? 'Log Transaksi Bulan Ini'; ?>
        </h2>
    </div>

    <div class="overflow-x-auto -mx-6 sm:mx-0">
        <div class="inline-block min-w-full align-middle sm:px-0">
            <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
                <thead>
                    <tr class="text-left text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                        <th class="pb-4 px-6 sm:px-4"><?= $lang['tanggal'] ?? 'Tanggal'; ?></th>
                        <th class="pb-4 px-4"><?= $lang['kategori'] ?? 'Kategori'; ?></th>
                        <th class="pb-4 px-4"><?= $lang['deskripsi'] ?? 'Deskripsi'; ?></th>
                        <th class="pb-4 px-4 text-right"><?= $lang['jumlah'] ?? 'Jumlah'; ?></th>
                        <th class="pb-4 px-6 sm:px-4 text-center"><?= $lang['jenis'] ?? 'Jenis'; ?></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm text-slate-700 dark:text-slate-300">
                    <?php if(mysqli_num_rows($log_transaksi) > 0): ?>
                        <?php while($t = mysqli_fetch_assoc($log_transaksi)): ?>
                            <tr>
                                <td class="py-4 px-6 sm:px-4 font-medium text-xs whitespace-nowrap text-slate-400">
                                    <?php 
                                    if (($current_lang ?? 'id') === 'en') {
                                        echo date('M d, Y', strtotime($t['tanggal']));
                                    } else {
                                        echo date('d M Y', strtotime($t['tanggal']));
                                    }
                                    ?>
                                </td>
                                <td class="py-4 px-4 font-semibold text-xs text-slate-800 dark:text-slate-200"><?= htmlspecialchars($t['kategori']) ?></td>
                                <td class="py-4 px-4 text-xs max-w-[200px] truncate" title="<?= htmlspecialchars($t['deskripsi']) ?>"><?= htmlspecialchars($t['deskripsi']) ?></td>
                                <td class="py-4 px-4 text-right font-bold text-xs whitespace-nowrap <?= $t['jenis'] == 'Pemasukan' ? 'text-green-500' : 'text-red-500' ?>">
                                    <?= $t['jenis'] == 'Pemasukan' ? '+' : '-' ?> <?= rupiah($t['jumlah']) ?>
                                </td>
                                <td class="py-4 px-6 sm:px-4 text-center whitespace-nowrap">
                                    <?php if($t['jenis'] == 'Pemasukan'): ?>
                                        <span class="inline-flex px-2 py-1 rounded-md text-[10px] font-bold bg-green-50 text-green-600 dark:bg-green-950/20 dark:text-green-400">
                                            <?= $lang['pemasukan'] ?? 'Pemasukan'; ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex px-2 py-1 rounded-md text-[10px] font-bold bg-red-50 text-red-600 dark:bg-red-950/20 dark:text-red-400">
                                            <?= $lang['pengeluaran'] ?? 'Pengeluaran'; ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="py-12 text-center">
                                <span class="w-10 h-10 rounded-xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-400 mx-auto mb-3 text-base">
                                    <i class="fa-solid fa-folder-open"></i>
                                </span>
                                <p class="text-sm font-medium text-slate-400 dark:text-slate-500">
                                    <?= $lang['tidak_ada_log_transaksi'] ?? 'Tidak ada log transaksi masuk atau keluar pada bulan ini.'; ?>
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
        labels: [
            '<?= $lang['pemasukan'] ?? 'Pemasukan'; ?>', 
            '<?= $lang['pengeluaran'] ?? 'Pengeluaran'; ?>'
        ],
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
                position: 'bottom',
                labels: {
                    padding: 14,
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