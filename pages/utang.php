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

<?php if (mysqli_num_rows($data) > 0): ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php while ($d = mysqli_fetch_assoc($data)): 
            $status = $d['status'] ?? 'Belum Lunas';
            $isLunas = (strtolower($status) === 'lunas' || $status == 1 || strtolower($status) === 'paid');
            
            $jumlah_dana = $d['jumlah'] ?? ($d['jumlah_utang'] ?? 0);
            $jatuh_tempo = $d['jatuh_tempo'] ?? ($d['tanggal'] ?? date('Y-m-d'));
            $nama_utang = $d['nama_utang'] ?? ($d['deskripsi'] ?? ($d['nama'] ?? '-'));
        ?>
            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/50 rounded-2xl p-5 shadow-sm relative group flex flex-col justify-between min-h-[180px]">
                
                <div>
                    <div class="flex items-start justify-between gap-4 mb-2">
                        <h3 class="font-bold text-slate-800 dark:text-white text-base tracking-tight leading-snug line-clamp-2">
                            <?= htmlspecialchars($nama_utang) ?>
                        </h3>
                        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button 
                                type="button"
                                class="btnEdit text-slate-400 hover:text-violet-500 p-1 rounded-md transition-colors"
                                data-id="<?= $d['id'] ?>"
                                data-nama_utang="<?= htmlspecialchars($nama_utang) ?>"
                                data-jumlah="<?= $jumlah_dana ?>"
                                data-jatuh_tempo="<?= $jatuh_tempo ?>"
                                data-status="<?= $status ?>"
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

                    <div class="text-xl font-black text-slate-800 dark:text-white mb-4">
                        <?= rupiah($jumlah_dana) ?>
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs pt-2 border-t border-slate-50 dark:border-slate-800/60">
                    <span class="inline-flex items-center gap-1 text-slate-400 dark:text-slate-500 font-medium">
                        <i class="fa-regular fa-calendar text-[10px]"></i>
                        <?= date('d M Y', strtotime($jatuh_tempo)) ?>
                    </span>
                    
                    <?php if ($isLunas): ?>
                        <span class="font-bold text-emerald-500 bg-emerald-50 dark:bg-emerald-950/30 px-2.5 py-1 rounded-full text-[11px]">
                            <?= $lang['lunas'] ?? 'Lunas'; ?>
                        </span>
                    <?php else: ?>
                        <span class="font-bold text-rose-500 bg-rose-50 dark:bg-rose-950/30 px-2.5 py-1 rounded-full text-[11px]">
                            <?= $lang['belum_lunas'] ?? 'Belum Lunas'; ?>
                        </span>
                    <?php endif; ?>
                </div>

            </div>
        <?php endwhile; ?>
    </div>
<?php else: ?>
    <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/50 rounded-2xl shadow-sm p-16 text-center">
        <span class="w-12 h-12 rounded-2xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-400 mx-auto mb-4 text-xl">
            <i class="fa-solid fa-hand-holding-dollar"></i>
        </span>
        <h3 class="text-base font-bold text-slate-700 dark:text-slate-300 mb-1">
            <?= $lang['belum_ada_utang'] ?? 'Belum ada catatan utang yang ditambahkan.'; ?>
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
                        placeholder="<?= $lang['contoh_utang'] ?? 'Contoh: Pinjaman Bank, Utang Teman'; ?>"
                        class="w-full mt-1.5 px-3 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-400/20 focus:border-violet-400 transition-all"
                        required
                    >
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        <?= $lang['jumlah_utang_rp'] ?? 'Jumlah Utang (Rp)'; ?>
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

            <input type="hidden" name="jumlah_utang" id="alt_jumlah">
            <input type="hidden" name="tanggal" id="alt_tanggal">
            <input type="hidden" name="deskripsi" id="alt_deskripsi">

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
                        <?= $lang['jumlah_utang_rp'] ?? 'Jumlah Utang (Rp)'; ?>
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

            <input type="hidden" name="jumlah_utang" id="alt_edit_jumlah">
            <input type="hidden" name="tanggal" id="alt_edit_tanggal">
            <input type="hidden" name="deskripsi" id="alt_edit_deskripsi">

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

formatRupiah(document.getElementById('jumlah'));
formatRupiah(document.getElementById('edit_jumlah'));

document.querySelector('#modalTambah form').addEventListener('submit', function() {
    let rawJumlah = document.getElementById('jumlah').value.replace(/\./g, '');
    let rawNama = this.querySelector('input[name="nama_utang"]').value;
    let rawTanggal = this.querySelector('input[name="jatuh_tempo"]').value;

    document.getElementById('jumlah').value = rawJumlah;

    document.getElementById('alt_jumlah').value = rawJumlah;
    document.getElementById('alt_tanggal').value = rawTanggal;
    document.getElementById('alt_deskripsi').value = rawNama;
});

document.querySelector('#modalEdit form').addEventListener('submit', function() {
    let rawJumlah = document.getElementById('edit_jumlah').value.replace(/\./g, '');
    let rawNama = document.getElementById('edit_nama_utang').value;
    let rawTanggal = document.getElementById('edit_jatuh_tempo').value;

    document.getElementById('edit_jumlah').value = rawJumlah;

    document.getElementById('alt_edit_jumlah').value = rawJumlah;
    document.getElementById('alt_edit_tanggal').value = rawTanggal;
    document.getElementById('alt_edit_deskripsi').value = rawNama;
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