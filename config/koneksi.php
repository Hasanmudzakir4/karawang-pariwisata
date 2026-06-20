<?php

/**
 * KONEKSI DATABASE - SISTEM INFORMASI PARIWISATA KARAWANG
 * Menggunakan PDO (PHP Data Objects) untuk Keamanan & Pencegahan SQL Injection
 */

$host = 'localhost';
$dbname = 'pariwisata_karawang';
$username = 'root';
$password = 'root';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    // Mengatur PDO error mode ke Exception untuk kemudahan debugging
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Koneksi ke database gagal: " . $e->getMessage());
}
