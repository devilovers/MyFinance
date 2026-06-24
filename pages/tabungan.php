<?php
include '../config/koneksi.php';
include '../helpers/finance_helper.php';

$data = mysqli_query($conn, "
    SELECT *
    FROM tabungan
    ORDER BY is_pinned DESC, created_at DESC
");

include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/navbar.php';
?>

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white tracking-tight">
            <?= $lang['target_tabungan_simpanan'] ?? 'Target Tabungan & Simpanan'; ?>
        </h1>
        <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
            <?= $lang['sub_target_tabungan'] ?? 'Kelola dan pantau progres tabungan impian Anda secara berkala.'; ?>
        </p>
    </div>

    <button
        id="btnTambah"
        class="inline-flex items-center justify-center gap-2 bg-violet-500 hover:bg-violet-600 text-white px-4 py-2.5 rounded-xl font-medium text-sm shadow-sm shadow-violet-500/10 transition-colors duration-200"
    >
        <i class="fa-solid fa-plus text-xs"></i>
        <?= $lang['tambah_target'] ?? 'Tambah Target'; ?>
    </button>
</div>

<?php if (mysqli_num_rows($data) > 0): ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php while ($d = mysqli_fetch_assoc($data)): 
            // Mengatasi perbedaan penamaan nama kolom database secara aman
            $target_dana = $d['target_dana'] ?? ($d['target'] ?? 0);
            $dana_terkumpul = $d['dana_terkumpul'] ?? ($d['terkumpul'] ?? 0);
            $tanggal_target = $d['tanggal_target'] ?? ($d['tanggal'] ?? date('Y-m-d'));

            $persen = $target_dana > 0 ? min(100, round(($dana_terkumpul / $target_dana) * 100)) : 0;
        ?>
            <div class="bg-white dark:bg-slate-900 border <?= ($d['is_pinned'] ?? 0) ? 'border-violet-200 dark:border-violet-900/50 ring-1 ring-violet-100 dark:ring-violet-950/20' : 'border-slate-100 dark:border-slate-800/50' ?> rounded-2xl p-5 shadow-sm relative group flex flex-col justify-between min-h-[220px]">
                
                <div>
                    <div class="flex items-start justify-between gap-4 mb-3">
                        <h3 class="font-bold text-slate-800 dark:text-white text-base tracking-tight leading-snug line-clamp-2">
                            <?= htmlspecialchars($d['nama_target'] ?? ($d['nama'] ?? '-')) ?>
                        </h3>
                        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button 
                                type="button"
                                class="btnPin text-slate-400 hover:text-amber-500 p-1 rounded-md transition-colors"
                                data-id="<?= $d['id'] ?>"
                            >
                                <i class="<?= ($d['is_pinned'] ?? 0) ? 'fa-solid fa-thumbtack text-amber-500' : 'fa-solid fa-thumbtack' ?> text-xs"></i>
                            </button>
                            <button 
                                type="button"
                                class="btnEdit text-slate-400 hover:text-violet-500 p-1 rounded-md transition-colors"
                                data-id="<?= $d['id'] ?>"
                                data-nama_target="<?= htmlspecialchars($d['nama_target'] ?? ($d['nama'] ?? '')) ?>"
                                data-target_dana="<?= $target_dana ?>"
                                data-dana_terkumpul="<?= $dana_terkumpul ?>"
                                data-tanggal_target="<?= $tanggal_target ?>"
                            >
                                <i class="fa-solid fa-pen text-xs"></i>
                            </button>
                            <button 
                                type="button"
                                class="btnHapus text-slate-400 hover:text-red-500 p-1 rounded-md transition-colors"
                                data-id="<?= $d['id'] ?>"
                            >
                                <i class="fa-solid fa-trash text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-col gap-0.5 mb-5">
                        <div class="text-xs text-slate-400 dark:text-slate-500">
                            <?= $lang['progres_capaian'] ?? 'Progres Capaian'; ?>
                        </div>
                        <div class="flex items-baseline gap-1">
                            <span class="text-lg font-black text-slate-800 dark:text-white"><?= rupiah($dana_terkumpul) ?></span>
                            <span class="text-xs text-slate-400 dark:text-slate-500"><?= $lang['dari'] ?? 'dari'; ?> <?= rupiah($target_dana) ?></span>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden mb-3.5">
                        <div 
                            class="bg-gradient-to-r from-violet-500 to-fuchsia-500 h-full rounded-full transition-all duration-500"
                            style="width: <?= $persen ?>%"
                        ></div>
                    </div>

                    <div class="flex items-center justify-between text-xs">
                        <span class="inline-flex items-center gap-1 text-slate-400 dark:text-slate-500 font-medium">
                            <i class="fa-regular fa-calendar text-[10px]"></i>
                            <?= date('d M Y', strtotime($tanggal_target)) ?>
                        </span>
                        <span class="font-bold text-violet-500 bg-violet-50 dark:bg-violet-950/30 px-2 py-0.5 rounded-md">
                            <?= $persen ?>%
                        </span>
                    </div>
                </div>

            </div>
        <?php endwhile; ?>
    </div>
<?php else: ?>
    <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/50 rounded-2xl shadow-sm p-16 text-center">
        <span class="w-12 h-12 rounded-2xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-400 mx-auto mb-4 text-xl">
            <i class="fa-solid fa-piggy-bank"></i>
        </span>
        <h3 class="text-base font-bold text-slate-700 dark:text-slate-300 mb-1">
            <?= $lang['belum_ada_tabungan'] ?? 'Belum ada target tabungan yang ditambahkan.'; ?>
        </h3>
    </div>
<?php endif; ?>

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
                <?= $lang['tambah_target'] ?? 'Tambah Target'; ?>
            </h2>
        </div>

        <form action="../process/tabungan/tambah.php" method="POST">
            <div class="space-y-4">
                <div>
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        <?= $lang['nama_target_tabungan'] ?? 'Nama Target Tabungan'; ?>
                    </label>
                    <input
                        type="text"
                        name="nama_target"
                        placeholder="<?= $lang['contoh_tabungan'] ?? 'Contoh: Beli Laptop, Dana Darurat'; ?>"
                        class="w-full mt-1.5 px-3 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400/20 focus:border-violet-400 transition-all"
                        required
                    >
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        <?= $lang['target_tabungan_rp'] ?? 'Target Tabungan (Rp)'; ?>
                    </label>
                    <input
                        type="text"
                        id="target_dana"
                        name="target_dana"
                        placeholder="0"
                        class="w-full mt-1.5 px-3 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400/20 focus:border-violet-400 transition-all"
                        required
                    >
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        <?= $lang['dana_terkumpul_rp'] ?? 'Dana Terkumpul (Rp)'; ?>
                    </label>
                    <input
                        type="text"
                        id="dana_terkumpul"
                        name="dana_terkumpul"
                        placeholder="0"
                        class="w-full mt-1.5 px-3 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400/20 focus:border-violet-400 transition-all"
                        required
                    >
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        <?= $lang['tanggal_target'] ?? 'Tanggal Target'; ?>
                    </label>
                    <input
                        type="date"
                        name="tanggal_target"
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
                <?= $lang['edit_target_tabungan'] ?? 'Edit Target Tabungan'; ?>
            </h2>
        </div>

        <form action="../process/tabungan/edit.php" method="POST">
            <input type="hidden" name="id" id="edit_id">

            <div class="space-y-4">
                <div>
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        <?= $lang['nama_target_tabungan'] ?? 'Nama Target Tabungan'; ?>
                    </label>
                    <input
                        type="text"
                        name="nama_target"
                        id="edit_nama_target"
                        class="w-full mt-1.5 px-3 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400/20 focus:border-violet-400 transition-all"
                        required
                    >
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        <?= $lang['target_tabungan_rp'] ?? 'Target Tabungan (Rp)'; ?>
                    </label>
                    <input
                        type="text"
                        id="edit_target_dana"
                        name="target_dana"
                        class="w-full mt-1.5 px-3 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400/20 focus:border-violet-400 transition-all"
                        required
                    >
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        <?= $lang['dana_terkumpul_rp'] ?? 'Dana Terkumpul (Rp)'; ?>
                    </label>
                    <input
                        type="text"
                        id="edit_dana_terkumpul"
                        name="dana_terkumpul"
                        class="w-full mt-1.5 px-3 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400/20 focus:border-violet-400 transition-all"
                        required
                    >
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        <?= $lang['tanggal_target'] ?? 'Tanggal Target'; ?>
                    </label>
                    <input
                        type="date"
                        name="tanggal_target"
                        id="edit_tanggal_target"
                        class="w-full mt-1.5 px-3 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400/20 focus:border-violet-400 transition-all"
                        required
                    >
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
    if(!input) return;
    input.addEventListener('input', function () {
        let angka = this.value.replace(/\D/g, '');
        this.value = new Intl.NumberFormat('id-ID').format(angka);
    });
}

formatRupiah(document.getElementById('target_dana'));
formatRupiah(document.getElementById('dana_terkumpul'));
formatRupiah(document.getElementById('edit_target_dana'));
formatRupiah(document.getElementById('edit_dana_terkumpul'));

document.querySelector('#modalTambah form').addEventListener('submit', () => {
    document.getElementById('target_dana').value = document.getElementById('target_dana').value.replace(/\./g, '');
    document.getElementById('dana_terkumpul').value = document.getElementById('dana_terkumpul').value.replace(/\./g, '');
});

document.querySelector('#modalEdit form').addEventListener('submit', () => {
    document.getElementById('edit_target_dana').value = document.getElementById('edit_target_dana').value.replace(/\./g, '');
    document.getElementById('edit_dana_terkumpul').value = document.getElementById('edit_dana_terkumpul').value.replace(/\./g, '');
});

document.querySelectorAll('.btnEdit').forEach(btn => {
    btn.addEventListener('click', function () {
        modalEdit.classList.remove('hidden');
        document.getElementById('edit_id').value = this.dataset.id;
        document.getElementById('edit_nama_target').value = this.dataset.nama_target;
        document.getElementById('edit_target_dana').value = new Intl.NumberFormat('id-ID').format(this.dataset.target_dana);
        document.getElementById('edit_dana_terkumpul').value = new Intl.NumberFormat('id-ID').format(this.dataset.dana_terkumpul);
        document.getElementById('edit_tanggal_target').value = this.dataset.tanggal_target;
    });
});

document.querySelectorAll('.btnHapus').forEach(button => {
    button.addEventListener('click', function () {
        const id = this.dataset.id;
        Swal.fire({
            title: '<?= $lang['konfirmasi_hapus_tabungan'] ?? "Hapus target tabungan?"; ?>',
            text: '<?= $lang['teks_hapus_umum'] ?? "Data yang dihapus tidak bisa dikembalikan."; ?>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '<?= $lang['ya_hapus'] ?? "Ya, Hapus"; ?>',
            cancelButtonText: '<?= $lang['batal'] ?? "Batal"; ?>'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '../process/tabungan/hapus.php?id=' + id;
            }
        });
    });
});

document.querySelectorAll('.btnPin').forEach(button => {
    button.addEventListener('click', function () {
        const id = this.dataset.id;
        fetch('../process/tabungan/pin.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id=' + id
        })
        .then(() => {
            window.location.reload();
        });
    });
});
</script>

<?php
include '../includes/footer.php';
?>