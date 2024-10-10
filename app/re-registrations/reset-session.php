<?php
session_start();
unset($_SESSION['active_rr_session_id']);
unset($_SESSION['active_rr_session_name']);
unset($_SESSION['show_action_in_active_rr']);
echo json_encode(['status' => true]);
