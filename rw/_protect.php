<?php
require_once '../config/config.php';
require_once '../config/auth.php';

ensure_session_started();
set_security_headers();
check_login();
require_role('rw');

