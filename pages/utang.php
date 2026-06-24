<?php
include '../config/koneksi.php';
include '../helpers/finance_helper.php';

$data = mysqli_query($conn, "
    SELECT *
    FROM utang
    ORDER BY status ASC, jatuh_tempo ASC
");

include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/navbar.php';
?>

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white tracking-tight">
            <?= $lang['catatan_utang_pinjaman'] ?? 'Catatan Utang & Pinjaman'; ?>
        </h1>
        <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
            <?= $lang['sub_utang'] ?? 'Pantau saldo pinjaman, tenggat jatuh tempo, dan status pelunasan utang Anda.'; ?>
        </p>
    </div>

    <button
        id="btnTambah"
        class="inline-flex items-center justify-center gap-2 bg-violet-500 hover:bg-violet-600 text-white px-4 py-2.5 rounded-xl font-medium text-sm shadow-sm shadow-violet-500/10 transition-colors duration-200"
    >
        <i class="fa-solid fa-plus text-xs"></i>
        <?= $lang['tambah_catatan_utang'] ?? 'Tambah Catatan Utang'; ?>
    </button>
</div>

<div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/50 rounded-2xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/20">
                    <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500"><?= $lang['nama_utang_pinjaman'] ?? 'Nama Utang / Pinjaman'; ?></th>
                    <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500"><?= $lang['jumlah'] ?? 'Jumlah'; ?></th>
                    <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500"><?= $lang['jatuh_tempo'] ?? 'Jatuh Tempo'; ?></th>
                    <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500"><?= $lang['status'] ?? 'Status'; ?></th>
                    <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 text-right"><?= $lang['aksi'] ?? 'Aksi'; ?></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                <?php if (mysqli_num_rows($data) > 0): ?>
                    <?php while ($d = mysqli_fetch_assoc($data)): ?>
                        <tr class="hover:bg-slate-50/40 dark:hover:bg-slate-800/20 transition-colors duration-150">
                            <td class="px-6 py-4 text-sm font-medium text-slate-800 dark:text-slate-200 whitespace-nowrap">
                                <?= htmlspecialchars($d['nama_utang']) ?>
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-slate-700 dark:text-slate-300 whitespace-nowrap">
                                <?= rupiah($d['jumlah']) ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400 whitespace-nowrap">
                                <?php 
                                if ($d['status'] == 'Belum Lunas') {
                                    $today = new DateTime();
                                    $dueDate = new DateTime($d['jatuh_tempo']);
                                    $interval = $today->diff($dueDate);
                                    $days = (int)$dueDate->diff($today)->format('%r%a');

                                    if ($days > 0) {
                                        echo "<span class='text-red-500 font-semibold'>" . date('d M Y', strtotime($d['jatuh_tempo'])) . " (" . ($lang['lewat'] ?? 'Lewat') . " " . abs($days) . " " . ($lang['hari_lagi'] ?? 'hari') . ")</span>";
                                    } elseif ($days == 0) {
                                        echo "<span class='text-amber-500 font-semibold'>" . date('d M Y', strtotime($d['jatuh_tempo'])) . " (" . ($lang['hari_ini'] ?? 'Jatuh tempo hari ini') . ")</span>";
                                    } else {
                                        echo "<span>" . date('d M Y', strtotime($d['jatuh_tempo'])) . " (" . abs($days) . " " . ($lang['hari_lagi'] ?? 'hari lagi') . ")</span>";
                                    }
                                } else {
                                    echo "<span class='line-through text-slate-400'>" . date('d M Y', strtotime($d['jatuh_tempo'])) . "</span>";
                                }
                                ?>
                            </td>
                            <td class="px-6 py-4 text-sm whitespace-nowrap">
                                <?php if ($d['status'] == 'Lunas'): ?>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-green-50 text-green-600 dark:bg-green-950/30 dark:text-green-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> <?= $lang['lunas'] ?? 'Lunas'; ?>
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-red-50 text-red-600 dark:bg-red-950/30 dark:text-red-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> <?= $lang['belum_lunas'] ?? 'Belum Lunas'; ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button
                                        type="button"
                                        class="btnEdit border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 w-8 h-8 rounded-lg flex items-center justify-center transition-colors"
                                        data-id="<?= $d['id'] ?>"
                                        data-nama_utang="<?= htmlspecialchars($d['nama_utang']) ?>"
                                        data-jumlah="<?= $d['jumlah'] ?>"
                                        data-jatuh_tempo="<?= $d['jatuh_tempo'] ?>"
                                        data-status="<?= $d['status'] ?>"
                                    >
                                        <i class="fa-solid fa-pen text-xs"></i>
                                    </button>
                                    <button
                                        type="button"
                                        class="btnHapus bg-red-50 dark:bg-red-950/20 text-red-500 hover:bg-red-100 dark:hover:bg-red-950/40 w-8 h-8 rounded-lg flex items-center justify-center transition-colors"
                                        data-id="<?= $d['id'] ?>"
                                    >
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <span class="w-10 h-10 rounded-xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-400 mx-auto mb-3 text-base">
                                <i class="fa-solid fa-folder-open"></i>
                            </span>
                            <p class="text-sm font-medium text-slate-400 dark:text-slate-500">
                                <?= $lang['belum_ada_utang'] ?? 'Belum ada catatan utang yang ditambahkan.'; ?>
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
                <?= $lang['tambah_catatan_utang'] ?? 'Tambah Catatan Utang'; ?>
            </h2>
        </div>

        <form action="../process/utang/tambah.php" method="POST">
            <div class="space-y-4">
                <div>
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        <?= $lang['nama_utang_pinjaman'] ?? 'Nama Utang / Pinjaman'; ?>
                    </label>
                    <input
                        type="text"
                        name="nama_utang"
                        placeholder="<?= $lang['contoh_utang'] ?? 'Contoh: Pinjaman Bank, Hutang Teman'; ?>"
                        class="w-full mt-1.5 px-3 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400/20 focus:border-violet-400 transition-all"
                        required
                    >
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
                        <?= $lang['jatuh_tempo'] ?? 'Jatuh Tempo'; ?>
                    </label>
                    <input
                        type="date"
                        name="jatuh_tempo"
                        value="<?= date('Y-m-d') ?>"
                        class="w-full mt-1.5 px-3 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400/20 focus:border-violet-400 transition-all"
                        required
                    >
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
                <?= $lang['edit_utang'] ?? 'Edit Catatan Utang'; ?>
            </h2>
        </div>

        <form action="../process/utang/edit.php" method="POST">
            <input type="hidden" name="id" id="edit_id">

            <div class="space-y-4">
                <div>
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        <?= $lang['nama_utang_pinjaman'] ?? 'Nama Utang / Pinjaman'; ?>
                    </label>
                    <input
                        type="text"
                        name="nama_utang"
                        id="edit_nama_utang"
                        class="w-full mt-1.5 px-3 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400/20 focus:border-violet-400 transition-all"
                        required
                    >
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
                        <?= $lang['jatuh_tempo'] ?? 'Jatuh Tempo'; ?>
                    </label>
                    <input
                        type="date"
                        name="jatuh_tempo"
                        id="edit_jatuh_tempo"
                        class="w-full mt-1.5 px-3 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400/20 focus:border-violet-400 transition-all"
                        required
                    >
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        <?= $lang['status'] ?? 'Status'; ?>
                    </label>
                    <select
                        name="status"
                        id="edit_status"
                        class="w-full mt-1.5 px-3 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400/20 focus:border-violet-400 transition-all"
                        required
                    >
                        <option value="Belum Lunas"><?= $lang['belum_lunas'] ?? 'Belum Lunas'; ?></option>
                        <option value="Lunas"><?= $lang['lunas'] ?? 'Lunas'; ?></option>
                    </select>
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
        document.getElementById('edit_nama_utang').value = this.dataset.nama_utang;
        document.getElementById('edit_jumlah').value = new Intl.NumberFormat('id-ID').format(this.dataset.jumlah);
        document.getElementById('edit_jatuh_tempo').value = this.dataset.jatuh_tempo;
        document.getElementById('edit_status').value = this.dataset.status;
    });
});

document.querySelectorAll('.btnHapus').forEach(button => {
    button.addEventListener('click', function () {
        const id = this.dataset.id;
        Swal.fire({
            title: '<?= $lang['konfirmasi_hapus_utang'] ?? "Hapus catatan utang?"; ?>',
            text: '<?= $lang['teks_hapus_umum'] ?? "Data yang dihapus tidak bisa dikembalikan."; ?>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '<?= $lang['ya_hapus'] ?? "Ya, Hapus"; ?>',
            cancelButtonText: '<?= $lang['batal'] ?? "Batal"; ?>'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '../process/utang/hapus.php?id=' + id;
            }
        });
    });
});
</script>

<?php
include '../includes/footer.php';
?>