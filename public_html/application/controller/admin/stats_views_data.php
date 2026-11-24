<?php include_once_path('application/core/admin');
include_once_path('application/model/stats_model');
json_out(view_stats((string) input('from', date('Y-m-d', strtotime('-29 days'))), (string) input('to', date('Y-m-d')), !empty($_GET['dedupe']) && $_GET['dedupe'] !== '0'));
