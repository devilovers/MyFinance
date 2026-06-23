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

<h1 class="text-3xl font-bold mb-6 dark:text-white">
    Investasi
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

        <div class="card">

            <div class="flex justify-between items-start">

                <div class="flex-1">

                    <div class="flex items-center gap-2">
                        <h2 class="text-2xl font-bold dark:text-white">
                        <?= htmlspecialchars($i['nama_target']) ?>
                        </h2>
                    </div>

                    <p class="text-gray-500 mt-2">
                        <?= rupiah($i['terkumpul']) ?>
                        /
                        <?= rupiah($i['target']) ?>
                    </p>

                    <p class="text-sm text-gray-400 mt-1">
                        <?= number_format($persen, 0) ?>%
                        tercapai
                    </p>

                </div>

                <div class="flex gap-2">

                    <button
                        type="button"
                        class="
                            btnEdit
                            bg-amber-400
                            hover:bg-amber-500
                            text-white
                            w-10
                            h-10
                            rounded-xl
                            flex
                            items-center
                            justify-center
                            transition
                        "
                        data-id="<?= $i['id'] ?>"
                        data-nama_target="<?= htmlspecialchars($i['nama_target']) ?>"
                        data-target="<?= $i['target'] ?>"
                        data-terkumpul="<?= $i['terkumpul'] ?>"
                        data-tanggal="<?= $i['tanggal'] ?>"
                    >
                        <i class="fa-solid fa-pen"></i>
                    </button>

                    <button
                        type="button"
                        class="
                            btnHapus
                            bg-red-500
                            hover:bg-red-600
                            text-white
                            w-10
                            h-10
                            rounded-xl
                            flex
                            items-center
                            justify-center
                            transition
                        "
                        data-id="<?= $i['id'] ?>"
                    >
                        <i class="fa-solid fa-trash"></i>
                    </button>

                </div>

            </div>

            <div
                class="
                    w-full
                    bg-gray-200
                    dark:bg-slate-700
                    rounded-full
                    h-4
                    mt-5
                    overflow-hidden
                "
            >

                <div
                    class="
                        bg-violet-500
                        h-4
                        rounded-full
                    "
                    style="width: <?= $persen ?>%"
                ></div>

            </div>

        </div>

    <?php endwhile; ?>

<?php else : ?>

    <div class="card lg:col-span-2">

        <p class="text-gray-500 dark:text-gray-400">
            Belum ada target investasi.
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

<div
    class="
        bg-white
        dark:bg-slate-800
        rounded-3xl
        p-8
        w-full
        max-w-xl
    "
>

<h2 class="text-2xl font-bold mb-6 dark:text-white">
    Tambah Target Investasi
</h2>

<form
    action="../process/investasi/tambah.php"
    method="POST"
>

<div class="space-y-5">

<div>

<label class="font-medium dark:text-white">
Nama Target / Instrumen
</label>

<input
    type="text"
    name="nama_target"
    class="
        w-full
        mt-2
        p-3
        rounded-xl
        border
        dark:bg-slate-700
        dark:text-white
    "
    required
>

</div>

<div>

<label class="font-medium dark:text-white">
Target Investasi
</label>

<input
    type="text"
    id="target"
    name="target"
    class="
        w-full
        mt-2
        p-3
        rounded-xl
        border
        dark:bg-slate-700
        dark:text-white
    "
    required
>

</div>

<div>

<label class="font-medium dark:text-white">
Jumlah Terkumpul (Dana Saat Ini)
</label>

<input
    type="text"
    id="terkumpul"
    name="terkumpul"
    value="0"
    class="
        w-full
        mt-2
        p-3
        rounded-xl
        border
        dark:bg-slate-700
        dark:text-white
    "
>

</div>

<div>

<label class="font-medium dark:text-white">
Tanggal Mulai
</label>

<input
    type="date"
    name="tanggal"
    value="<?= date('Y-m-d') ?>"
    class="
        w-full
        mt-2
        p-3
        rounded-xl
        border
        dark:bg-slate-700
        dark:text-white
    "
>

</div>

</div>

<div class="flex justify-end gap-3 mt-8">

<button
    type="button"
    id="btnBatal"
    class="
        px-5
        py-3
        rounded-xl
        bg-gray-300
        hover:bg-gray-400
        transition
    "
>
    Batal
</button>

<button
    class="
        px-5
        py-3
        rounded-xl
        bg-violet-500
        text-white
        hover:bg-violet-600
        transition
    "
>
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

<div
    class="
        bg-white
        dark:bg-slate-800
        rounded-3xl
        p-8
        w-full
        max-w-xl
    "
>

<h2 class="text-2xl font-bold mb-6 dark:text-white">
    Edit Target Investasi
</h2>

<form
    action="../process/investasi/edit.php"
    method="POST"
>

<input
    type="hidden"
    name="id"
    id="edit_id"
>

<div class="space-y-5">

<div>

<label class="font-medium dark:text-white">
Nama Target / Instrumen
</label>

<input
    type="text"
    name="nama_target"
    id="edit_nama_target"
    class="
        w-full
        mt-2
        p-3
        rounded-xl
        border
        dark:bg-slate-700
        dark:text-white
    "
    required
>

</div>

<div>

<label class="font-medium dark:text-white">
Target Investasi
</label>

<input
    type="text"
    id="edit_target"
    name="target"
    class="
        w-full
        mt-2
        p-3
        rounded-xl
        border
        dark:bg-slate-700
        dark:text-white
    "
    required
>

</div>

<div>

<label class="font-medium dark:text-white">
Jumlah Terkumpul (Dana Saat Ini)
</label>

<input
    type="text"
    id="edit_terkumpul"
    name="terkumpul"
    class="
        w-full
        mt-2
        p-3
        rounded-xl
        border
        dark:bg-slate-700
        dark:text-white
    "
>

</div>

<div>

<label class="font-medium dark:text-white">
Tanggal Mulai
</label>

<input
    type="date"
    name="tanggal"
    id="edit_tanggal"
    class="
        w-full
        mt-2
        p-3
        rounded-xl
        border
        dark:bg-slate-700
        dark:text-white
    "
>

</div>

</div>

<div class="flex justify-end gap-3 mt-8">

<button
    type="button"
    id="closeEdit"
    class="
        px-5
        py-3
        rounded-xl
        bg-gray-300
        hover:bg-gray-400
        transition
    "
>
    Batal
</button>

<button
    class="
        px-5
        py-3
        rounded-xl
        bg-violet-500
        text-white
        hover:bg-violet-600
        transition
    "
>
    Simpan
</button>

</div>

</form>

</div>
</div>

<script>
const modal =
    document.getElementById('modalTambah');

const modalEdit =
    document.getElementById('modalEdit');

document
.getElementById('btnTambah')
.addEventListener('click', () => {
    modal.classList.remove('hidden');
});

document
.getElementById('btnBatal')
.addEventListener('click', () => {
    modal.classList.add('hidden');
});

document
.getElementById('closeEdit')
.addEventListener('click', () => {
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

        let angka =
            this.value.replace(/\D/g, '');

        this.value =
            new Intl.NumberFormat('id-ID')
                .format(angka);
    });
}

formatRupiah(
    document.getElementById('target')
);

formatRupiah(
    document.getElementById('terkumpul')
);

const editTarget = document.getElementById('edit_target');
const editTerkumpul = document.getElementById('edit_terkumpul');

formatRupiah(editTarget);
formatRupiah(editTerkumpul);

document
.querySelector('#modalTambah form')
.addEventListener('submit', () => {

    target.value =
        target.value.replace(/\./g, '');

    terkumpul.value =
        terkumpul.value.replace(/\./g, '');
});

document
.querySelector('#modalEdit form')
.addEventListener('submit', () => {

    editTarget.value =
        editTarget.value.replace(/\./g, '');

    editTerkumpul.value =
        editTerkumpul.value.replace(/\./g, '');
});

document
.querySelectorAll('.btnEdit')
.forEach(btn => {

    btn.addEventListener('click', function () {

        modalEdit.classList.remove('hidden');

        document.getElementById('edit_id').value =
            this.dataset.id;

        document.getElementById('edit_nama_target').value =
            this.dataset.nama_target;

        document.getElementById('edit_target').value =
            new Intl.NumberFormat('id-ID')
            .format(this.dataset.target);

        document.getElementById('edit_terkumpul').value =
            new Intl.NumberFormat('id-ID')
            .format(this.dataset.terkumpul);

        document.getElementById('edit_tanggal').value =
            this.dataset.tanggal;
    });

});

document.querySelectorAll('.btnHapus')
.forEach(button => {

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

                window.location.href =
                    '../process/investasi/hapus.php?id=' + id;

            }

        });

    });

});
</script>

<?php
include '../includes/footer.php';
?>