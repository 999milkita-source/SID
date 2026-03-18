<?php
require_once __DIR__ . '/../config/auth.php';

check_login();
require_role('admin');