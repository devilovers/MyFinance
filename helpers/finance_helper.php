<?php

function rupiah($angka)
{
    return 'Rp ' . number_format($angka ?? 0, 0, ',', '.');
}

function totalPemasukan($conn)
{
    $query = mysqli_query($conn, "
        SELECT IFNULL(SUM(jumlah), 0) AS total
        FROM transaksi
        WHERE jenis = 'Pemasukan'
    ");

    $data = mysqli_fetch_assoc($query);

    return $data['total'];
}

function totalPengeluaran($conn)
{
    $query = mysqli_query($conn, "
        SELECT IFNULL(SUM(jumlah), 0) AS total
        FROM transaksi
        WHERE jenis = 'Pengeluaran'
    ");

    $data = mysqli_fetch_assoc($query);

    return $data['total'];
}

function totalTabungan($conn)
{
    $query = mysqli_query($conn, "
        SELECT IFNULL(SUM(terkumpul), 0) AS total
        FROM tabungan
    ");

    $data = mysqli_fetch_assoc($query);

    return $data['total'];
}

function totalInvestasi($conn)
{
    $query = mysqli_query($conn, "
        SELECT IFNULL(SUM(terkumpul), 0) AS total
        FROM investasi
    ");

    $data = mysqli_fetch_assoc($query);

    return $data['total'];
}

function totalUtang($conn)
{
    $query = mysqli_query($conn, "
        SELECT IFNULL(SUM(jumlah), 0) AS total
        FROM utang
        WHERE status = 'Belum Lunas'
    ");

    $data = mysqli_fetch_assoc($query);

    return $data['total'];
}

function totalSaldo($conn)
{
    $pemasukan = totalPemasukan($conn);
    $pengeluaran = totalPengeluaran($conn);

    return $pemasukan - $pengeluaran;
}

function utangJatuhTempo($conn)
{
    $query = mysqli_query($conn, "
        SELECT *
        FROM utang
        WHERE status = 'Belum Lunas'
        AND jatuh_tempo <= DATE_ADD(CURDATE(), INTERVAL 3 DAY)
        ORDER BY jatuh_tempo ASC
    ");

    return $query;
}
?>