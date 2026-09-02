<?php

// app/Language/id/Validation.php

return [
    // Core Rules
    'required'      => 'Kolom {field} harus diisi.',
    'isset'         => 'Kolom {field} harus memiliki nilai.',
    'valid_email'   => 'Kolom {field} harus berisi alamat email yang sah.',
    'valid_emails'  => 'Kolom {field} harus berisi semua alamat email yang sah.',
    'valid_url'     => 'Kolom {field} harus berisi URL yang sah.',
    'valid_ip'      => 'Kolom {field} harus berisi IP yang sah.',
    'min_length'    => 'Panjang karakter kolom {field} minimal {param} karakter.',
    'max_length'    => 'Panjang karakter kolom {field} tidak boleh lebih dari {param} karakter.',
    'exact_length'  => 'Panjang karakter kolom {field} harus tepat {param} karakter.',
    'alpha'         => 'Kolom {field} hanya boleh berisi karakter alfabet.',
    'alpha_space'   => 'Kolom {field} hanya boleh berisi karakter alfabet dan spasi.',
    'alpha_dash'    => 'Kolom {field} hanya boleh berisi karakter alfanumerik, underscore, dan dash.',
    'alpha_numeric' => 'Kolom {field} hanya boleh berisi karakter alfanumerik.',
    'alpha_numeric_space'  => 'Kolom {field} hanya boleh berisi karakter alfanumerik dan spasi.',
    'alpha_numeric_punct'  => 'Kolom {field} hanya boleh berisi karakter alfanumerik, spasi, dan karakter baca standar.',
    'integer'       => 'Kolom {field} harus berisi bilangan bulat (integer).',
    'decimal'       => 'Kolom {field} harus berisi angka desimal.',
    'is_natural'    => 'Kolom {field} hanya boleh berisi angka.',
    'is_natural_no_zero' => 'Kolom {field} hanya boleh berisi angka dan harus bernilai lebih dari nol.',
    'is_unique'     => 'Data pada kolom {field} sudah terdaftar, silakan gunakan nilai lain.',
    'matches'       => 'Kolom {field} tidak cocok dengan kolom {param}.',
    'differs'       => 'Kolom {field} harus berbeda dari kolom {param}.',
    'in_list'       => 'Kolom {field} harus berisi salah satu dari nilai berikut: {param}.',
    'not_in_list'   => 'Kolom {field} tidak boleh berisi salah satu dari nilai berikut: {param}.',
    'numeric'       => 'Kolom {field} harus berisi angka.',
    'regex_match'   => 'Kolom {field} tidak dalam format yang benar.',
    'timezone'      => 'Kolom {field} harus berisi zona waktu yang sah.',
    'valid_base64'  => 'Kolom {field} harus berisi string base64 yang sah.',
    'valid_json'    => 'Kolom {field} harus berisi JSON yang sah.',

    // File/Upload Rules
    'uploaded'      => 'File {field} belum dipilih.',
    'max_size'      => 'Ukuran file {field} terlalu besar.',
    'is_image'      => 'File {field} harus berupa gambar.',
    'mime_in'       => 'Tipe file {field} tidak diizinkan.',
    'ext_in'        => 'Ekstensi file {field} tidak diizinkan.',
    'max_dims'      => 'Dimensi gambar {field} tidak sesuai.',
];
