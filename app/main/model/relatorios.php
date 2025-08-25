<?php
require_once '.php';


function getRelatorios() {
    global $conn;
    $query = "SELECT * FROM relatorios";
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }
    
    $relatorios = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $relatorios[] = $row;
    }
    
    return $relatorios;
}
  

?>
