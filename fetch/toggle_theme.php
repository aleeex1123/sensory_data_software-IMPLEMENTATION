<?php
session_start();

// Flip theme
if (isset($_SESSION['theme'])) {
    $_SESSION['theme'] = ($_SESSION['theme'] == 1) ? 0 : 1;
} else {
    $_SESSION['theme'] = 1;
}

echo json_encode(['theme' => $_SESSION['theme']]);
