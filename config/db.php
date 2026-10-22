<?php

function get_db_connection() {
    static $conn = null;
    if ($conn !== null && @$conn->ping()) {
        return $conn;
    }

    // Using 127.0.0.1 instead of localhost avoids 2+ second Windows IPv6 [::1] DNS resolution lag
    $servername = "127.0.0.1";
    // $username = "finezin_dbu_seo";
    // $password = "Seo@2025*!";
    // $dbname = "finezin_db_seo";
    $username = "root";
    $password = "";
    $dbname = "finez_seo";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
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