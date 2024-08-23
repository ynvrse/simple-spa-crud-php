<?php
include "koneksi.php";

$id = $npm = $nama = $prodi = $sukses = $eror = $search_input = '';
$rSearch = 'Cari Data';
$rHeader = 'Tambah';
$showBtn = $op = false;
$bgColor = "bg-success";

// Hitung total data
$query = $conn->query("select * from mahasiswa");
$totalData = $query->num_rows;

// Fungsi pencarian
if (isset($_POST['search'])) {
    $search_input = $_POST['search_input'];
    $query = $conn->query("SELECT * FROM mahasiswa WHERE nama LIKE '%$search_input%' OR npm LIKE '%$search_input%' OR prodi LIKE '%$search_input%'");
    $totalData = $query->num_rows;
    $rSearch = empty($search_input) ? 'Cari Data' : 'Refresh';
}

// Operasi berdasarkan parameter 'op'
if (isset($_GET['op'])) {
    $op = $_GET['op'];
    $showBtn = true;
    $bgColor = ($op === 'update') ? 'bg-info' : $bgColor;

    if ($op === 'update') {
        $rHeader = 'Edit';
        $id = $_GET['id'];
        $query = $conn->query("SELECT * FROM mahasiswa WHERE id='$id'");
        $data = $query->fetch_array();
        $npm = $data['npm'];
        $nama = $data['nama'];
        $prodi = $data['prodi'];
        $query->free();
    } elseif ($op === "delete") {
        $id = $_GET['id'];
        $deleted = $conn->query("DELETE FROM mahasiswa WHERE id='$id'");
        $sukses = $deleted ? "Berhasil Menghapus Data!" : "";
        $eror = !$deleted ? "Gagal Menghapus Data!" : '';

        $bgColor = $deleted ? 'bg-success' : 'bg-danger';
    }
}

// Simpan atau update data
if (isset($_POST['simpan'])) {
    $npm = $_POST['npm'];
    $nama = $_POST['nama'];
    $prodi = $_POST['prodi'];

    if ($npm && $nama && $prodi) {
        try {
            $query = ($op === 'update') ?
                "UPDATE mahasiswa SET nama='$nama', npm='$npm', prodi='$prodi' WHERE id='$id'" :
                "INSERT INTO mahasiswa (npm,nama,prodi) VALUES ('$npm','$nama','$prodi')";

            $result = $conn->query($query);
            $sukses = ($op === 'update') ? "Data Berhasil Diubah!" : "Berhasil Menambahkan Data Baru!";
            $bgColor = $result ? 'bg-success' : 'bg-danger';
            $eror = !$result ? "Operasi Gagal!" : '';
        } catch (Exception $e) {
            $eror = "Operasi Gagal : " . $e->getMessage();
        }
    } else {
        $eror = "Harap Masukan Semua Data!";
        $bgColor = 'bg-danger';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMPLE SPA CRUD with PHP & MySQL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
        a {
            text-decoration: none;
        }
    </style>
</head>

<body class="<?= $bgColor ?>">

    <div class="mx-auto w-75 p-2">
        <?php if ($sukses) : ?>
            <div class="alert alert-success"><?= $sukses ?></div>
            <?php header('refresh:2;url=index.php'); ?>
        <?php elseif ($eror) : ?>
            <div class="alert alert-danger"><?= $eror ?></div>
        <?php endif; ?>

        <div class="card my-4">
            <div class="card-header ">
                <h4><?= $rHeader ?> Data</h4>
            </div>
            <div class="card-body">
                <form action="" method="post">
                    <div class="row p-2">
                        <label for="npm">NPM</label>
                        <input type="text" name="npm" value="<?= $npm ?>" class="form-control">
                    </div>
                    <div class="row p-2">
                        <label for="nama">Nama</label>
                        <input type="text" name="nama" value="<?= $nama ?>" class="form-control">
                    </div>
                    <div class="row p-2">
                        <label for="prodi">Prodi</label>
                        <select name="prodi" class="form-select">
                            <option value="">= Pilih Prodi =</option>
                            <option value="TI" <?= $prodi == 'TI' ? 'selected' : '' ?>>TI</option>
                            <option value="SI" <?= $prodi == 'SI' ? 'selected' : '' ?>>SI</option>
                        </select>
                    </div>
                    <div class="col mt-2">
                        <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
                        <?php if ($showBtn) : ?>
                            <a href="index.php"><button type="button" class="btn btn-secondary">Batal</button></a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header ">
                <div class="row d-flex">
                    <div class="col">
                        <h4>Data Mahasiswa FTI</h4>
                        <?php
                        $startTime = microtime(true);

                        try {
                            $query = "SELECT * FROM mahasiswa";
                            $result = $conn->query($query);


                            if ($result->num_rows > 0) {
                                $data = $result->fetch_all(MYSQLI_ASSOC);
                            } else {
                                $data = [];
                            }

                            $endTime = microtime(true);

                            $executionTime = $endTime - $startTime;
                            echo "Query dieksekusi dalam waktu: " . $executionTime . " detik";
                        } catch (Exception $e) {
                            echo "Error: " . $e->getMessage();
                        }
                        ?>

                    </div>
                    <div class="col-auto">

                        <div class="p-2 bg-info rounded-3 text-white fw-bold">

                            Total: <?= $totalData ?> Data
                        </div>

                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row w-25 ms-auto mb-2">
                    <form action="" method="post">
                        <div class="input-group d-flex">
                            <input type="text" name="search_input" placeholder="<?= $search_input   ?>" class="form-control border-dark">
                            <button type="submit" name="search" class="btn btn-secondary"><?= $rSearch ?></button>
                        </div>
                    </form>
                </div>
                <div class="table-responsive" style="max-height: 150px; overflow-y: auto;">
                    <table class="table table-bordered table-striped border-dark">
                        <thead class="text-center">
                            <tr>
                                <th scope="col">No.</th>
                                <th scope="col">NPM</th>
                                <th scope="col">Nama</th>
                                <th scope="col">Prodi</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            <?php
                            $queryRead = "SELECT * FROM mahasiswa ";
                            if (isset($_POST['search']) && !empty($_POST['search_input'])) {
                                $queryRead .= "WHERE nama LIKE '%$search_input%' OR npm LIKE '%$search_input%' OR prodi LIKE '%$search_input%' ";
                            }
                            $queryRead .= "ORDER BY id DESC";

                            $result = $conn->query($queryRead);
                            $no = 1;

                            if (mysqli_num_rows($result) > 0) :
                                while ($data = $result->fetch_array()) :
                            ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= $data['npm'] ?></td>
                                        <td><?= $data['nama'] ?></td>
                                        <td><?= $data['prodi'] ?></td>
                                        <td width="20%">
                                            <a href="index.php?op=update&id=<?= $data['id'] ?>">
                                                <button type="button" class="btn btn-warning">Edit</button>
                                            </a>
                                            <a onclick="return confirm('Yakin ingin hapus data?')" href="index.php?op=delete&id=<?= $data['id'] ?>">
                                                <button type="button" class="btn btn-danger">Hapus</button>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="5">Tidak ada data</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
<?php $conn->close(); ?>

</html>