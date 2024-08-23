<?php

$database = 'latihan_crud';

// Buat koneksi ke MySQL tanpa memilih database terlebih dahulu
$conn = new mysqli('localhost', 'root', '');

if ($conn->connect_error) {
    die("Koneksi Gagal: " . $conn->connect_error);
}

// Cek apakah database sudah ada, jika belum maka buat
$createDatabase = "CREATE DATABASE IF NOT EXISTS $database";
$conn->query($createDatabase);

// Pilih database
$conn->select_db($database);

// Buat tabel mahasiswa
$createMahasiswaTable = "CREATE TABLE IF NOT EXISTS mahasiswa (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    npm VARCHAR(20) UNIQUE,
    nama TEXT,
    prodi VARCHAR(50)
)";

$conn->query($createMahasiswaTable);
