<?php
include_once_path('application/model/survey_model');
$s = survey_find((int) input('id'));
render('survey/intro', ['title' => $s['title'], 's' => $s, 'block' => survey_block($s)]);
