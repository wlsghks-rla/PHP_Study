<?php include_once_path('application/core/admin');
include_once_path('application/model/stats_model');
$t = in_array(input('type'), ['year', 'month', 'day', 'hour']) ? input('type') : 'year';
json_out(menu_stats($t, (int) input('year', date('Y')), max(1, min(12, (int) input('month', date('n')))), !empty($_GET['dedupe'])));
