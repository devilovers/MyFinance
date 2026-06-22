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

    <div class="card">
        <p class="text-gray-500 dark:text-gray-400">
            Total Saldo
        </p>

        <h1 class="text-3xl font-bold mt-2 text-violet-500">
            <?= rupiah($saldo); ?>
        </h1>
    </div>

    <div class="card">
        <p class="text-gray-500 dark:text-gray-400">
            Pemasukan
        </p>

        <h1 class="text-3xl font-bold text-green-500 mt-2">
            <?= rupiah($pemasukan); ?>
        </h1>
    </div>

    <div class="card">
        <p class="text-gray-500 dark:text-gray-400">
            Pengeluaran
        </p>

        <h1 class="text-3xl font-bold text-red-500 mt-2">
            <?= rupiah($pengeluaran); ?>
        </h1>
    </div>

    <div class="card">
        <p class="text-gray-500 dark:text-gray-400">
            Tabungan
        </p>

        <h1 class="text-3xl font-bold text-blue-500 mt-2">
            <?= rupiah($tabungan); ?>
        </h1>
    </div>

    <div class="card">
        <p class="text-gray-500 dark:text-gray-400">
            Investasi
        </p>

        <h1 class="text-3xl font-bold text-yellow-500 mt-2">
            <?= rupiah($investasi); ?>
        </h1>
    </div>

    <div class="card">
        <p class="text-gray-500 dark:text-gray-400">
            Utang
        </p>

        <h1 class="text-3xl font-bold text-orange-500 mt-2">
            <?= rupiah($utang); ?>
        </h1>
    </div>

</div>

<!-- Grafik -->
<div class="card mt-6">
    <div class="flex items-center justify-between mb-5">
        <h2 class="text-xl font-semibold dark:text-white">
            Perbandingan Pemasukan & Pengeluaran
        </h2>
    </div>

    <canvas id="financeChart"></canvas>
</div>

<!-- Notifikasi Utang -->
<?php if (mysqli_num_rows($notifikasi) > 0): ?>

<div class="card mt-6">

    <h2 class="text-xl font-semibold text-red-500 mb-4">
        🔔 Pengingat Utang
    </h2>

    <div class="space-y-3">

        <?php while ($u = mysqli_fetch_assoc($notifikasi)) : ?>

            <?php
            $hariIni = date('Y-m-d');
            $jatuhTempo = $u['jatuh_tempo'];

            $selisih = floor(
                (strtotime($jatuhTempo) - strtotime($hariIni))
                / (60 * 60 * 24)
            );
            ?>

            <div
                class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-4 rounded-2xl">

                <h3 class="font-semibold text-red-600 dark:text-red-400">
                    <?= htmlspecialchars($u['nama_utang']) ?>
                </h3>

                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                    Jumlah :
                    <?= rupiah($u['jumlah']) ?>
                </p>

                <p class="text-sm text-gray-600 dark:text-gray-300">
                    Jatuh Tempo :
                    <?= date('d F Y', strtotime($u['jatuh_tempo'])) ?>
                </p>

                <?php if ($selisih < 0): ?>

                    <p class="mt-2 text-red-500 font-semibold">
                        🚨 Utang telah melewati jatuh tempo.
                    </p>

                <?php elseif ($selisih == 0): ?>

                    <p class="mt-2 text-orange-500 font-semibold">
                        ⚠️ Utang jatuh tempo hari ini.
                    </p>

                <?php else: ?>

                    <p class="mt-2 text-orange-500 font-semibold">
                        ⚠️ Utang jatuh tempo dalam
                        <?= $selisih ?>
                        hari lagi.
                    </p>

                <?php endif; ?>

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
            'Pemasukan',
            'Pengeluaran'
        ],
        datasets: [{
            data: [
                <?= $pemasukan ?>,
                <?= $pengeluaran ?>
            ],
            backgroundColor: [
                '#86EFAC',
                '#FCA5A5'
            ],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>

<?php
include '../includes/footer.php';
?>