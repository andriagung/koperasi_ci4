<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class BackupDatabase extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Maintenance';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'db:backup';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Melakukan backup struktur dan data database.';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'db:backup';

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        CLI::write('Mempersiapkan proses backup database...', 'yellow');

        $dbName = env('database.default.database', 'koperasi_rsud');
        $dbUser = env('database.default.username', 'root');
        $dbPass = env('database.default.password', '');
        
        $backupDir = WRITEPATH . 'backups';
        
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0777, true);
        }

        $filename = 'backup_' . $dbName . '_' . date('Y-m-d_H-i-s') . '.sql';
        $filepath = $backupDir . '/' . $filename;

        // Command for mysqldump
        $passwordArg = !empty($dbPass) ? "-p{$dbPass}" : "";
        $command = "mysqldump -u {$dbUser} {$passwordArg} {$dbName} > {$filepath}";

        // Execute command
        system($command, $returnVar);

        if ($returnVar === 0) {
            CLI::write('Backup berhasil diselesaikan!', 'green');
            CLI::write('File disimpan di: ' . $filepath, 'green');
            
            // Clean up old backups (older than 30 days)
            $this->cleanupOldBackups($backupDir);
        } else {
            CLI::error('Gagal melakukan backup database. Pastikan mysqldump tersedia di environment Anda.');
            CLI::error('Return variable: ' . $returnVar);
        }
    }

    /**
     * Delete backups older than 30 days
     */
    private function cleanupOldBackups($dir)
    {
        $files = glob($dir . '/*.sql');
        $now   = time();

        foreach ($files as $file) {
            if (is_file($file)) {
                if ($now - filemtime($file) >= 60 * 60 * 24 * 30) {
                    unlink($file);
                    CLI::write("File backup lama dihapus: " . basename($file), 'yellow');
                }
            }
        }
    }
}
