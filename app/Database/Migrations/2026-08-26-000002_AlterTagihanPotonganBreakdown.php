<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterTagihanPotonganBreakdown extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tagihan_potongan', [
            'pot_sp' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0,
                'after'      => 'nominal_simpanan_wajib'
            ],
            'pot_ss' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0,
                'after'      => 'pot_sp'
            ],
            'pot_sl' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0,
                'after'      => 'pot_ss'
            ],
            'pot_ppu' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0,
                'after'      => 'pot_sl'
            ],
            'pot_bpu' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0,
                'after'      => 'pot_ppu'
            ],
            'pot_ppb' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0,
                'after'      => 'pot_bpu'
            ],
            'pot_bpb' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0,
                'after'      => 'pot_ppb'
            ],
            'pot_pps' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0,
                'after'      => 'pot_bpb'
            ],
            'pot_bps' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0,
                'after'      => 'pot_pps'
            ],
            'dansos' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0,
                'after'      => 'pot_bps'
            ],
            'pangan' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0,
                'after'      => 'dansos'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('tagihan_potongan', [
            'pot_sp', 'pot_ss', 'pot_sl',
            'pot_ppu', 'pot_bpu',
            'pot_ppb', 'pot_bpb',
            'pot_pps', 'pot_bps',
            'dansos', 'pangan'
        ]);
    }
}
