<?php

include_once __DIR__ . '/../includes/session.php';

Auth::logout();

header('Location: /');