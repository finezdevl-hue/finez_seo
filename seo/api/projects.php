<?php
    session_start();
    include __DIR__ . '/../../config/db.php';
    include __DIR__ . '/../../includes/pagination.php';

    // actions start
    if(isset($_POST['action']) && !empty($_POST['action'])) {
        $action = $_POST['action'];

        // action to update the fee details of a member starts
        if($action=="save_fees"){ 
            try{
                
                $sql ="UPDATE tbl_fee_master SET from_date = ?, to_date = ?, fee = ? WHERE id = ? ";
                
                $parameters = [
                $_POST['from_date'],
                $_POST['to_date'],
                $_POST['fees'],
                $_POST['id'],
                ];

                $types="ssii";
                
                app_exec_nonquery($sql, $parameters, $types);
                
            }
            // catch (Throwable $e) {
            //     throw new Exception('Oops! Something went wrong.');
            // }
            catch (Exception $e) {
                http_response_code(500);   
                echo "Oops! Something went wrong." . $e->getMessage();
                return;
            }
        }
        // action to update the fee details of a member ends

        // action to add the new fee details of a member starts
        if($action=="save_projects"){ 
            if (!isset($_SESSION['login_id']) || (int)$_SESSION['login_id'] !== 1) {
                http_response_code(403);
                echo "Unauthorized. Only administrators can add or modify projects.";
                exit();
            }
            try{
                if($_POST['id']==0){
                
                    $sql ="INSERT INTO tbl_seo_project (name, url) VALUES (?, ?)";
                    
                    $parameters = [
                    $_POST['name'],
                    $_POST['url'],
                    
                    ];

                    $types="ss";
                }
                else{
                    $sql = "UPDATE tbl_seo_project SET name = ?, url = ? WHERE id = ?";
                    
                    $parameters = [
                    $_POST['name'],
                    $_POST['url'],
                    $_POST['id'],
                    ];

                    $types="ssi";
                }
                app_exec_nonquery($sql, $parameters, $types);
            }
            // catch (Throwable $e) {
            //     throw new Exception('Oops! Something went wrong.');
            // }
            catch (Exception $e) {
                http_response_code(500);   
                echo "Oops! Something went wrong." . $e->getMessage();
                return;
            }
        }
        // action to add the new fee details of a member ends

        //action to delete fee details start
        if($action=="delete_fee_details"){
            try{
                $id = $_POST['id'];
                $sql = "DELETE FROM tbl_fee_master WHERE id = ?";
                $types="i";
                $parameters = [
                $_POST['id'],
                ];
    
                app_exec_nonquery($sql, $parameters, $types);
    
            }
            catch (Throwable $e) {
                throw new Exception('Oops! Something went wrong.');
            }
        }
        //action to delete fee details end

        

        // action to load all fee details starts
        if($action=="load_data"){
            try{
                $rowsPerPage = 8;
                $current_page = (int)$_POST['page'];
                // Pagination logic
                $sql="";  
                $sqlcountrows="";
                $sqldatarows="";   
        
                $offset = ($current_page - 1) * $rowsPerPage;

                if($_POST['val']== ''){
                    $sqldatarows ="SELECT id,name,url
                    FROM tbl_seo_project";
            
                    $sqlcountrows = "SELECT COUNT(id) AS total FROM tbl_seo_project ";
                
                }
                else{            

                    $sqldatarows = "SELECT id,name,url FROM tbl_seo_project
                    WHERE name LIKE ?"; 

                    $sqlcountrows = "SELECT  Count(id) as total
                    FROM tbl_seo_project WHERE name LIKE ?";         
                }

                if($_POST['val']== ''){
                    $result = app_exec_query($sqldatarows);
                    
                    $totalRowsResult = app_exec_query($sqlcountrows);
                    $totalRows = $totalRowsResult->fetch_assoc()['total'];
                }

                else{
                    $searchvalue= $_POST['val'];
                    $parameters = [
                        '%' . $searchvalue . '%',
                    ];
                    
                    $types="s";
                    $result=app_exec_getresult($sqldatarows,$parameters,$types);

                    $totalRowsResult = app_exec_getresult($sqlcountrows,$parameters,$types);
                    $totalRows = $totalRowsResult->fetch_assoc()['total'];
                }

               
                $sqldatarows .= " LIMIT $offset , $rowsPerPage ";
                //stringify
                if ($result && $result->num_rows > 0) {
                    // Fetch all rows as an array
                    $qrydata = [];
                    while ($row = $result->fetch_assoc()) {
                        $qrydata[] = $row; // Add each row to the array
                    }
                } else {
                    $qrydata = []; // If no data is found
                }
        
                $isAdmin = (!empty($_SESSION['login_id']) && (int)$_SESSION['login_id'] === 1);
                $pagination = array("total_rows" => $totalRows, "is_admin" => $isAdmin);
                $resdata=array($pagination,$qrydata);
        
                echo json_encode($resdata);
                exit();
            }
            // catch (Throwable $e) {
            //     throw new Exception('Oops! Something went wrong.');
            // }
            catch (Exception $e) {
                http_response_code(500);   
                echo "Oops! Something went wrong." . $e->getMessage();
                return;
            }
        }
        // action to load all fee details ends
    }
?>