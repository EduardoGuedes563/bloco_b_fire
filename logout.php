<?php
session_start();
require_once 'includes/auth.php';

logout();
redirecionar('/bloco_b_fire/index.php');