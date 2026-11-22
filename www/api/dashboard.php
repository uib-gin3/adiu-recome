<?php
// Data for the three dashboard visualizations
require __DIR__ . '/../app/Dashboard.php';
require __DIR__ . '/common.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    methodNotAllowed();
}
// The family filter is a plain string compared as a query parameter
$family = isset($_GET['family']) ? trim((string)$_GET['family']) : '';
sendJson(Dashboard::data($family));
