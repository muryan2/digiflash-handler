<?php

// Pastikan skrip hanya menerima permintaan POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die("Metode tidak diizinkan.");
}

// Tangkap data POST yang dikirim oleh bukaOlshop
// Ganti dengan nama-nama field yang sesuai dengan dokumentasi API bukaOlshop
$userId      = $_POST['id_user'] ?? null;
$username    = $_POST['username'] ?? null;
$email       = $_POST['email'] ?? null;
$amount      = $_POST['jumlah_topup'] ?? null;
$invoiceId   = $_POST['invoice_id'] ?? null;

// Validasi data yang masuk
if (!$userId || !$amount || !$invoiceId) {
    http_response_code(400);
    die("Data yang dibutuhkan tidak lengkap.");
}

// ----------------------------------------------------
// TAHAP 1: Logika Pemrosesan Pembayaran Anda di Sini
// ----------------------------------------------------
// Di bagian ini, Anda harus menambahkan kode untuk:
// 1. Memeriksa status pembayaran dari sistem Anda (misalnya: database, API payment gateway).
// 2. Memastikan bahwa transaksi dengan invoiceId ($invoiceId) sudah berhasil dibayar.
// 3. Pastikan juga jumlah pembayaran ($amount) sudah sesuai.

// Contoh placeholder untuk logika Anda
$isPaymentSuccess = false; // Ganti dengan logika deteksi pembayaran Anda

if ($isPaymentSuccess) {
    // ----------------------------------------------------
    // TAHAP 2: Panggilan ke API bukaOlshop untuk Mengisi Saldo
    // ----------------------------------------------------
    // Setelah pembayaran terkonfirmasi, panggil API bukaOlshop untuk mengisi saldo.
    // Anda harus mengganti URL dan data dengan detail API yang sebenarnya dari bukaOlshop.
    // Pastikan Anda menggunakan API Key yang aman.

    $apiUrl = 'https://api.bukaolshop.com/v1/topup'; // Ganti dengan URL API yang benar
    $apiKey = 'YOUR_API_KEY_HERE'; // Ganti dengan API Key Anda yang sebenarnya

    $data = [
        'user_id' => $userId,
        'amount'  => $amount,
        'notes'   => "Top up via override feature for invoice $invoiceId"
    ];

    $options = [
        'http' => [
            'method'  => 'POST',
            'header'  => "Content-Type: application/json\r\n" .
                         "X-API-KEY: " . $apiKey . "\r\n",
            'content' => json_encode($data)
        ]
    ];

    $context  = stream_context_create($options);
    $result   = file_get_contents($apiUrl, false, $context);
    
    if ($result === FALSE) {
        // Gagal memanggil API bukaOlshop
        http_response_code(500);
        die("Gagal memproses top up. Silakan coba lagi nanti.");
    }
    
    // Decode respons dari API
    $apiResponse = json_decode($result, true);

    if (isset($apiResponse['success']) && $apiResponse['success']) {
        http_response_code(200);
        echo json_encode([
            'status' => 'success',
            'message' => 'Saldo berhasil diisi.'
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => $apiResponse['message'] ?? 'Gagal memproses top up saldo.'
        ]);
    }

} else {
    // Pembayaran belum terdeteksi. Berikan respons yang sesuai.
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Pembayaran belum terdeteksi atau tidak valid.'
    ]);
}
?>