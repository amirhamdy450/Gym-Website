<?php
// Includes/Memberships.php

// Fetch all membership plans
function GetMemberships($Pdo) {
    try {
        $Stmt = $Pdo->query("SELECT * FROM Memberships ORDER BY Price ASC");
        return $Stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}
?>
