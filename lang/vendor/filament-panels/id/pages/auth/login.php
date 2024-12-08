<?php

return [

    'title' => 'Masuk',

    'heading' => 'Masuk',

    'actions' => [

        'register' => [
            'before' => 'atau',
            'label' => 'buat akun baru',
        ],

        'request_password_reset' => [
            'label' => 'Lupa kata sandi?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Alamat email',
            'placeholder' => 'Masukkan Email'
        ],

        'password' => [
            'label' => 'Kata sandi',
            'placeholder' => 'Masukkan Kata Sandi'
        ],

        'remember' => [
            'label' => 'Ingat saya',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Masuk',
            ],

        ],

    ],

    'messages' => [

        'failed' => 'Kredensial yang diberikan tidak dapat ditemukan.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Terlalu banyak permintaan',
            'body' => 'Silakan coba lagi dalam :seconds detik.',
        ],

    ],

];
