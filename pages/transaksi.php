<?php
include '../config/koneksi.php';
include '../helpers/finance_helper.php';

$search = $_GET['search'] ?? '';

if ($search != '') {

    $search = mysqli_real_escape_string(
        $conn,
        $search
    );

    $transaksi = mysqli_query(
        $conn,
        "
        SELECT *
        FROM transaksi
        WHERE
            jenis LIKE '%$search%'
            OR kategori LIKE '%$search%'
            OR deskripsi LIKE '%$search%'
        ORDER BY tanggal DESC, id DESC
        "
    );

} else {

    $transaksi = mysqli_query(
        $conn,
        "
        SELECT *
        FROM transaksi
        ORDER BY tanggal DESC, id DESC
        "
    );

}

include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/navbar.php';
?>

<h1 class="text-3xl font-bold mb-6 dark:text-white">
    Transaksi
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
        Tambah Transaksi
    </button>
</div>

<div class="card mb-6">

    <form method="GET">

        <div class="flex gap-4">

            <input
    type="text"
    name="search"
    value="<?= $_GET['search'] ?? '' ?>"
    placeholder="Cari transaksi..."
    class="
        flex-1
        p-3
        rounded-xl
        border
        dark:bg-slate-700
        dark:border-slate-600
        dark:text-white
    "
>

            <button
                class="
                    bg-violet-500
                    text-white
                    px-5
                    rounded-xl
                "
            >
                Cari
            </button>

        </div>

    </form>

</div>

<div class="card">
    <h2 class="text-xl font-semibold mb-4 dark:text-white">
        Riwayat Transaksi
    </h2>

    <?php if (mysqli_num_rows($transaksi) > 0): ?>

<div class="overflow-x-auto">

    <table class="w-full">

        <thead>
            <tr
                class="
                    border-b
                    dark:border-slate-700
                "
            >
                <th class="text-left p-4">
                    Tanggal
                </th>

                <th class="text-left p-4">
                    Jenis
                </th>

                <th class="text-left p-4">
                    Kategori
                </th>

                <th class="text-left p-4">
                    Deskripsi
                </th>

                <th class="text-right p-4">
                    Jumlah
                </th>

                <th class="text-center p-4">
                    Aksi
                </th>
            </tr>
        </thead>

        <tbody>

        <?php while ($t = mysqli_fetch_assoc($transaksi)): ?>

            <tr
                class="
                    border-b
                    dark:border-slate-700
                    hover:bg-violet-50
                    dark:hover:bg-slate-700
                    transition
                "
            >
                <td class="p-4">
                    <?= date(
                        'd M Y',
                        strtotime($t['tanggal'])
                    ) ?>
                </td>

                <td class="p-4">

                    <?php if ($t['jenis'] == 'Pemasukan'): ?>

                        <span
                            class="
                                bg-green-100
                                text-green-600
                                px-3
                                py-1
                                rounded-full
                                text-sm
                                font-semibold
                            "
                        >
                            Pemasukan
                        </span>

                    <?php else: ?>

                        <span
                            class="
                                bg-red-100
                                text-red-600
                                px-3
                                py-1
                                rounded-full
                                text-sm
                                font-semibold
                            "
                        >
                            Pengeluaran
                        </span>

                    <?php endif; ?>

                </td>

                <td class="p-4">
                    <?= htmlspecialchars(
                        $t['kategori']
                    ) ?>
                </td>

                <td class="p-4">
                    <?= htmlspecialchars(
                        $t['deskripsi']
                    ) ?>
                </td>

                <td
                    class="
                        p-4
                        text-right
                        font-bold
                    "
                >
                    <?= rupiah(
                        $t['jumlah']
                    ) ?>
                </td>
                <td class="p-4 text-center">

   <button
    type="button"
    class="
        btnHapus
        bg-red-500
        hover:bg-red-600
        text-white
        px-3
        py-2
        rounded-xl
        transition
    "
    data-id="<?= $t['id'] ?>"
>
    <i class="fa-solid fa-trash"></i>
</button>

</td>
            </tr>

        <?php endwhile; ?>

        </tbody>

    </table>

</div>

<?php else: ?>

<p class="text-gray-500 dark:text-gray-400">
    Belum ada transaksi.
</p>

<?php endif; ?>
</div>

<div
    id="modalTransaksi"
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
            max-w-2xl
            max-h-[90vh]
            overflow-y-auto
        "
    >

        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold dark:text-white">
                Tambah Transaksi
            </h2>

            <button
                id="btnClose"
                class="
                    text-gray-500
                    hover:text-red-500
                    text-2xl
                "
            >
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form
    action="../process/transaksi/tambah.php"
    method="POST"
>

            <div class="grid md:grid-cols-2 gap-5">

                <div>
                    <label
                        class="
                            font-medium
                            dark:text-white
                        "
                    >
                        Jenis Transaksi
                    </label>

                    <select
                        id="jenis"
                        name="jenis"
                        class="
                            w-full
                            mt-2
                            p-3
                            rounded-xl
                            border
                            dark:bg-slate-700
                            dark:border-slate-600
                            dark:text-white
                        "
                        required
                    >
                        <option value="">
                            Pilih Jenis
                        </option>

                        <option value="Pemasukan">
                            Pemasukan
                        </option>

                        <option value="Pengeluaran">
                            Pengeluaran
                        </option>
                    </select>
                </div>

                <div>
                    <label
                        class="
                            font-medium
                            dark:text-white
                        "
                    >
                        Kategori
                    </label>

                    <select
                        id="kategori"
                        name="kategori"
                        class="
                            w-full
                            mt-2
                            p-3
                            rounded-xl
                            border
                            dark:bg-slate-700
                            dark:border-slate-600
                            dark:text-white
                        "
                        required
                    >
                        <option value="">
                            Pilih jenis transaksi
                        </option>
                    </select>
                </div>

                <div>
                    <label
                        class="
                            font-medium
                            dark:text-white
                        "
                    >
                        Jumlah
                    </label>

                    <input
    type="text"
    id="jumlah"
    name="jumlah"
    class="
        w-full
        mt-2
        p-3
        rounded-xl
        border
        dark:bg-slate-700
        dark:border-slate-600
        dark:text-white
    "
    placeholder="Contoh: 10000"
    autocomplete="off"
    required
>
                </div>

                <div>
                    <label
                        class="
                            font-medium
                            dark:text-white
                        "
                    >
                        Tanggal
                    </label>

                    <input
                        type="date"
                        name="tanggal"
                        class="
                            w-full
                            mt-2
                            p-3
                            rounded-xl
                            border
                            dark:bg-slate-700
                            dark:border-slate-600
                            dark:text-white
                        "
                        value="<?= date('Y-m-d') ?>"
                        required
                    >
                </div>

            </div>

            <div class="mt-5">
                <label
                    class="
                        font-medium
                        dark:text-white
                    "
                >
                    Deskripsi
                </label>

                <textarea
                    name="deskripsi"
                    rows="4"
                    placeholder="Contoh: Membeli makanan di warung."
                    class="
                        w-full
                        mt-2
                        p-3
                        rounded-xl
                        border
                        dark:bg-slate-700
                        dark:border-slate-600
                        dark:text-white
                    "
                ></textarea>
            </div>

            <div class="mt-8 flex justify-end gap-3">

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
                    type="submit"
                    class="
                        px-5
                        py-3
                        rounded-xl
                        bg-violet-500
                        hover:bg-violet-600
                        text-white
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
const btnTambah = document.getElementById('btnTambah');
const modal = document.getElementById('modalTransaksi');
const btnBatal = document.getElementById('btnBatal');
const btnClose = document.getElementById('btnClose');

btnTambah.addEventListener('click', () => {
    modal.classList.remove('hidden');
});

btnBatal.addEventListener('click', () => {
    modal.classList.add('hidden');
});

btnClose.addEventListener('click', () => {
    modal.classList.add('hidden');
});

modal.addEventListener('click', (e) => {
    if (e.target === modal) {
        modal.classList.add('hidden');
    }
});

const jenis = document.getElementById('jenis');
const kategori = document.getElementById('kategori');

const pemasukan = [
    'Gaji',
    'Uang Saku'
];

const pengeluaran = [
    'Keluarga',
    'Makanan',
    'Barang',
    'Transportasi',
    'Kesehatan',
    'Utang',
    'Kebutuhan',
    'Keinginan',
    'Belanja',
    'Hiburan',
    'Hadiah',
    'Bepergian'
];

jenis.addEventListener('change', function () {

    kategori.innerHTML = '';

    let data = [];

    if (this.value === 'Pemasukan') {
        data = pemasukan;
    } else if (this.value === 'Pengeluaran') {
        data = pengeluaran;
    }

    const first = document.createElement('option');
    first.value = '';
    first.textContent = 'Pilih Kategori';

    kategori.appendChild(first);

    data.forEach(item => {
        const option = document.createElement('option');
        option.value = item;
        option.textContent = item;

        kategori.appendChild(option);
    });
});

document.querySelectorAll('.btnHapus')
.forEach(button => {

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

                window.location.href =
                    '../process/transaksi/hapus.php?id=' + id;

            }

        });

    });

});
</script>

<?php
include '../includes/footer.php';
?>