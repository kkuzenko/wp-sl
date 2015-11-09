<?php
$term_id = get_queried_object()->term_id;
$event_meta = json_decode(get_option('event_meta_'. $term_id),true);

$path = get_template_directory();
$path .= '/events-tpl/'. $event_meta['shortname'] . '.php';
require_once($path);