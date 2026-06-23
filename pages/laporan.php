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
$total_utang = totalUtang($conn);

$data_transaksi = mysqli_query($conn, "
    SELECT * FROM transaksi 
    WHERE MONTH(tanggal) = '$bulan_pilihan' 
    AND YEAR(tanggal) = '$tahun_pilihan'
    ORDER BY tanggal DESC, id DESC
");

include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/navbar.php';

$nama_bulan = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
    '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
    '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];
?>

<h1 class="text-3xl font-bold mb-6 dark:text-white">
    Laporan & Rekapitulasi Keuangan
</h1>

<div class="card mb-6">
    <form method="GET" action="" class="flex flex-wrap items-end gap-4">
        <div class="flex-1 min-w-[200px]">
            <label class="block font-medium text-gray-700 dark:text-gray-300 mb-2">Pilih Bulan</label>
            <select name="bulan" class="w-full p-3 border rounded-xl dark:bg-slate-700 dark:text-white dark:border-slate-600 focus:outline-none focus:ring-2 focus:ring-violet-500">
                <?php foreach ($nama_bulan as $key => $value): ?>
                    <option value="<?= $key ?>" <?= $bulan_pilihan == $key ? 'selected' : '' ?>><?= $value ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="flex-1 min-w-[200px]">
            <label class="block font-medium text-gray-700 dark:text-gray-300 mb-2">Pilih Tahun</label>
            <select name="tahun" class="w-full p-3 border rounded-xl dark:bg-slate-700 dark:text-white dark:border-slate-600 focus:outline-none focus:ring-2 focus:ring-violet-500">
                <?php 
                $tahun_sekarang = date('Y');
                for ($t = $tahun_sekarang - 3; $t <= $tahun_sekarang + 2; $t++): 
                ?>
                    <option value="<?= $t ?>" <?= $tahun_pilihan == $t ? 'selected' : '' ?>><?= $t ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <div>
            <button type="submit" class="w-full lg:w-auto bg-violet-500 hover:bg-violet-600 text-white px-6 py-3 rounded-xl font-semibold shadow-md transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-filter"></i> Filter Data
            </button>
        </div>
    </form>
</div>

<div class="grid lg:grid-cols-3 md:grid-cols-2 gap-6 mb-6">
    <div class="card border-l-4 border-green-500">
        <p class="text-gray-500 dark:text-gray-400 font-medium">Pemasukan Bulan Ini</p>
        <h2 class="text-2xl font-bold text-green-500 mt-2"><?= rupiah($pemasukan_bulan_ini); ?></h2>
    </div>

    <div class="card border-l-4 border-red-500">
        <p class="text-gray-500 dark:text-gray-400 font-medium">Pengeluaran Bulan Ini</p>
        <h2 class="text-2xl font-bold text-red-500 mt-2"><?= rupiah($pengeluaran_bulan_ini); ?></h2>
    </div>

    <div class="card border-l-4 border-violet-500">
        <p class="text-gray-500 dark:text-gray-400 font-medium">Selisih Bersih Arus Kas</p>
        <h2 class="text-2xl font-bold mt-2 <?= $bersih_bulan_ini >= 0 ? 'text-violet-500' : 'text-orange-500' ?>">
            <?= rupiah($bersih_bulan_ini); ?>
        </h2>
    </div>

    <div class="card border-l-4 border-blue-500">
        <p class="text-gray-500 dark:text-gray-400 font-medium">Total Seluruh Tabungan</p>
        <h2 class="text-2xl font-bold text-blue-500 mt-2"><?= rupiah($total_tabungan); ?></h2>
    </div>

    <div class="card border-l-4 border-yellow-500">
        <p class="text-gray-500 dark:text-gray-400 font-medium">Total Seluruh Investasi</p>
        <h2 class="text-2xl font-bold text-yellow-500 mt-2"><?= rupiah($total_investasi); ?></h2>
    </div>

    <div class="card border-l-4 border-orange-500">
        <p class="text-gray-500 dark:text-gray-400 font-medium">Total Sisa Utang (Belum Lunas)</p>
        <h2 class="text-2xl font-bold text-orange-500 mt-2"><?= rupiah($total_utang); ?></h2>
    </div>
</div>

<div class="grid lg:grid-cols-3 gap-6 mb-6">
    <div class="card lg:col-span-1 flex flex-col justify-between">
        <div>
            <h2 class="text-lg font-semibold mb-4 dark:text-white">Rasio Kas Bulan Ini</h2>
        </div>
        <div class="relative w-full p-4 flex items-center justify-center">
            <canvas id="reportChart" class="max-w-[220px] max-h-[220px]"></canvas>
        </div>
        <p class="text-xs text-gray-400 text-center mt-4">Perbandingan total uang masuk dan keluar pada periode terpilih.</p>
    </div>

    <div class="card lg:col-span-2">
        <h2 class="text-xl font-semibold mb-4 dark:text-white">Detail Riwayat Transaksi</h2>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-slate-700 text-gray-500 text-sm">
                        <th class="pb-3 font-medium">Tanggal</th>
                        <th class="pb-3 font-medium">Kategori / Keterangan</th>
                        <th class="pb-3 font-medium">Jenis</th>
                        <th class="pb-3 font-medium text-right">Jumlah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-700/50">
                    <?php if (mysqli_num_rows($data_transaksi) > 0): ?>
                        <?php while ($t = mysqli_fetch_assoc($data_transaksi)): ?>
                            <tr class="text-gray-700 dark:text-slate-300 text-sm">
                                <td class="py-3"><?= date('d M Y', strtotime($t['tanggal'])) ?></td>
                                <td class="py-3 font-medium"><?= htmlspecialchars($t['kategori'] ?? $t['keterangan'] ?? 'Transaksi') ?></td>
                                <td class="py-3">
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-md <?= $t['jenis'] == 'Pemasukan' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' ?>">
                                        <?= $t['jenis'] ?>
                                    </span>
                                </td>
                                <td class="py-3 text-right font-semibold <?= $t['jenis'] == 'Pemasukan' ? 'text-green-600' : 'text-red-600' ?>">
                                    <?= ($t['jenis'] == 'Pemasukan' ? '+' : '-') . number_format($t['jumlah'], 0, ',', '.') ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center py-6 text-gray-400">
                                Tidak ada log transaksi masuk atau keluar pada bulan ini.
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
                dataPemasukan == 0 && dataPengeluaran == 0 ? '#E2E8F0' : '#86EFAC', // Abu-abu jika kosong
                '#FCA5A5'
            ],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    boxWidth: 12
                }
            }
        }
    }
});
</script>

<?php
include '../includes/footer.php';
?>