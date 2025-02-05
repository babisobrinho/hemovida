<?php

    include 'db_functions.php';

    if (isset($_GET['id']) && isset($_GET['table'])) {
        $id = $_GET['id'];
        $table = $_GET['table'];
    
        $deleted = deleteRecordById($pdo, $table, $id);
    
        if ($deleted) {
            header('Location: ../' . $table . '.php?success=record_deleted');
            exit;
        } else {
            header('Location: ../' . $table . '.php?error=delete_failed');
            exit;
        }
    
    } else {
        header('Location: ../index.php?error=no_id_or_table');
        exit;
    }
    
?>
    