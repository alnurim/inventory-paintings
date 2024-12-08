#!/usr/bin/env php
<?php

// Fungsi untuk menampilkan header hanya pada bagian tertentu
function showHeader($title, $withDecoration = true)
{
    if ($withDecoration) {
        echo "\n\033[36m";
        echo str_repeat('=', 50) . "\n";
        echo str_pad($title, 50, ' ', STR_PAD_BOTH) . "\n";
        echo str_repeat('=', 50) . "\033[0m\n";
    } else {
        echo "\n\033[33m$title\033[0m\n";
    }
}

// Fungsi untuk menampilkan pesan pembatalan dan keluar
function abortProcess()
{
    echo "\n\033[31mProses dibatalkan oleh pengguna.\033[0m\n";
    exit(0);
}

// Path ke folder models
$modelsPath = __DIR__ . '/app/Models';

// Periksa apakah folder models ada
if (!is_dir($modelsPath)) {
    echo "\033[31mFolder 'app/Models' tidak ditemukan.\033[0m\n";
    exit(1);
}

// Ambil semua file PHP dari folder models
$modelFiles = glob($modelsPath . '/*.php');

// Pastikan ada file model
if (empty($modelFiles)) {
    echo "\033[33mTidak ada file model di folder 'app/Models'.\033[0m\n";
    exit(0);
}

// Ambil nama model dari nama file
$models = array_map(function ($filePath) {
    return pathinfo($filePath, PATHINFO_FILENAME);
}, $modelFiles);

// Daftar model yang selalu dikecualikan
$alwaysExcludedModels = ['User', 'Role'];
$modelsToProcess = array_diff($models, $alwaysExcludedModels);

if (empty($modelsToProcess)) {
    echo "\033[33mTidak ada model yang tersedia untuk dibuatkan resource.\033[0m\n";
    exit(0);
}

// Tanya apakah ada model yang ingin dikecualikan
echo "\n\033[33mApakah ada model yang ingin dikecualikan? (Y/N)\033[0m ";
$response = strtolower(trim(fgets(STDIN)));

// Periksa apakah pengguna membatalkan
if ($response === null || feof(STDIN)) {
    abortProcess();
}

$excludedModels = [];
if ($response === 'y') {
    // Menampilkan model yang ditemukan jika pengguna memilih 'Y'
    showHeader('Model yang Ditemukan', true);
    foreach ($modelsToProcess as $index => $model) {
        echo sprintf("\033[36m[%d] %s\033[0m\n", $index + 1, $model);
    }

    // Meminta pengguna untuk memilih model yang ingin dikecualikan
    do {
        echo "\n\033[33mMasukkan nomor model yang ingin dikecualikan  : \033[0m";
        $excludedInput = trim(fgets(STDIN));

        // Periksa apakah pengguna membatalkan
        if ($excludedInput === null || feof(STDIN)) {
            abortProcess();
        }

        $excludedIndexes = array_map('trim', explode(',', $excludedInput));
        foreach ($excludedIndexes as $index) {
            $index = (int)$index - 1; // Konversi ke indeks array
            if (isset($modelsToProcess[$index])) {
                $excludedModels[] = $modelsToProcess[$index];
                unset($modelsToProcess[$index]);
            } else {
                echo "\033[31mNomor '$index' tidak valid.\033[0m\n";
            }
        }

        $modelsToProcess = array_values($modelsToProcess); // Reset indeks array
        echo "\033[33mApakah ada lagi yang ingin dikecualikan? (Y/N): \033[0m";
        $response = strtolower(trim(fgets(STDIN)));

        // Periksa apakah pengguna membatalkan
        if ($response === null || feof(STDIN)) {
            abortProcess();
        }
    } while ($response === 'y');
}

if (!empty($excludedModels)) {
    echo "\n\033[36mModel yang akan dibuat resource: \033[0m" . implode(', ', $modelsToProcess) . "\n";
}


// Konfirmasi sebelum eksekusi
echo "\n\033[33mLanjutkan proses pembuatan resource? (Y/N)\033[0m ";
$response = strtolower(trim(fgets(STDIN)));

if ($response !== 'y') {
    abortProcess();
}

// Proses pembuatan resource tanpa pertanyaan konfirmasi tambahan
foreach ($modelsToProcess as $model) {
    $command = "php artisan make:filament-resource $model --simple --generate --view";
    exec($command, $output, $returnCode);

    if ($returnCode !== 0) {
        echo "\033[31mGagal membuat resource untuk $model. Pesan error:\033[0m\n";
        echo implode("\n", $output) . "\n";
    }
}

echo "\033[32mSemua resource berhasil dibuat.\033[0m\n";
