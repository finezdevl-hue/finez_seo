<?php
session_start();
// Administrative Access Only (Restricted from employees)
if (!isset($_SESSION['name']) || !isset($_SESSION['login_id']) || (int)$_SESSION['login_id'] !== 1) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

include '../../app_common/db_connect.php';
include '../../app_pagination/pagination.php';

    // action start
    if(isset($_POST['action']) && !empty($_POST['action'])) {
        $action = $_POST['action'];

        // action to save member details or update member details starts
        if($action=="save_members"){
            try{
                
                if($_POST['id']==0){
                    $sql ="INSERT INTO tbl_seo_members (first_name, middle_name,last_name,phone,user_name) VALUES (?, ?, ?, ?, ?)";
                    $parameters = [
                    $_POST['first_name'],
                    $_POST['middle_name'],
                    $_POST['last_name'],

                    $_POST['phone'],
                    $_POST['user_name'],
                    
                    ];

                    $types="sssss";
                
                }

                else{
                    $sql = "UPDATE tbl_seo_members SET  first_name = ?, middle_name = ?, last_name = ?, phone = ?, user_name = ? WHERE id = ?";
                    
                    $parameters = [
                    $_POST['first_name'],
                    $_POST['middle_name'],
                    $_POST['last_name'],

                    $_POST['phone'],
                    $_POST['user_name'],


                    $_POST['id'],
                    ];

                    $types="sssssi";
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
        // action to save member details  or update member details ends
             
        

        

        // action to save login for the members starts
        if($action=="save_login"){
            try{
                
                $sql ="INSERT INTO tbl_seo_login (name,user_name, password) VALUES (?, ?, ?)";
                $types="sss";

                $parameters = [
                   
                    $_POST['name'],
                    $_POST['user_name'],
                    $_POST['password'],
                ];
                app_exec_nonquery($sql, $parameters, $types);
            }
            catch (Throwable $e) {
                throw new Exception('Oops! Something went wrong.');
            }       
            
        }
        // action to save login for the members ends

       

        // action to load members data starts
        if($action=="load_members_data"){
            try{
                $rowsPerPage = 8;

                $current_page = (int)$_POST['page'];

                // Pagination logic
                $sql="";  
                $sqlcountrows="";
                $sqldatarows="";   
                
                $offset = ($current_page - 1) * $rowsPerPage;
                
                if($_POST['val']== ''){
                    // $sqldatarows = "SELECT f.id, f.first_name, f.middle_name, f.last_name, b.name AS blood_group FROM tbl_seo_members AS f JOIN tbl_bloodgroup_master AS b ON f.blood_group = b.id";
                    $sqldatarows = "SELECT  id,first_name,last_name,middle_name,phone,user_name FROM tbl_seo_members";
                     
                    $sqlcountrows = "SELECT  Count(id) as total FROM tbl_seo_members";
                }
                else{            

                    $sqldatarows = "SELECT id,first_name,last_name,middle_name,phone,user_name FROM tbl_seo_members
                    WHERE first_name LIKE ? OR middle_name LIKE ? OR last_name like ?"; 

                    $sqlcountrows = "SELECT  Count(id) as total
                    FROM tbl_seo_members WHERE first_name LIKE ? OR middle_name LIKE ? OR last_name like ?";         
                }    
                
                $sqldatarows .= " LIMIT $offset , $rowsPerPage ";
            
                if($_POST['val']== '') {
                    $result = app_exec_query($sqldatarows);
                    
                    $totalRowsResult = app_exec_query($sqlcountrows);
                    $totalRows = $totalRowsResult->fetch_assoc()['total'];
                }
                else{
                    $searchvalue= $_POST['val'];
                    $parameters = [
                        '%' . $searchvalue . '%',
                        '%' . $searchvalue . '%',
                        '%' . $searchvalue . '%',
                    ];
                    
                    $types="sss";
                    $result=app_exec_getresult($sqldatarows,$parameters,$types);

                    $totalRowsResult = app_exec_getresult($sqlcountrows,$parameters,$types);
                    $totalRows = $totalRowsResult->fetch_assoc()['total'];
                }

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

                $pagination = array("total_rows"=>$totalRows);
                $resdata=array($pagination,$qrydata);

                echo json_encode($resdata);
                exit();
            }
            catch (Throwable $e) {
                throw new Exception('Oops! Something went wrong.');
            }
            
        }
        // action to load members data ends
    }
?>