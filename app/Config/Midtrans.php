<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Midtrans extends BaseConfig
{
    // Mode Sandbox untuk development
    public $isProduction = false;
    
    // Key Sandbox Midtrans
    // Note: Ini hanya contoh. Untuk production, isi dengan key production asli.
    public $serverKey = 'SB-Mid-server-EXAMPLE_KEY_HERE';
    public $clientKey = 'SB-Mid-client-EXAMPLE_KEY_HERE';
    
    public $is3ds = true;
    public $isSanitized = true;
}
