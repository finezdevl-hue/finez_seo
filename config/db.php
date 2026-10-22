<?php

function get_db_connection() {
    static $conn = null;
    if ($conn !== null && @$conn->ping()) {
        return $conn;
    }

    // -------------------------------------------------------------------------
    // Database Configuration
    // -------------------------------------------------------------------------
    // For live hosting, you can set your production credentials directly here,
    // or set environment variables (DB_HOST, DB_USER, DB_PASS, DB_NAME).
    
    // Production Example:
    // $servername = "localhost";
    // $username   = "finezin_dbu_seo";
    // $password   = "Seo@2025*!";
    // $dbname     = "finezin_db_seo";

    $servername = getenv('DB_HOST') ?: "127.0.0.1";
    $username   = getenv('DB_USER') ?: "root";
    $password   = getenv('DB_PASS') !== false ? getenv('DB_PASS') : "";
    $dbname     = getenv('DB_NAME') ?: "finez_seo";
    $port       = (int)(getenv('DB_PORT') ?: 3306);

    $conn = new mysqli($servername, $username, $password, $dbname, $port);
    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4");
    return $conn;
}

function app_exec_query($sql){
    try{
        $conn = get_db_connection();
        return $conn->query($sql);
    } 
    catch (Exception $e){
        http_response_code(500);   
    }
}

function app_exec_nonquery(string $sql, array $parameters, string $types){
    try{
        $conn = get_db_connection();
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$parameters);
        if ($stmt->execute()) {
            echo "Success";
        }
        else {
            echo "Error";
        }
        $stmt->close();
    }
    catch (Exception $e){
        http_response_code(500);
    }
}

function app_exec_getresult(string $sql, array $parameters, string $types){
    try{
        $conn = get_db_connection();
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$parameters);
        $stmt->execute();
        return $stmt->get_result() ?: null;  
    }
    catch (Exception $e){
        http_response_code(500);
    }
}

function getresult(string $sql, array $values, ?string $types = null): ?mysqli_result
{
    try{
        $conn = get_db_connection();
        $stmt = $conn->prepare($sql);    
        $stmt->bind_param($types, ...$values);
        $stmt->execute();
        return $stmt->get_result() ?: null;
    }
    catch (Exception $e){
        http_response_code(500);
    }
}

?>
