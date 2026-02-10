<?php
include('pusher.php');

$data['id'] = 'userId';
$data['name'] = 'username';
$data['message'] = 'message';

$pusher->trigger('my-channel', 'my-event', $data);
