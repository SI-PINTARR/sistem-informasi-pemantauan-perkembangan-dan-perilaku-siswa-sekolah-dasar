<?php
require 'koneksi.php';
session_destroy();
echo json_encode(['ok' => true]);
