<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include '../config/koneksi.php';
include '../helpers/finance_helper.php';

$search = $_GET['search'] ?? '';

if ($search != '') {
    $search = mysqli_real_escape_string($conn, $search);
    $transaksi = mysqli_query($conn, "
        SELECT *
        FROM transaksi
        WHERE jenis LIKE '%$search%'
           OR kategori LIKE '%$search%'
           OR metode_pembayaran LIKE '%$search%'
           OR deskripsi LIKE '%$search%'
        ORDER BY tanggal DESC, id DESC
    ");
} else {
    $transaksi = mysqli_query($conn, "
        SELECT *
        FROM transaksi
        ORDER BY tanggal DESC, id DESC
    ");
}

include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/navbar.php';
?>

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white tracking-tight">
            <?= $lang['riwayat_transaksi_keuangan'] ?? 'Riwayat Transaksi Keuangan'; ?>
        </h1>
        <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
            <?= $lang['sub_transaksi'] ?? 'Catat dan tinju seluruh arus kas masuk maupun keluar secara berkala.'; ?>
        </p>
    </div>

    <button
        id="btnTambah"
        class="inline-flex items-center justify-center gap-2 bg-violet-500 hover:bg-violet-600 text-white px-4 py-2.5 rounded-xl font-medium text-sm shadow-sm shadow-violet-500/10 transition-colors duration-200"
    >
        <i class="fa-solid fa-plus text-xs"></i>
        <?= $lang['tambah_transaksi'] ?? 'Tambah Transaksi'; ?>
    </button>
</div>

<div class="p-6 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/50 rounded-2xl shadow-sm mb-6">
    <form method="GET" action="" class="flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
            <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input
                type="text"
                name="search"
                value="<?= htmlspecialchars($search) ?>"
                placeholder="<?= $lang['cari_transaksi_placeholder'] ?? 'Cari kategori, jenis, metode, atau deskripsi transaksi...'; ?>"
                class="w-full pl-11 pr-4 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400/20 focus:border-violet-400 transition-all"
            >
        </div>
        <div class="flex gap-2">
            <button
                type="submit"
                class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl font-medium text-sm transition-colors duration-150"
            >
                <?= $lang['cari'] ?? 'Cari'; ?>
            </button>
            <?php if ($search != ''): ?>
                <a
                    href="transaksi.php"
                    class="px-4 py-2.5 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-xl flex items-center justify-center transition-colors text-sm font-medium"
                >
                    <?= $lang['reset'] ?? 'Reset'; ?>
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/50 rounded-2xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/20">
                    <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500"><?= $lang['tanggal'] ?? 'Tanggal'; ?></th>
                    <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500"><?= $lang['jenis'] ?? 'Jenis'; ?></th>
                    <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500"><?= $lang['kategori'] ?? 'Kategori'; ?></th>
                    <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500"><?= $lang['metode'] ?? 'Metode'; ?></th>
                    <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500"><?= $lang['jumlah'] ?? 'Jumlah'; ?></th>
                    <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500"><?= $lang['deskripsi'] ?? 'Deskripsi'; ?></th>
                    <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 text-right"><?= $lang['aksi'] ?? 'Aksi'; ?></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                <?php if (mysqli_num_rows($transaksi) > 0): ?>
                    <?php while ($t = mysqli_fetch_assoc($transaksi)): ?>
                        <tr class="hover:bg-slate-50/40 dark:hover:bg-slate-800/20 transition-colors duration-150">
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400 whitespace-nowrap">
                                <?php 
                                if (($current_lang ?? 'id') === 'en') {
                                    echo date('M d, Y', strtotime($t['tanggal']));
                                } else {
                                    echo date('d M Y', strtotime($t['tanggal']));
                                }
                                ?>
                            </td>
                            <td class="px-6 py-4 text-sm whitespace-nowrap">
                                <?php if (strtolower($t['jenis']) == 'pemasukan'): ?>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-green-50 text-green-600 dark:bg-green-950/30 dark:text-green-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> <?= $lang['pemasukan'] ?? 'Pemasukan'; ?>
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-red-50 text-red-600 dark:bg-red-950/30 dark:text-red-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> <?= $lang['pengeluaran'] ?? 'Pengeluaran'; ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-slate-800 dark:text-slate-200 whitespace-nowrap">
                                <?php 
                                // Menerjemahkan kategori lama yang tersimpan dalam format teks bahasa indonesia di DB
                                $key_kategori = 'kategori_' . strtolower($t['kategori']);
                                echo htmlspecialchars($lang[$key_kategori] ?? $t['kategori']); 
                                ?>
                            </td>
                            <td class="px-6 py-4 text-sm whitespace-nowrap">
                                <?php if ($t['metode_pembayaran'] == 'Cash'): ?>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-medium bg-amber-50 text-amber-700 dark:bg-amber-950/30 dark:text-amber-400 border border-amber-200/50 dark:border-amber-900/30">
                                        <i class="fa-solid fa-money-bill-wave text-[10px]"></i> Cash
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-medium bg-blue-50 text-blue-700 dark:bg-blue-950/30 dark:text-blue-400 border border-blue-200/50 dark:border-blue-900/30">
                                        <i class="fa-solid fa-wallet text-[10px]"></i> E-Wallet
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-sm font-bold whitespace-nowrap <?= strtolower($t['jenis']) == 'pemasukan' ? 'text-green-500' : 'text-red-500' ?>">
                                <?= strtolower($t['jenis']) == 'pemasukan' ? '+' : '-' ?> <?= rupiah($t['jumlah']) ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400 max-w-xs truncate">
                                <?= htmlspecialchars($t['deskripsi'] ?: '-') ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button
                                        type="button"
                                        class="btnEdit border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 w-8 h-8 rounded-lg flex items-center justify-center transition-colors"
                                        data-id="<?= $t['id'] ?>"
                                        data-jenis="<?= htmlspecialchars($t['jenis']) ?>"
                                        data-kategori="<?= htmlspecialchars($t['kategori']) ?>"
                                        data-metode="<?= htmlspecialchars($t['metode_pembayaran']) ?>"
                                        data-jumlah="<?= $t['jumlah'] ?>"
                                        data-tanggal="<?= $t['tanggal'] ?>"
                                        data-deskripsi="<?= htmlspecialchars($t['deskripsi']) ?>"
                                    >
                                        <i class="fa-solid fa-pen text-xs"></i>
                                    </button>
                                    <button
                                        type="button"
                                        class="btnHapus bg-red-50 dark:bg-red-950/20 text-red-500 hover:bg-red-100 dark:hover:bg-red-950/40 w-8 h-8 rounded-lg flex items-center justify-center transition-colors"
                                        data-id="<?= $t['id'] ?>"
                                    >
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <span class="w-10 h-10 rounded-xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-400 mx-auto mb-3 text-base">
                                <i class="fa-solid fa-folder-open"></i>
                            </span>
                            <p class="text-sm font-medium text-slate-400 dark:text-slate-500">
                                <?= $lang['tidak_ada_transaksi_ditemukan'] ?? 'Tidak ada data transaksi ditemukan.'; ?>
                            </p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div
    id="modalTambah"
    class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4 transition-all"
>
    <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl p-6 w-full max-w-md shadow-xl transform transition-all animate-in fade-in zoom-in-95 duration-200 relative">
        <button type="button" class="btnBatalClose absolute right-6 top-6 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 focus:outline-none">
            <i class="fa-solid fa-xmark text-sm"></i>
        </button>

        <div class="text-center mb-5">
            <h2 class="text-lg font-bold text-slate-800 dark:text-white tracking-tight">
                <?= $lang['tambah_transaksi_baru'] ?? 'Tambah Transaksi Baru'; ?>
            </h2>
        </div>

        <form action="../process/transaksi/tambah.php" method="POST">
            <div class="space-y-4">
                <div>
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        <?= $lang['jenis_transaksi'] ?? 'Jenis Transaksi'; ?>
                    </label>
                    <select
                        name="jenis"
                        id="jenis"
                        class="w-full mt-1.5 px-3 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400/20 focus:border-violet-400 transition-all"
                        required
                    >
                        <option value="Pemasukan"><?= $lang['pemasukan'] ?? 'Pemasukan'; ?></option>
                        <option value="Pengeluaran"><?= $lang['pengeluaran'] ?? 'Pengeluaran'; ?></option>
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        <?= $lang['kategori'] ?? 'Kategori'; ?>
                    </label>
                    <select
                        name="kategori"
                        id="kategori"
                        class="w-full mt-1.5 px-3 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400/20 focus:border-violet-400 transition-all"
                        required
                    >
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        <?= $lang['metode_pembayaran'] ?? 'Metode Pembayaran'; ?>
                    </label>
                    <select
                        name="metode_pembayaran"
                        id="metode_pembayaran"
                        class="w-full mt-1.5 px-3 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400/20 focus:border-violet-400 transition-all"
                        required
                    >
                        <option value="Cash"><?= $lang['tunai'] ?? 'Cash (Tunai)'; ?></option>
                        <option value="E-Wallet"><?= $lang['digital'] ?? 'E-Wallet (Digital)'; ?></option>
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        <?= $lang['jumlah'] ?? 'Jumlah'; ?> (Rp)
                    </label>
                    <input
                        type="text"
                        id="jumlah"
                        name="jumlah"
                        placeholder="0"
                        class="w-full mt-1.5 px-3 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400/20 focus:border-violet-400 transition-all"
                        required
                    >
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        <?= $lang['tanggal'] ?? 'Tanggal'; ?>
                    </label>
                    <input
                        type="date"
                        name="tanggal"
                        value="<?= date('Y-m-d') ?>"
                        class="w-full mt-1.5 px-3 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400/20 focus:border-violet-400 transition-all"
                        required
                    >
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        <?= $lang['deskripsi'] ?? 'Deskripsi'; ?>
                    </label>
                    <textarea
                        name="deskripsi"
                        rows="3"
                        placeholder="<?= $lang['keterangan_opsional'] ?? 'Keterangan tambahan (opsional)...'; ?>"
                        class="w-full mt-1.5 px-3 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400/20 focus:border-violet-400 transition-all resize-none"
                    ></textarea>
                </div>
            </div>

            <div class="flex justify-end gap-2.5 mt-6">
                <button
                    type="button"
                    id="btnBatal"
                    class="px-4 py-2 text-sm font-medium rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors"
                >
                    <?= $lang['batal'] ?? 'Batal'; ?>
                </button>
                <button
                    type="submit"
                    class="px-4 py-2 text-sm font-medium rounded-xl bg-violet-500 text-white hover:bg-violet-600 shadow-sm shadow-violet-500/10 transition-colors"
                >
                    <?= $lang['simpan'] ?? 'Simpan'; ?>
                </button>
            </div>
        </form>
    </div>
</div>

<div
    id="modalEdit"
    class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4 transition-all"
>
    <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl p-6 w-full max-w-md shadow-xl transform transition-all animate-in fade-in zoom-in-95 duration-200 relative">
        <button type="button" class="btnEditClose absolute right-6 top-6 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 focus:outline-none">
            <i class="fa-solid fa-xmark text-sm"></i>
        </button>

        <div class="text-center mb-5">
            <h2 class="text-lg font-bold text-slate-800 dark:text-white tracking-tight">
                <?= $lang['edit_transaksi'] ?? 'Edit Transaksi'; ?>
            </h2>
        </div>

        <form action="../process/transaksi/edit.php" method="POST">
            <input type="hidden" name="id" id="edit_id">

            <div class="space-y-4">
                <div>
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        <?= $lang['jenis_transaksi'] ?? 'Jenis Transaksi'; ?>
                    </label>
                    <select
                        name="jenis"
                        id="edit_jenis"
                        class="w-full mt-1.5 px-3 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400/20 focus:border-violet-400 transition-all"
                        required
                    >
                        <option value="Pemasukan"><?= $lang['pemasukan'] ?? 'Pemasukan'; ?></option>
                        <option value="Pengeluaran"><?= $lang['pengeluaran'] ?? 'Pengeluaran'; ?></option>
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        <?= $lang['kategori'] ?? 'Kategori'; ?>
                    </label>
                    <select
                        name="kategori"
                        id="edit_kategori"
                        class="w-full mt-1.5 px-3 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400/20 focus:border-violet-400 transition-all"
                        required
                    >
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        <?= $lang['metode_pembayaran'] ?? 'Metode Pembayaran'; ?>
                    </label>
                    <select
                        name="metode_pembayaran"
                        id="edit_metode_pembayaran"
                        class="w-full mt-1.5 px-3 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400/20 focus:border-violet-400 transition-all"
                        required
                    >
                        <option value="Cash"><?= $lang['tunai'] ?? 'Cash (Tunai)'; ?></option>
                        <option value="E-Wallet"><?= $lang['digital'] ?? 'E-Wallet (Digital)'; ?></option>
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        <?= $lang['jumlah'] ?? 'Jumlah'; ?> (Rp)
                    </label>
                    <input
                        type="text"
                        id="edit_jumlah"
                        name="jumlah"
                        class="w-full mt-1.5 px-3 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400/20 focus:border-violet-400 transition-all"
                        required
                    >
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        <?= $lang['tanggal'] ?? 'Tanggal'; ?>
                    </label>
                    <input
                        type="date"
                        name="tanggal"
                        id="edit_tanggal"
                        class="w-full mt-1.5 px-3 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400/20 focus:border-violet-400 transition-all"
                        required
                    >
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        <?= $lang['deskripsi'] ?? 'Deskripsi'; ?>
                    </label>
                    <textarea
                        name="deskripsi"
                        id="edit_deskripsi"
                        rows="3"
                        class="w-full mt-1.5 px-3 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400/20 focus:border-violet-400 transition-all resize-none"
                    ></textarea>
                </div>
            </div>

            <div class="flex justify-end gap-2.5 mt-6">
                <button
                    type="button"
                    id="closeEdit"
                    class="px-4 py-2 text-sm font-medium rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors"
                >
                    <?= $lang['batal'] ?? 'Batal'; ?>
                </button>
                <button
                    type="submit"
                    class="px-4 py-2 text-sm font-medium rounded-xl bg-violet-500 text-white hover:bg-violet-600 shadow-sm shadow-violet-500/10 transition-colors"
                >
                    <?= $lang['simpan'] ?? 'Simpan'; ?>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const modal = document.getElementById('modalTambah');
const modalEdit = document.getElementById('modalEdit');

const opsiKategori = {
    'Pemasukan': [
        { value: 'Gaji', text: '<?= $lang['kategori_gaji'] ?? "Gaji"; ?>' },
        { value: 'Saku', text: '<?= $lang['kategori_saku'] ?? "Saku"; ?>' }
    ],
    'Pengeluaran': [
        { value: 'Keluarga', text: '<?= $lang['kategori_keluarga'] ?? "Keluarga"; ?>' },
        { value: 'Makanan', text: '<?= $lang['kategori_makanan'] ?? "Makanan"; ?>' },
        { value: 'Barang', text: '<?= $lang['kategori_barang'] ?? "Barang"; ?>' },
        { value: 'Transportasi', text: '<?= $lang['kategori_transportasi'] ?? "Transportasi"; ?>' },
        { value: 'Kesehatan', text: '<?= $lang['kategori_kesehatan'] ?? "Kesehatan"; ?>' },
        { value: 'Kebutuhan', text: '<?= $lang['kategori_kebutuhan'] ?? "Kebutuhan"; ?>' },
        { value: 'Keinginan', text: '<?= $lang['kategori_keinginan'] ?? "Keinginan"; ?>' },
        { value: 'Belanja', text: '<?= $lang['kategori_belanja'] ?? "Belanja"; ?>' },
        { value: 'Hiburan', text: '<?= $lang['kategori_hiburan'] ?? "Hiburan"; ?>' },
        { value: 'Hadiah', text: '<?= $lang['kategori_hadiah'] ?? "Hadiah"; ?>' },
        { value: 'Bepergian', text: '<?= $lang['kategori_bepergian'] ?? "Bepergian"; ?>' }
    ]
};

function updateKategoriOptions(jenisSelectId, kategoriSelectId, selectedValue = '') {
    const jenisSelect = document.getElementById(jenisSelectId);
    const kategoriSelect = document.getElementById(kategoriSelectId);
    const jenis = jenisSelect.value;
    
    kategoriSelect.innerHTML = '';
    
    const defaultOption = document.createElement('option');
    defaultOption.value = '';
    defaultOption.textContent = '<?= $lang['pilih_kategori'] ?? "Pilih Kategori"; ?>';
    defaultOption.disabled = true;
    defaultOption.selected = true;
    kategoriSelect.appendChild(defaultOption);
    
    if (opsiKategori[jenis]) {
        opsiKategori[jenis].forEach(kat => {
            const option = document.createElement('option');
            option.value = kat.value;      
            option.textContent = kat.text; 
            
            if (selectedValue && kat.value.toLowerCase() === selectedValue.toLowerCase()) {
                option.selected = true;
                defaultOption.selected = false;
            }
            kategoriSelect.appendChild(option);
        });
    }
}

document.getElementById('jenis').addEventListener('change', () => {
    updateKategoriOptions('jenis', 'kategori');
});

document.getElementById('edit_jenis').addEventListener('change', () => {
    updateKategoriOptions('edit_jenis', 'edit_kategori');
});

document.getElementById('btnTambah').addEventListener('click', () => {
    modal.classList.remove('hidden');
    updateKategoriOptions('jenis', 'kategori');
});

document.getElementById('btnBatal').addEventListener('click', () => {
    modal.classList.add('hidden');
});

document.querySelector('.btnBatalClose').addEventListener('click', () => {
    modal.classList.add('hidden');
});

document.getElementById('closeEdit').addEventListener('click', () => {
    modalEdit.classList.add('hidden');
});

document.querySelector('.btnEditClose').addEventListener('click', () => {
    modalEdit.classList.add('hidden');
});

modal.addEventListener('click', (e) => {
    if (e.target === modal) {
        modal.classList.add('hidden');
    }
});

modalEdit.addEventListener('click', (e) => {
    if (e.target === modalEdit) {
        modalEdit.classList.add('hidden');
    }
});

function formatRupiah(input) {
    input.addEventListener('input', function () {
        let angka = this.value.replace(/\D/g, '');
        this.value = new Intl.NumberFormat('id-ID').format(angka);
    });
}

formatRupiah(document.getElementById('jumlah'));

const editJumlah = document.getElementById('edit_jumlah');
formatRupiah(editJumlah);

document.querySelector('#modalTambah form').addEventListener('submit', () => {
    const jumlahInput = document.getElementById('jumlah');
    jumlahInput.value = jumlahInput.value.replace(/\./g, '');
});

document.querySelector('#modalEdit form').addEventListener('submit', () => {
    editJumlah.value = editJumlah.value.replace(/\./g, '');
});

document.querySelectorAll('.btnEdit').forEach(btn => {
    btn.addEventListener('click', function () {
        modalEdit.classList.remove('hidden');
        document.getElementById('edit_id').value = this.dataset.id;
        document.getElementById('edit_jenis').value = this.dataset.jenis;
        
        updateKategoriOptions('edit_jenis', 'edit_kategori', this.dataset.kategori);
        
        document.getElementById('edit_metode_pembayaran').value = this.dataset.metode;
        document.getElementById('edit_jumlah').value = new Intl.NumberFormat('id-ID').format(this.dataset.jumlah);
        document.getElementById('edit_tanggal').value = this.dataset.tanggal;
        document.getElementById('edit_deskripsi').value = this.dataset.deskripsi;
    });
});

document.querySelectorAll('.btnHapus').forEach(button => {
    button.addEventListener('click', function () {
        const id = this.dataset.id;
        Swal.fire({
            title: '<?= $lang['konfirmasi_hapus_transaksi'] ?? "Hapus transaksi?"; ?>',
            text: '<?= $lang['teks_hapus_umum'] ?? "Data yang dihapus tidak bisa dikembalikan."; ?>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '<?= $lang['ya_hapus'] ?? "Ya, Hapus"; ?>',
            cancelButtonText: '<?= $lang['batal'] ?? "Batal"; ?>'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '../process/transaksi/hapus.php?id=' + id;
            }
        });
    });
});
</script>

<?php
include '../includes/footer.php';
?>