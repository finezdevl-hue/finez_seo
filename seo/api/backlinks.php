<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once __DIR__ . '/../../app_common/db_connect.php';
include_once __DIR__ . '/../../app_pagination/pagination.php';

if (!isset($_SESSION['name'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

// Ensure session project ID exists or fallback
function get_active_project_id() {
    if (isset($_POST['project_id']) && $_POST['project_id'] !== '') {
        $pId = (int)$_POST['project_id'];
        $_SESSION['id'] = $pId;
        return $pId;
    }
    if (isset($_SESSION['id'])) {
        return (int)$_SESSION['id'];
    }
    return 0; // Default to 0 (All Projects)
}

if (isset($_POST['action']) && !empty($_POST['action'])) {
    $action = $_POST['action'];

    // =========================================================================
    // 1. Save or Update Backlink
    // =========================================================================
    if ($action == "save_backlink") {
        try {
            date_default_timezone_set('Asia/Kolkata');
            $currentDateTime = date('Y-m-d H:i:s');
            
            $projectId = get_active_project_id();
            $links = trim($_POST['links'] ?? '');
            $content = (int)($_POST['content'] ?? 1);
            $userName = trim($_POST['user_name'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $loginId = (int)($_SESSION['login_id'] ?? 1);
            $id = (int)($_POST['id'] ?? 0);

            if (empty($links)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Backlink URL is required.']);
                exit();
            }

            if ($id === 0) {
                // Insert new backlink
                $sql = "INSERT INTO tbl_seo_backlinks (project_id, links, content, user_name, password, login_id, date_time) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $parameters = [$projectId, $links, $content, $userName, $password, $loginId, $currentDateTime];
                $types = "isissis";
            } else {
                // Update existing backlink (admin or owner)
                $sql = "UPDATE tbl_seo_backlinks SET project_id = ?, links = ?, content = ?, user_name = ?, password = ?, date_time = ? WHERE id = ?";
                $parameters = [$projectId, $links, $content, $userName, $password, $currentDateTime, $id];
                $types = "isisssi";
            }

            ob_start();
            app_exec_nonquery($sql, $parameters, $types);
            ob_end_clean();
            echo json_encode(['status' => 'success', 'message' => 'Saved Successfully']);
            exit();
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            exit();
        }
    }

    // =========================================================================
    // 2. Load Projects List (For Project Filter / Selector)
    // =========================================================================
    if ($action == "load_all_projects") {
        try {
            $sql = "SELECT id, name, url FROM tbl_seo_project ORDER BY name ASC";
            $res = app_exec_query($sql);
            $projects = [];
            while ($row = $res->fetch_assoc()) {
                $projects[] = $row;
            }
            echo json_encode(['status' => 'success', 'projects' => $projects, 'active_project_id' => get_active_project_id()]);
            exit();
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            exit();
        }
    }

    // =========================================================================
    // 3. Load Employee List (For User Filter Dropdown)
    // =========================================================================
    if ($action == "load_project") {
        try {
            $currentLoginId = (int)($_SESSION['login_id'] ?? 0);
            $isAdmin = ($currentLoginId === 1);

            if ($isAdmin) {
                // Admin can view and filter across all team members
                $sql = "SELECT login_id, name FROM tbl_seo_login ORDER BY name ASC";
                $result = app_exec_query($sql);

                echo "<div class='dropdown'><select id='selected_employee' class='status-dropdown' onchange='loadData(1)'>";
                echo "<option value='0' selected>All Team Members</option>";
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $id = (int)$row['login_id'];
                        $name = htmlspecialchars($row['name']);
                        $label = ($id === $currentLoginId) ? "$name (Admin)" : $name;
                        echo "<option value='$id'>$label</option>";
                    }
                }
                echo "</select></div>";
            } else {
                // Employee only sees their own backlink history - locked to self
                $empName = htmlspecialchars($_SESSION['name'] ?? 'My Submissions');
                echo "<input type='hidden' id='selected_employee' value='$currentLoginId'>";
                echo "<span class='badge-modern badge-subtle-success' style='height: 38px; display: inline-flex; align-items: center; padding: 0 14px; font-size: 12px; font-weight: 700;'><i class='fa fa-user-circle'></i> $empName (My Submissions)</span>";
            }
            exit();
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            exit();
        }
    }

    // =========================================================================
    // 4. Load Backlinks Data (Paginated & Filtered)
    // =========================================================================
    if ($action == "load_data") {
        try {
            $rowsPerPage = 8;
            $current_page = max(1, (int)($_POST['page'] ?? 1));
            $offset = ($current_page - 1) * $rowsPerPage;

            $currentLoginId = (int)($_SESSION['login_id'] ?? 0);
            $isAdmin = ($currentLoginId === 1);

            $projectId = get_active_project_id();

            // CRITICAL: Employees only ever see their OWN backlink history
            if (!$isAdmin) {
                $employeeId = $currentLoginId;
            } else {
                $employeeId = isset($_POST['employee']) && $_POST['employee'] !== '' ? (int)$_POST['employee'] : 0;
            }
            $searchVal = trim($_POST['val'] ?? '');

            $whereParts = [];
            if ($projectId > 0) {
                $whereParts[] = "b.project_id = " . $projectId;
            }
            if ($employeeId > 0) {
                $whereParts[] = "b.login_id = " . $employeeId;
            }
            if (!empty($searchVal)) {
                $safeSearch = addslashes($searchVal);
                $whereParts[] = "(b.links LIKE '%$safeSearch%' OR b.user_name LIKE '%$safeSearch%' OR c.name LIKE '%$safeSearch%' OR l.name LIKE '%$safeSearch%' OR p.name LIKE '%$safeSearch%')";
            }

            $whereSql = !empty($whereParts) ? "WHERE " . implode(" AND ", $whereParts) : "";

            $sqldatarows = "SELECT b.id, b.project_id, b.links, b.user_name, b.password, b.content, b.login_id, DATE_FORMAT(b.date_time, '%Y-%m-%d %H:%i') AS entry_date, c.name, l.name AS employee_name, p.name AS project_name
                FROM tbl_seo_backlinks AS b 
                LEFT JOIN tbl_seo_project AS p ON b.project_id = p.id
                LEFT JOIN tbl_seo_content_type_master AS c ON b.content = c.id 
                LEFT JOIN tbl_seo_login AS l ON b.login_id = l.login_id 
                $whereSql 
                ORDER BY b.date_time DESC 
                LIMIT $offset, $rowsPerPage";

            $sqlcountrows = "SELECT COUNT(b.id) AS total 
                FROM tbl_seo_backlinks AS b 
                LEFT JOIN tbl_seo_project AS p ON b.project_id = p.id
                LEFT JOIN tbl_seo_content_type_master AS c ON b.content = c.id 
                LEFT JOIN tbl_seo_login AS l ON b.login_id = l.login_id 
                $whereSql";

            $result = app_exec_query($sqldatarows);
            $totalRowsResult = app_exec_query($sqlcountrows);
            $totalRows = (int)($totalRowsResult ? $totalRowsResult->fetch_assoc()['total'] : 0);

            $qrydata = [];
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $qrydata[] = $row;
                }
            }

            $pagination = array(
                "total_rows" => $totalRows,
                "current_login_id" => $currentLoginId,
                "is_admin" => $isAdmin
            );
            $resdata = array($pagination, $qrydata);

            echo json_encode($resdata);
            exit();
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            exit();
        }
    }

    // =========================================================================
    // 5. Load Content Categories
    // =========================================================================
    if ($action == "load_content") {
        try {
            $sql = "SELECT id, name FROM tbl_seo_content_type_master ORDER BY name ASC";
            $result = app_exec_query($sql);
            $qrydata = [];
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $qrydata[] = $row;
                }
            }
            $resdata = array($qrydata);
            echo json_encode($resdata);
            exit();
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            exit();
        }
    }
}
