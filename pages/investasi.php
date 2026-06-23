<?php
include '../config/koneksi.php';
include '../helpers/finance_helper.php';

$data = mysqli_query($conn, "
    SELECT *
    FROM investasi
    ORDER BY created_at DESC
");

include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/navbar.php';
?>

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white tracking-tight">
            Target & Instrumen Investasi
        </h1>
        <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
            Pantau dan kelola akumulasi portofolio masa depan Anda.
        </p>
    </div>

    <button
        id="btnTambah"
        class="inline-flex items-center justify-center gap-2 bg-violet-500 hover:bg-violet-600 text-white px-4 py-2.5 rounded-xl font-medium text-sm shadow-sm shadow-violet-500/10 transition-colors duration-200"
    >
        <i class="fa-solid fa-plus text-xs"></i>
        Tambah Investasi
    </button>
</div>

<div class="grid lg:grid-cols-2 gap-6">

<?php if(mysqli_num_rows($data) > 0): ?>

    <?php while($i = mysqli_fetch_assoc($data)) : ?>

        <?php
        $persen = 0;

        if ($i['target'] > 0) {
            $persen = ($i['terkumpul'] / $i['target']) * 100;

            if ($persen > 100) {
                $persen = 100;
            }
        }
        ?>

        <div class="p-6 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/50 rounded-2xl shadow-sm transition-all duration-200 hover:shadow-md flex flex-col justify-between">
            
            <div>
                <div class="flex justify-between items-start gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-yellow-50 dark:bg-yellow-950/30 flex items-center justify-center text-yellow-500 text-xs shrink-0">
                                <i class="fa-solid fa-chart-pie"></i>
                            </span>
                            <h2 class="text-base font-bold text-slate-800 dark:text-white tracking-tight truncate">
                                <?= htmlspecialchars($i['nama_target']) ?>
                            </h2>
                        </div>

                        <div class="mt-4 flex items-baseline gap-1.5 flex-wrap">
                            <span class="text-xl font-bold text-slate-800 dark:text-white tracking-tight">
                                <?= rupiah($i['terkumpul']) ?>
                            </span>
                            <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">
                                dari <?= rupiah($i['target']) ?>
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center gap-1.5 shrink-0">
                        <button
                            type="button"
                            class="btnEdit border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 w-9 h-9 rounded-xl flex items-center justify-center transition-colors duration-150"
                            data-id="<?= $i['id'] ?>"
                            data-nama_target="<?= htmlspecialchars($i['nama_target']) ?>"
                            data-target="<?= $i['target'] ?>"
                            data-terkumpul="<?= $i['terkumpul'] ?>"
                            data-tanggal="<?= $i['tanggal'] ?>"
                        >
                            <i class="fa-solid fa-pen text-xs"></i>
                        </button>

                        <button
                            type="button"
                            class="btnHapus bg-red-50 dark:bg-red-950/20 text-red-500 hover:bg-red-100 dark:hover:bg-red-950/40 w-9 h-9 rounded-xl flex items-center justify-center transition-colors duration-150"
                            data-id="<?= $i['id'] ?>"
                        >
                            <i class="fa-solid fa-trash text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="mt-5">
                <div class="flex justify-between items-center mb-1.5 text-xs font-semibold">
                    <span class="text-slate-400 dark:text-slate-500 uppercase tracking-wider">Progres Capaian</span>
                    <span class="text-violet-500"><?= number_format($persen, 0) ?>%</span>
                </div>
                <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2.5 overflow-hidden">
                    <div
                        class="bg-violet-500 h-2.5 rounded-full transition-all duration-500"
                        style="width: <?= $persen ?>%"
                    ></div>
                </div>
            </div>

        </div>

    <?php endwhile; ?>

<?php else : ?>

    <div class="p-6 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/50 rounded-2xl shadow-sm lg:col-span-2 text-center py-12">
        <span class="w-12 h-12 rounded-xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-400 mx-auto mb-3 text-lg">
            <i class="fa-solid fa-folder-open"></i>
        </span>
        <p class="text-sm font-medium text-slate-400 dark:text-slate-500">
            Belum ada target investasi yang ditambahkan.
        </p>
    </div>

<?php endif; ?>

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
                Tambah Target Investasi
            </h2>
        </div>

        <form action="../process/investasi/tambah.php" method="POST">
            <div class="space-y-4">
                <div>
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Nama Target / Instrumen
                    </label>
                    <input
                        type="text"
                        name="nama_target"
                        placeholder="Contoh: Reksa Dana Saham, Emas"
                        class="w-full mt-1.5 px-3 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400/20 focus:border-violet-400 transition-all"
                        required
                    >
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Target Investasi (Rp)
                    </label>
                    <input
                        type="text"
                        id="target"
                        name="target"
                        placeholder="0"
                        class="w-full mt-1.5 px-3 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400/20 focus:border-violet-400 transition-all"
                        required
                    >
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Jumlah Terkumpul Saat Ini (Rp)
                    </label>
                    <input
                        type="text"
                        id="terkumpul"
                        name="terkumpul"
                        value="0"
                        class="w-full mt-1.5 px-3 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400/20 focus:border-violet-400 transition-all"
                    >
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Tanggal Mulai
                    </label>
                    <input
                        type="date"
                        name="tanggal"
                        value="<?= date('Y-m-d') ?>"
                        class="w-full mt-1.5 px-3 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400/20 focus:border-violet-400 transition-all"
                    >
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
                Edit Target Investasi
            </h2>
        </div>

        <form action="../process/investasi/edit.php" method="POST">
            <input type="hidden" name="id" id="edit_id">

            <div class="space-y-4">
                <div>
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Nama Target / Instrumen
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
                        Target Investasi (Rp)
                    </label>
                    <input
                        type="text"
                        id="edit_target"
                        name="target"
                        class="w-full mt-1.5 px-3 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400/20 focus:border-violet-400 transition-all"
                        required
                    >
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Jumlah Terkumpul Saat Ini (Rp)
                    </label>
                    <input
                        type="text"
                        id="edit_terkumpul"
                        name="terkumpul"
                        class="w-full mt-1.5 px-3 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400/20 focus:border-violet-400 transition-all"
                    >
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Tanggal Mulai
                    </label>
                    <input
                        type="date"
                        name="tanggal"
                        id="edit_tanggal"
                        class="w-full mt-1.5 px-3 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400/20 focus:border-violet-400 transition-all"
                    >
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

formatRupiah(document.getElementById('target'));
formatRupiah(document.getElementById('terkumpul'));

const editTarget = document.getElementById('edit_target');
const editTerkumpul = document.getElementById('edit_terkumpul');

formatRupiah(editTarget);
formatRupiah(editTerkumpul);

document.querySelector('#modalTambah form').addEventListener('submit', () => {
    const targetInput = document.getElementById('target');
    const terkumpulInput = document.getElementById('terkumpul');
    targetInput.value = targetInput.value.replace(/\./g, '');
    terkumpulInput.value = terkumpulInput.value.replace(/\./g, '');
});

document.querySelector('#modalEdit form').addEventListener('submit', () => {
    editTarget.value = editTarget.value.replace(/\./g, '');
    editTerkumpul.value = editTerkumpul.value.replace(/\./g, '');
});

document.querySelectorAll('.btnEdit').forEach(btn => {
    btn.addEventListener('click', function () {
        modalEdit.classList.remove('hidden');
        document.getElementById('edit_id').value = this.dataset.id;
        document.getElementById('edit_nama_target').value = this.dataset.nama_target;
        document.getElementById('edit_target').value = new Intl.NumberFormat('id-ID').format(this.dataset.target);
        document.getElementById('edit_terkumpul').value = new Intl.NumberFormat('id-ID').format(this.dataset.terkumpul);
        document.getElementById('edit_tanggal').value = this.dataset.tanggal;
    });
});

document.querySelectorAll('.btnHapus').forEach(button => {
    button.addEventListener('click', function () {
        const id = this.dataset.id;
        Swal.fire({
            title: 'Hapus target investasi?',
            text: 'Data yang dihapus tidak bisa dikembalikan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '../process/investasi/hapus.php?id=' + id;
            }
        });
    });
});
</script>

<?php
include '../includes/footer.php';
?>