<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>

<?php
// Route to the specific dashboard view based on the type passed from controller
if (isset($dashboard_type)) {
    echo view('admin/dashboards/' . $dashboard_type);
} else {
    // Default fallback
    echo view('admin/dashboards/executive');
}
?>

<?= $this->endSection() ?>
