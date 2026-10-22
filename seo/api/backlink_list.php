<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    include_once __DIR__ . '/../../config/db.php';
    include_once __DIR__ . '/../../includes/pagination.php';

    // actions start
    if(isset($_POST['action']) && !empty($_POST['action'])) {
        $action = $_POST['action'];

        // action to add the new fee details of a member starts
        if($action=="save_backlink"){ 
            try{
                if($_POST['id']==0){
                    date_default_timezone_set('Asia/Kolkata');
                    $currentDate = date('Y-m-d');
                    $currentTime =date('H-i-s');
                    $sql ="INSERT INTO tbl_seo_backlinks (project_id, links, content, user_name, password, login_id, date, time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                    
                    $parameters = [
                        $_SESSION['id'],
                        $_POST['links'],
                        $_POST['content'],
                        $_POST['user_name'],
                        $_POST['password'],
                        $_SESSION['login_id'],
                        $currentDate,
                        $currentTime,
                    ];

                    $types="isississ";
                }
                else{
                    if($_POST['login_id'] == $_SESSION['login_id']){

                        date_default_timezone_set('Asia/Kolkata');
                        $currentDate = date('Y-m-d');
                        $currentTime =date('H-i-s');
                        $sql = "UPDATE tbl_seo_backlinks SET links = ?, content = ?, user_name = ?, password= ?, date = ?, time = ? WHERE id = ?";
                        
                        $parameters = [
                            $_POST['links'],
                            $_POST['content'],
                            $_POST['user_name'],
                            $_POST['password'],
                            $currentDate,
                            $currentTime,
                            $_POST['id'],
                        ];

                        $types="sissssi";
                    }
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

       
        if($action=="load_project"){
            try{
                $sql = "SELECT id, name FROM tbl_seo_project";
                $result = app_exec_query($sql);
                //dropdown for load data
                echo "<div class='dropdown'><select id='selected_project' class='status-dropdown' onchange='onProjectChange()'>";
                echo "<option value='0' selected>All Projects</option>";
                // Check if query returned any results
                
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $id = $row['id'];
                        $name = $row['name'];                
                        echo "<option  value='$id'>$name</option>";
                    }
                } else {
                    echo "<option value='' disabled>No statuses available</option>";
                }
                echo "</select></div>";
                exit();

            }
            catch (Throwable $e) {
                throw new Exception('Oops! Something went wrong.');
            }

           
        }
      

                // action to load available months with entry counts
        if($action=="load_months"){
            try{
                $currentLoginId = (int)($_SESSION['login_id'] ?? 0);
                $isAdmin = ($currentLoginId === 1);
                $projectId = isset($_POST['project']) && $_POST['project'] !== '' ? (int)$_POST['project'] : 0;

                $whereParts = ["b.date_time >= '2000-01-01'"];
                if ($projectId > 0) {
                    $whereParts[] = "b.project_id = " . $projectId;
                }
                if (!$isAdmin) {
                    $whereParts[] = "b.login_id = " . $currentLoginId;
                }
                $whereClause = "WHERE " . implode(" AND ", $whereParts);

                $sql = "SELECT DATE_FORMAT(b.date_time, '%Y-%m') as ym, 
                               DATE_FORMAT(b.date_time, '%M %Y') as label, 
                               COUNT(b.id) as count 
                        FROM tbl_seo_backlinks b 
                        $whereClause 
                        GROUP BY ym, label 
                        ORDER BY ym DESC";

                $result = app_exec_query($sql);
                $months = [];
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $months[] = [
                            "ym" => $row['ym'],
                            "label" => $row['label'],
                            "count" => (int)$row['count']
                        ];
                    }
                }
                echo json_encode(["status" => "success", "months" => $months]);
                exit();
            }
            catch (Throwable $e) {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => $e->getMessage()]);
                exit();
            }
        }

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
                


                $currentLoginId = (int)($_SESSION['login_id'] ?? 0);
                $isAdmin = ($currentLoginId === 1);
                $projectId = isset($_POST['project']) && $_POST['project'] !== '' ? (int)$_POST['project'] : 0;
                $month = isset($_POST['month']) && !empty($_POST['month']) ? trim($_POST['month']) : '';

                $whereParts = [];
                if ($projectId > 0) {
                    $whereParts[] = "b.project_id = " . $projectId;
                }
                if (!$isAdmin) {
                    $whereParts[] = "b.login_id = " . $currentLoginId;
                }
                if (!empty($month) && preg_match('/^(\d{4})-(\d{2})$/', $month, $m)) {
                    $y = (int)$m[1];
                    $mon = (int)$m[2];
                    $startDt = sprintf('%04d-%02d-01 00:00:00', $y, $mon);
                    $endDt = date('Y-m-d 00:00:00', strtotime('+1 month', strtotime(sprintf('%04d-%02d-01', $y, $mon))));
                    $whereParts[] = "(b.date_time >= '$startDt' AND b.date_time < '$endDt')";
                }
                $whereClause = !empty($whereParts) ? "WHERE " . implode(" AND ", $whereParts) : "";
                $sqldatarows = "SELECT b.id, b.links, b.user_name, b.password, b.content, b.login_id, b.date_time, c.name, l.name as employee FROM tbl_seo_backlinks AS b LEFT JOIN tbl_seo_login AS l ON b.login_id = l.login_id LEFT JOIN tbl_seo_content_type_master AS c ON b.content = c.id $whereClause ORDER by b.date_time DESC";

                // echo $sqldatarows;
        
    
                $sqlcountrows = "SELECT COUNT(b.id) AS total FROM tbl_seo_backlinks AS b $whereClause";
                // echo $sqlcountrows;
                
                
                $sqldatarows .= " LIMIT $offset , $rowsPerPage ";
        
                
                $result = app_exec_query($sqldatarows);
                    
                $totalRowsResult = app_exec_query($sqlcountrows);
                $totalRows = $totalRowsResult->fetch_assoc()['total'];
                
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
        
                $pagination = array("total_rows" => $totalRows, "is_admin" => $isAdmin, "month" => $month, "project" => $projectId);
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

        if($action=="load_content"){
            try{
                $sql = "SELECT id, name FROM tbl_seo_content_type_master";
                $result = app_exec_query($sql);

                if ($result && $result->num_rows > 0) {
                    // Fetch all rows as an array
                    $qrydata = [];
                    while ($row = $result->fetch_assoc()) {
                        $qrydata[] = $row; // Add each row to the array
                    }
                } else {
                    $qrydata = []; // If no data is found
                }
        
                // $pagination = array("total_rows"=>$totalRows);
                $resdata=array($qrydata);
        
                echo json_encode($resdata);
                exit();
                
            }
            catch (Throwable $e) {
                throw new Exception('Oops! Something went wrong.');
            }
        }
    }
?>