<?php
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
            Riwayat Transaksi Keuangan
        </h1>
        <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
            Catat dan tinjau seluruh arus kas masuk maupun keluar secara berkala.
        </p>
    </div>

    <button
        id="btnTambah"
        class="inline-flex items-center justify-center gap-2 bg-violet-500 hover:bg-violet-600 text-white px-4 py-2.5 rounded-xl font-medium text-sm shadow-sm shadow-violet-500/10 transition-colors duration-200"
    >
        <i class="fa-solid fa-plus text-xs"></i>
        Tambah Transaksi
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
                placeholder="Cari kategori, jenis, atau deskripsi transaksi..."
                class="w-full pl-11 pr-4 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400/20 focus:border-violet-400 transition-all"
            >
        </div>
        <div class="flex gap-2">
            <button
                type="submit"
                class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl font-medium text-sm transition-colors duration-150"
            >
                Cari
            </button>
            <?php if ($search != ''): ?>
                <a
                    href="transaksi.php"
                    class="px-4 py-2.5 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-xl flex items-center justify-center transition-colors text-sm font-medium"
                >
                    Reset
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
                    <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Tanggal</th>
                    <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Jenis</th>
                    <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Kategori</th>
                    <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Jumlah</th>
                    <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Deskripsi</th>
                    <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                <?php if (mysqli_num_rows($transaksi) > 0): ?>
                    <?php while ($t = mysqli_fetch_assoc($transaksi)): ?>
                        <tr class="hover:bg-slate-50/40 dark:hover:bg-slate-800/20 transition-colors duration-150">
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400 whitespace-nowrap">
                                <?= date('d F Y', strtotime($t['tanggal'])) ?>
                            </td>
                            <td class="px-6 py-4 text-sm whitespace-nowrap">
                                <?php if (strtolower($t['jenis']) == 'pemasukan'): ?>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-green-50 text-green-600 dark:bg-green-950/30 dark:text-green-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Pemasukan
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-red-50 text-red-600 dark:bg-red-950/30 dark:text-red-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Pengeluaran
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-slate-800 dark:text-slate-200 whitespace-nowrap">
                                <?= htmlspecialchars($t['kategori']) ?>
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
                        <td colspan="6" class="px-6 py-12 text-center">
                            <span class="w-10 h-10 rounded-xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-400 mx-auto mb-3 text-base">
                                <i class="fa-solid fa-folder-open"></i>
                            </span>
                            <p class="text-sm font-medium text-slate-400 dark:text-slate-500">
                                Tidak ada data transaksi ditemukan.
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
                Tambah Transaksi Baru
            </h2>
        </div>

        <form action="../process/transaksi/tambah.php" method="POST">
            <div class="space-y-4">
                <div>
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Jenis Transaksi
                    </label>
                    <select
                        name="jenis"
                        class="w-full mt-1.5 px-3 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400/20 focus:border-violet-400 transition-all"
                        required
                    >
                        <option value="Pemasukan">Pemasukan</option>
                        <option value="Pengeluaran">Pengeluaran</option>
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Kategori
                    </label>
                    <input
                        type="text"
                        name="kategori"
                        placeholder="Contoh: Gaji, Makanan, Transportasi"
                        class="w-full mt-1.5 px-3 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400/20 focus:border-violet-400 transition-all"
                        required
                    >
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Jumlah (Rp)
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
                        Tanggal
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
                        Deskripsi
                    </label>
                    <textarea
                        name="deskripsi"
                        rows="3"
                        placeholder="Keterangan tambahan (opsional)..."
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
                    Batal
                </button>
                <button
                    type="submit"
                    class="px-4 py-2 text-sm font-medium rounded-xl bg-violet-500 text-white hover:bg-violet-600 shadow-sm shadow-violet-500/10 transition-colors"
                >
                    Simpan
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
                Edit Transaksi
            </h2>
        </div>

        <form action="../process/transaksi/edit.php" method="POST">
            <input type="hidden" name="id" id="edit_id">

            <div class="space-y-4">
                <div>
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Jenis Transaksi
                    </label>
                    <select
                        name="jenis"
                        id="edit_jenis"
                        class="w-full mt-1.5 px-3 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400/20 focus:border-violet-400 transition-all"
                        required
                    >
                        <option value="Pemasukan">Pemasukan</option>
                        <option value="Pengeluaran">Pengeluaran</option>
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Kategori
                    </label>
                    <input
                        type="text"
                        name="kategori"
                        id="edit_kategori"
                        class="w-full mt-1.5 px-3 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400/20 focus:border-violet-400 transition-all"
                        required
                    >
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Jumlah (Rp)
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
                        Tanggal
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
                        Deskripsi
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
                    Batal
                </button>
                <button
                    type="submit"
                    class="px-4 py-2 text-sm font-medium rounded-xl bg-violet-500 text-white hover:bg-violet-600 shadow-sm shadow-violet-500/10 transition-colors"
                >
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const modal = document.getElementById('modalTambah');
const modalEdit = document.getElementById('modalEdit');

document.getElementById('btnTambah').addEventListener('click', () => {
    modal.classList.remove('hidden');
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
        document.getElementById('edit_kategori').value = this.dataset.kategori;
        document.getElementById('edit_jumlah').value = new Intl.NumberFormat('id-ID').format(this.dataset.jumlah);
        document.getElementById('edit_tanggal').value = this.dataset.tanggal;
        document.getElementById('edit_deskripsi').value = this.dataset.deskripsi;
    });
});

document.querySelectorAll('.btnHapus').forEach(button => {
    button.addEventListener('click', function () {
        const id = this.dataset.id;
        Swal.fire({
            title: 'Hapus transaksi?',
            text: 'Data yang dihapus tidak bisa dikembalikan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
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