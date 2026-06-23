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

<h1 class="text-3xl font-bold mb-6 dark:text-white">
    Catatan Utang / Pinjaman
</h1>

<div class="flex justify-end mb-6">
    <button
        id="btnTambah"
        class="
            bg-violet-500
            hover:bg-violet-600
            text-white
            px-5
            py-3
            rounded-2xl
            font-semibold
            shadow-lg
            transition
        "
    >
        <i class="fa-solid fa-plus mr-2"></i>
        Tambah Catatan Utang
    </button>
</div>

<div class="grid lg:grid-cols-2 gap-6">

<?php if(mysqli_num_rows($data) > 0): ?>
    <?php while($u = mysqli_fetch_assoc($data)) : ?>
        <div class="card relative overflow-hidden">
            <div class="absolute left-0 top-0 bottom-0 w-2 <?= $u['status'] == 'Belum Lunas' ? 'bg-red-500' : 'bg-green-500' ?>"></div>

            <div class="flex justify-between items-start pl-2">
                <div class="flex-1">
                    <div class="flex items-center gap-3 flex-wrap">
                        <h2 class="text-xl font-bold dark:text-white">
                            <?= htmlspecialchars($u['nama_utang']) ?>
                        </h2>
                        <span class="text-xs px-2 py-1 rounded-lg font-semibold <?= $u['status'] == 'Belum Lunas' ? 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400' : 'bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400' ?>">
                            <?= $u['status'] ?>
                        </span>
                    </div>

                    <p class="text-2xl font-bold text-gray-800 dark:text-slate-200 mt-3">
                        <?= rupiah($u['jumlah']) ?>
                    </p>

                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                        <i class="fa-solid fa-calendar-days mr-1"></i>
                        Jatuh Tempo: <span class="font-medium <?= $u['status'] == 'Belum Lunas' && strtotime($u['jatuh_tempo']) <= time() ? 'text-red-500 font-bold' : '' ?>"><?= date('d M Y', strtotime($u['jatuh_tempo'])) ?></span>
                    </p>
                </div>

                <div class="flex gap-2 ml-4">
                    <button
                        type="button"
                        class="
                            btnEdit
                            bg-amber-400
                            hover:bg-amber-500
                            text-white
                            w-9
                            h-9
                            rounded-xl
                            flex
                            items-center
                            justify-center
                            transition
                        "
                        data-id="<?= $u['id'] ?>"
                        data-nama_utang="<?= htmlspecialchars($u['nama_utang']) ?>"
                        data-jumlah="<?= $u['jumlah'] ?>"
                        data-jatuh_tempo="<?= $u['jatuh_tempo'] ?>"
                        data-status="<?= $u['status'] ?>"
                    >
                        <i class="fa-solid fa-pen text-sm"></i>
                    </button>

                    <button
                        type="button"
                        class="
                            btnHapus
                            bg-red-500
                            hover:bg-red-600
                            text-white
                            w-9
                            h-9
                            rounded-xl
                            flex
                            items-center
                            justify-center
                            transition
                        "
                        data-id="<?= $u['id'] ?>"
                    >
                        <i class="fa-solid fa-trash text-sm"></i>
                    </button>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
<?php else : ?>
    <div class="card lg:col-span-2">
        <p class="text-gray-500 dark:text-gray-400">
            Tidak ada catatan riwayat utang piutang.
        </p>
    </div>
<?php endif; ?>

</div>

<div
    id="modalTambah"
    class="
        hidden
        fixed
        inset-0
        bg-black/50
        z-50
        flex
        items-center
        justify-center
        p-5
    "
>
    <div class="bg-white dark:bg-slate-800 rounded-3xl p-8 w-full max-w-xl">
        <h2 class="text-2xl font-bold mb-6 dark:text-white">
            Tambah Catatan Utang
        </h2>

        <form action="../process/utang/tambah.php" method="POST">
            <div class="space-y-5">
                <div>
                    <label class="font-medium dark:text-white">
                        Deskripsi Pinjaman (Pinjam ke siapa & Untuk apa)
                    </label>
                    <input
                        type="text"
                        name="nama_utang"
                        placeholder="Contoh: Pinjam ke Budi untuk bayar kontrakan"
                        class="w-full mt-2 p-3 rounded-xl border dark:bg-slate-700 dark:text-white"
                        required
                    >
                </div>

                <div>
                    <label class="font-medium dark:text-white">
                        Jumlah Pinjaman (Rp)
                    </label>
                    <input
                        type="text"
                        id="jumlah"
                        name="jumlah"
                        class="w-full mt-2 p-3 rounded-xl border dark:bg-slate-700 dark:text-white"
                        required
                    >
                </div>

                <div>
                    <label class="font-medium dark:text-white">
                        Tanggal Jatuh Tempo
                    </label>
                    <input
                        type="date"
                        name="jatuh_tempo"
                        value="<?= date('Y-m-d') ?>"
                        class="w-full mt-2 p-3 rounded-xl border dark:bg-slate-700 dark:text-white"
                        required
                    >
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-8">
                <button type="button" id="btnBatal" class="px-5 py-3 rounded-xl bg-gray-300 hover:bg-gray-400 transition">
                    Batal
                </button>
                <button class="px-5 py-3 rounded-xl bg-violet-500 text-white hover:bg-violet-600 transition">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<div
    id="modalEdit"
    class="
        hidden
        fixed
        inset-0
        bg-black/50
        z-50
        flex
        items-center
        justify-center
        p-5
    "
>
    <div class="bg-white dark:bg-slate-800 rounded-3xl p-8 w-full max-w-xl">
        <h2 class="text-2xl font-bold mb-6 dark:text-white">
            Edit Catatan Utang
        </h2>

        <form action="../process/utang/edit.php" method="POST">
            <input type="hidden" name="id" id="edit_id">

            <div class="space-y-5">
                <div>
                    <label class="font-medium dark:text-white">
                        Deskripsi Pinjaman (Pinjam ke siapa & Untuk apa)
                    </label>
                    <input
                        type="text"
                        name="nama_utang"
                        id="edit_nama_utang"
                        class="w-full mt-2 p-3 rounded-xl border dark:bg-slate-700 dark:text-white"
                        required
                    >
                </div>

                <div>
                    <label class="font-medium dark:text-white">
                        Jumlah Pinjaman (Rp)
                    </label>
                    <input
                        type="text"
                        id="edit_jumlah"
                        name="jumlah"
                        class="w-full mt-2 p-3 rounded-xl border dark:bg-slate-700 dark:text-white"
                        required
                    >
                </div>

                <div>
                    <label class="font-medium dark:text-white">
                        Tanggal Jatuh Tempo
                    </label>
                    <input
                        type="date"
                        name="jatuh_tempo"
                        id="edit_jatuh_tempo"
                        class="w-full mt-2 p-3 rounded-xl border dark:bg-slate-700 dark:text-white"
                        required
                    >
                </div>

                <div>
                    <label class="font-medium dark:text-white">
                        Status Pembayaran
                    </label>
                    <select
                        name="status"
                        id="edit_status"
                        class="w-full mt-2 p-3 rounded-xl border dark:bg-slate-700 dark:text-white"
                    >
                        <option value="Belum Lunas">Belum Lunas</option>
                        <option value="Lunas">Lunas</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-8">
                <button type="button" id="closeEdit" class="px-5 py-3 rounded-xl bg-gray-300 hover:bg-gray-400 transition">
                    Batal
                </button>
                <button class="px-5 py-3 rounded-xl bg-violet-500 text-white hover:bg-violet-600 transition">
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

document.getElementById('closeEdit').addEventListener('click', () => {
    modalEdit.classList.add('hidden');
});

modal.addEventListener('click', (e) => {
    if (e.target === modal) modal.classList.add('hidden');
});

modalEdit.addEventListener('click', (e) => {
    if (e.target === modalEdit) modalEdit.classList.add('hidden');
});

function formatRupiah(input) {
    input.addEventListener('input', function () {
        let angka = this.value.replace(/\D/g, '');
        this.value = new Intl.NumberFormat('id-ID').format(angka);
    });
}

const inputJumlah = document.getElementById('jumlah');
const editJumlah = document.getElementById('edit_jumlah');

formatRupiah(inputJumlah);
formatRupiah(editJumlah);

document.querySelector('#modalTambah form').addEventListener('submit', () => {
    inputJumlah.value = inputJumlah.value.replace(/\./g, '');
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