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
            Catatan Utang & Pinjaman
        </h1>
        <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
            Pantau saldo pinjaman, tenggat jatuh tempo, dan status pelunasan utang Anda.
        </p>
    </div>

    <button
        id="btnTambah"
        class="inline-flex items-center justify-center gap-2 bg-violet-500 hover:bg-violet-600 text-white px-4 py-2.5 rounded-xl font-medium text-sm shadow-sm shadow-violet-500/10 transition-colors duration-200"
    >
        <i class="fa-solid fa-plus text-xs"></i>
        Tambah Catatan Utang
    </button>
</div>

<div class="grid lg:grid-cols-2 gap-6">

<?php if(mysqli_num_rows($data) > 0): ?>
    <?php while($u = mysqli_fetch_assoc($data)) : ?>
        <div class="p-6 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/50 rounded-2xl shadow-sm transition-all duration-200 hover:shadow-md flex flex-col justify-between relative overflow-hidden">
            
            <div class="absolute left-0 top-0 bottom-0 w-1.5 <?= $u['status'] == 'Belum Lunas' ? 'bg-amber-500' : 'bg-green-500' ?>"></div>

            <div>
                <div class="flex justify-between items-start gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg <?= $u['status'] == 'Belum Lunas' ? 'bg-amber-50 dark:bg-amber-950/30 text-amber-500' : 'bg-green-50 dark:bg-green-950/30 text-green-500' ?> flex items-center justify-center text-xs shrink-0">
                                <i class="fa-solid fa-hand-holding-dollar"></i>
                            </span>
                            <h2 class="text-base font-bold text-slate-800 dark:text-white tracking-tight truncate">
                                <?= htmlspecialchars($u['nama_utang']) ?>
                            </h2>
                        </div>

                        <div class="mt-4 flex items-baseline gap-1.5 flex-wrap">
                            <span class="text-xl font-bold text-slate-800 dark:text-white tracking-tight">
                                <?= rupiah($u['jumlah']) ?>
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium <?= $u['status'] == 'Belum Lunas' ? 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400' : 'bg-green-50 text-green-700 dark:bg-green-950/40 dark:text-green-400' ?>">
                                <?= $u['status'] ?>
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center gap-1.5 shrink-0">
                        <button
                            type="button"
                            class="btnEdit border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 w-9 h-9 rounded-xl flex items-center justify-center transition-colors duration-150"
                            data-id="<?= $u['id'] ?>"
                            data-nama_utang="<?= htmlspecialchars($u['nama_utang']) ?>"
                            data-jumlah="<?= $u['jumlah'] ?>"
                            data-jatuh_tempo="<?= $u['jatuh_tempo'] ?>"
                            data-status="<?= $u['status'] ?>"
                        >
                            <i class="fa-solid fa-pen text-xs"></i>
                        </button>

                        <button
                            type="button"
                            class="btnHapus bg-red-50 dark:bg-red-950/20 text-red-500 hover:bg-red-100 dark:hover:bg-red-950/40 w-9 h-9 rounded-xl flex items-center justify-center transition-colors duration-150"
                            data-id="<?= $u['id'] ?>"
                        >
                            <i class="fa-solid fa-trash text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="mt-5 pt-4 border-t border-slate-100 dark:border-slate-800/60 flex items-center justify-between text-xs">
                <div class="flex items-center gap-1.5 text-slate-400 dark:text-slate-500">
                    <i class="fa-regular fa-calendar-check text-sm"></i>
                    <span>Jatuh Tempo:</span>
                    <span class="font-semibold text-slate-600 dark:text-slate-300">
                        <?= date('d M Y', strtotime($u['jatuh_tempo'])) ?>
                    </span>
                </div>

                <?php if ($u['status'] == 'Belum Lunas'): ?>
                    <?php
                    $today = new DateTime();
                    $targetDate = new DateTime($u['jatuh_tempo']);
                    $diff = $today->diff($targetDate);
                    $selisih = (int)$diff->format('%r%a');
                    ?>

                    <?php if ($selisih < 0): ?>
                        <span class="text-red-500 font-semibold flex items-center gap-1">
                            <i class="fa-solid fa-circle-exclamation"></i> Terlambat <?= abs($selisih) ?> hari
                        </span>
                    <?php elseif ($selisih == 0): ?>
                        <span class="text-amber-500 font-semibold flex items-center gap-1">
                            <i class="fa-solid fa-clock"></i> Hari ini!
                        </span>
                    <?php else: ?>
                        <span class="text-slate-400 dark:text-slate-500 font-medium">
                            <?= $selisih ?> hari lagi
                        </span>
                    <?php endif; ?>
                <?php else: ?>
                    <span class="text-green-500 font-semibold flex items-center gap-1">
                        <i class="fa-solid fa-circle-check"></i> Selesai
                    </span>
                <?php endif; ?>
            </div>

        </div>
    <?php endwhile; ?>
<?php else : ?>
    <div class="p-6 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/50 rounded-2xl shadow-sm lg:col-span-2 text-center py-12">
        <span class="w-12 h-12 rounded-xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-400 mx-auto mb-3 text-lg">
            <i class="fa-solid fa-folder-open"></i>
        </span>
        <p class="text-sm font-medium text-slate-400 dark:text-slate-500">
            Belum ada catatan utang yang ditambahkan.
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
                Tambah Catatan Utang
            </h2>
        </div>

        <form action="../process/utang/tambah.php" method="POST">
            <div class="space-y-4">
                <div>
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Nama Transaksi / Pemberi Pinjaman
                    </label>
                    <input
                        type="text"
                        name="nama_utang"
                        placeholder="Contoh: Pinjaman Bank, Hutang Teman"
                        class="w-full mt-1.5 px-3 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400/20 focus:border-violet-400 transition-all"
                        required
                    >
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Jumlah Utang (Rp)
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
                        Tanggal Jatuh Tempo
                    </label>
                    <input
                        type="date"
                        name="jatuh_tempo"
                        value="<?= date('Y-m-d') ?>"
                        class="w-full mt-1.5 px-3 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400/20 focus:border-violet-400 transition-all"
                        required
                    >
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Status Pelunasan
                    </label>
                    <select
                        name="status"
                        class="w-full mt-1.5 px-3 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400/20 focus:border-violet-400 transition-all"
                        required
                    >
                        <option value="Belum Lunas">Belum Lunas</option>
                        <option value="Lunas">Lunas</option>
                    </select>
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
                Edit Catatan Utang
            </h2>
        </div>

        <form action="../process/utang/edit.php" method="POST">
            <input type="hidden" name="id" id="edit_id">

            <div class="space-y-4">
                <div>
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Nama Transaksi / Pemberi Pinjaman
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
                        Jumlah Utang (Rp)
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
                        Tanggal Jatuh Tempo
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
                        Status Pelunasan
                    </label>
                    <select
                        name="status"
                        id="edit_status"
                        class="w-full mt-1.5 px-3 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400/20 focus:border-violet-400 transition-all"
                        required
                    >
                        <option value="Belum Lunas">Belum Lunas</option>
                        <option value="Lunas">Lunas</option>
                    </select>
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
            title: 'Hapus catatan utang?',
            text: 'Data yang dihapus tidak bisa dikembalikan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
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