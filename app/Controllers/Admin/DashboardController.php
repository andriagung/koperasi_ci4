<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

/**
 * DashboardController: Dashboard utama admin/peneliti.
 */
class DashboardController extends BaseController
{
    public function index()
    {
        $reportModel = new \App\Models\ReportModel();
        
        $stats = $reportModel->getQuickStats();
        $bySchool = $reportModel->getParticipationBySchool();

        return $this->renderView('admin/dashboard', [
            'title'    => 'Dashboard Admin — SIREMAJA',
            'stats'    => $stats,
            'bySchool' => $bySchool,
        ]);
    }
}
