<?php

namespace App\Libraries;

use Hashids\Hashids;

class IdHash
{
    protected Hashids $hashids;

    public function __construct()
    {
        // Salt WAJIB dari .env, minimal panjang 8 karakter, unik per aplikasi
        $this->hashids = new Hashids(env('app.idHashSalt') ?: 'koperasi_secret_salt_2026', 8);
    }

    public function encode(int $id): string
    {
        return $this->hashids->encode($id);
    }

    /**
     * Return null jika hash tidak valid — Controller wajib memperlakukan
     * ini sebagai 404, bukan error 500.
     */
    public function decode(string $hash): ?int
    {
        $decoded = $this->hashids->decode($hash);
        return $decoded[0] ?? null;
    }
}
