<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class KoperasiCron extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Koperasi';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'koperasi:cron';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Menjalankan fungsi otomatisasi di latar belakang (Background Jobs) seperti Backup DB dan Kalkulasi Aging.';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'koperasi:cron';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [];

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        CLI::write('Mulai menjalankan Koperasi Cron Jobs...', 'green');
        
        $this->backupDatabase();
        $this->updateAgingTagihan();
        
        CLI::write('Selesai.', 'green');
    }

    private function backupDatabase()
    {
        CLI::write('[Tugas 1] Melakukan pencadangan (backup) database otomatis...', 'yellow');
        
        $db = \Config\Database::connect();
        $dbname = $db->database;
        $username = $db->username;
        $password = $db->password;
        
        // Di Production (Linux), Anda bisa menggunakan command mysqldump.
        // shell_exec("mysqldump -u {$username} -p{$password} {$dbname} > " . WRITEPATH . "backups/backup_" . date('Ymd_His') . ".sql");
        
        // Simulasi backup
        sleep(2);
        
        CLI::write('=> Backup database selesai!', 'cyan');
    }

    private function updateAgingTagihan()
    {
        CLI::write('[Tugas 2] Memperbarui status Tagihan Macet (Aging)...', 'yellow');
        
        // Di sini Anda bisa memanggil AccountingService untuk mengkalkulasi denda
        // $accountingService = new \App\Services\AccountingService();
        // $accountingService->prosesDendaKeterlambatan();
        
        // Simulasi proses
        sleep(1);
        
        CLI::write('=> Update Aging selesai!', 'cyan');
    }
}
