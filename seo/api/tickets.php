<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once __DIR__ . '/../../app_common/db_connect.php';
include_once __DIR__ . '/../../app_pagination/pagination.php';

if (!headers_sent()) {
    header('Content-Type: application/json; charset=utf-8');
}

if (!isset($_SESSION['name'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$conn = get_db_connection();

if (isset($_POST['action']) && !empty($_POST['action'])) {
    $action = $_POST['action'];

    // =========================================================================
    // 1. Get SEO KPI Summary Metrics
    // =========================================================================
    if ($action == "get_kpis") {
        try {
            $totalActive = 0;
            $linksQuota = 0;
            $linksCompleted = 0;
            $reviewCount = 0;
            $urgentCount = 0;

            // Active tasks (Open or In Progress)
            $r1 = $conn->query("SELECT COUNT(*) AS c FROM tbl_seo_tickets WHERE status IN ('Open', 'In Progress')");
            if ($r1) $totalActive = (int)$r1->fetch_assoc()['c'];

            // Link Quota vs Completed
            $r2 = $conn->query("SELECT COALESCE(SUM(required_links_count), 0) AS req, COALESCE(SUM(completed_links_count), 0) AS comp FROM tbl_seo_tickets");
            if ($r2) {
                $row2 = $r2->fetch_assoc();
                $linksQuota = (int)$row2['req'];
                $linksCompleted = (int)$row2['comp'];
            }

            // Pending QA Review
            $r3 = $conn->query("SELECT COUNT(*) AS c FROM tbl_seo_tickets WHERE status = 'Under Review'");
            if ($r3) $reviewCount = (int)$r3->fetch_assoc()['c'];

            // Urgent / Broken links
            $r4 = $conn->query("SELECT COUNT(*) AS c FROM tbl_seo_tickets WHERE (priority = 'Urgent' OR category = 'Broken Link Replacement') AND status NOT IN ('Completed', 'Closed')");
            if ($r4) $urgentCount = (int)$r4->fetch_assoc()['c'];

            echo json_encode([
                'status' => 'success',
                'active_tasks' => $totalActive,
                'links_quota' => $linksQuota,
                'links_completed' => $linksCompleted,
                'pending_qa' => $reviewCount,
                'urgent_tasks' => $urgentCount
            ]);
            exit();
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
            exit();
        }
    }

    // =========================================================================
    // 2. Get Filter Options (Projects & Specialists)
    // =========================================================================
    if ($action == "get_filter_options") {
        try {
            $projects = [];
            $rp = $conn->query("SELECT id, name, url FROM tbl_seo_project ORDER BY name ASC");
            if ($rp) {
                while ($row = $rp->fetch_assoc()) {
                    $projects[] = $row;
                }
            }

            $specialists = [];
            $rd = $conn->query("SELECT login_id, name, user_name FROM tbl_seo_login ORDER BY name ASC");
            if ($rd) {
                while ($row = $rd->fetch_assoc()) {
                    $specialists[] = $row;
                }
            }

            $categories = [
                'Link Building',
                'Broken Link Replacement',
                'On-Page / Technical SEO',
                'Keyword & Anchor Strategy',
                'Content Writing',
                'SEO Audit / Indexing',
                'Client Request'
            ];

            echo json_encode([
                'status' => 'success',
                'projects' => $projects,
                'specialists' => $specialists,
                'categories' => $categories
            ]);
            exit();
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
            exit();
        }
    }

    // =========================================================================
    // 3. Load SEO Tickets (Paginated List View)
    // =========================================================================
    if ($action == "load_data") {
        try {
            $rowsPerPage = 10;
            $currentPage = isset($_POST['page']) ? (int)$_POST['page'] : 1;
            if ($currentPage < 1) $currentPage = 1;
            $offset = ($currentPage - 1) * $rowsPerPage;

            $whereClauses = ["1=1"];
            $params = [];
            $types = "";

            // Search Keyword
            if (!empty($_POST['val'])) {
                $term = "%" . trim($_POST['val']) . "%";
                $whereClauses[] = "(t.ticket_code LIKE ? OR t.title LIKE ? OR t.target_url LIKE ? OR t.target_keywords LIKE ? OR t.description LIKE ?)";
                $params[] = $term; $params[] = $term; $params[] = $term; $params[] = $term; $params[] = $term;
                $types .= "sssss";
            }

            // Project filter
            if (!empty($_POST['project_id']) && (int)$_POST['project_id'] > 0) {
                $whereClauses[] = "t.project_id = ?";
                $params[] = (int)$_POST['project_id'];
                $types .= "i";
            }

            // Category filter
            if (!empty($_POST['category'])) {
                $whereClauses[] = "t.category = ?";
                $params[] = $_POST['category'];
                $types .= "s";
            }

            // Priority filter
            if (!empty($_POST['priority'])) {
                $whereClauses[] = "t.priority = ?";
                $params[] = $_POST['priority'];
                $types .= "s";
            }

            // Status filter
            if (!empty($_POST['status'])) {
                $whereClauses[] = "t.status = ?";
                $params[] = $_POST['status'];
                $types .= "s";
            }

            // Assignee filter
            if (!empty($_POST['assigned_to']) && (int)$_POST['assigned_to'] > 0) {
                $whereClauses[] = "t.assigned_to = ?";
                $params[] = (int)$_POST['assigned_to'];
                $types .= "i";
            }

            $whereSql = implode(" AND ", $whereClauses);

            // Count Query
            $countSql = "SELECT COUNT(t.id) as total FROM tbl_seo_tickets t WHERE $whereSql";
            $stmtCount = $conn->prepare($countSql);
            if (!empty($types)) {
                $stmtCount->bind_param($types, ...$params);
            }
            $stmtCount->execute();
            $totalRows = (int)$stmtCount->get_result()->fetch_assoc()['total'];
            $stmtCount->close();

            // Data Query
            $dataSql = "SELECT t.*, p.name AS project_name, p.url AS project_url,
                               COALESCE(u.name, 'Unassigned') AS assignee_name, 
                               COALESCE(r.name, 'Admin') AS reporter_name 
                        FROM tbl_seo_tickets t 
                        LEFT JOIN tbl_seo_project p ON t.project_id = p.id 
                        LEFT JOIN tbl_seo_login u ON t.assigned_to = u.login_id 
                        LEFT JOIN tbl_seo_login r ON t.created_by = r.login_id 
                        WHERE $whereSql 
                        ORDER BY FIELD(t.priority, 'Urgent', 'High', 'Medium', 'Low'), t.id DESC 
                        LIMIT $offset, $rowsPerPage";

            $stmtData = $conn->prepare($dataSql);
            if (!empty($types)) {
                $stmtData->bind_param($types, ...$params);
            }
            $stmtData->execute();
            $result = $stmtData->get_result();

            $qrydata = [];
            while ($row = $result->fetch_assoc()) {
                $qrydata[] = $row;
            }
            $stmtData->close();

            $pagination = ["total_rows" => $totalRows];
            echo json_encode([$pagination, $qrydata]);
            exit();
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
            exit();
        }
    }

    // =========================================================================
    // 4. Load Campaign Board (Kanban for SEO Tasks)
    // =========================================================================
    if ($action == "load_campaign_board") {
        try {
            $whereClauses = ["1=1"];
            $params = [];
            $types = "";

            if (!empty($_POST['val'])) {
                $term = "%" . trim($_POST['val']) . "%";
                $whereClauses[] = "(t.ticket_code LIKE ? OR t.title LIKE ? OR t.target_keywords LIKE ?)";
                $params[] = $term; $params[] = $term; $params[] = $term;
                $types .= "sss";
            }

            if (!empty($_POST['project_id']) && (int)$_POST['project_id'] > 0) {
                $whereClauses[] = "t.project_id = ?";
                $params[] = (int)$_POST['project_id'];
                $types .= "i";
            }

            if (!empty($_POST['category'])) {
                $whereClauses[] = "t.category = ?";
                $params[] = $_POST['category'];
                $types .= "s";
            }

            if (!empty($_POST['priority'])) {
                $whereClauses[] = "t.priority = ?";
                $params[] = $_POST['priority'];
                $types .= "s";
            }

            if (!empty($_POST['assigned_to']) && (int)$_POST['assigned_to'] > 0) {
                $whereClauses[] = "t.assigned_to = ?";
                $params[] = (int)$_POST['assigned_to'];
                $types .= "i";
            }

            $whereSql = implode(" AND ", $whereClauses);

            $sql = "SELECT t.*, p.name AS project_name, 
                           COALESCE(u.name, 'Unassigned') AS assignee_name 
                    FROM tbl_seo_tickets t 
                    LEFT JOIN tbl_seo_project p ON t.project_id = p.id 
                    LEFT JOIN tbl_seo_login u ON t.assigned_to = u.login_id 
                    WHERE $whereSql 
                    ORDER BY FIELD(t.priority, 'Urgent', 'High', 'Medium', 'Low'), t.id DESC";

            $stmt = $conn->prepare($sql);
            if (!empty($types)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $res = $stmt->get_result();

            $lanes = [
                'Open' => [],
                'In Progress' => [],
                'Under Review' => [],
                'Completed' => []
            ];

            while ($row = $res->fetch_assoc()) {
                $status = $row['status'];
                if ($status == 'Closed') $status = 'Completed';
                if (isset($lanes[$status])) {
                    $lanes[$status][] = $row;
                } else {
                    $lanes['Open'][] = $row;
                }
            }
            $stmt->close();

            echo json_encode([
                'status' => 'success',
                'lanes' => $lanes
            ]);
            exit();
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
            exit();
        }
    }

    // =========================================================================
    // 5. Get SEO Ticket Details & Submission Comments
    // =========================================================================
    if ($action == "get_ticket_details") {
        try {
            $ticketId = (int)$_POST['id'];
            $stmt = $conn->prepare("SELECT t.*, p.name AS project_name, p.url AS project_url,
                                           COALESCE(u.name, 'Unassigned') AS assignee_name, 
                                           COALESCE(r.name, 'Admin') AS reporter_name 
                                    FROM tbl_seo_tickets t 
                                    LEFT JOIN tbl_seo_project p ON t.project_id = p.id 
                                    LEFT JOIN tbl_seo_login u ON t.assigned_to = u.login_id 
                                    LEFT JOIN tbl_seo_login r ON t.created_by = r.login_id 
                                    WHERE t.id = ?");
            $stmt->bind_param("i", $ticketId);
            $stmt->execute();
            $ticket = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if (!$ticket) {
                echo json_encode(['status' => 'error', 'message' => 'Ticket not found']);
                exit();
            }

            // Get comments / proof links
            $comments = [];
            $stmtC = $conn->prepare("SELECT c.*, COALESCE(u.name, 'SEO Specialist') AS author_name 
                                     FROM tbl_seo_ticket_comments c 
                                     LEFT JOIN tbl_seo_login u ON c.user_id = u.login_id 
                                     WHERE c.ticket_id = ? 
                                     ORDER BY c.id ASC");
            $stmtC->bind_param("i", $ticketId);
            $stmtC->execute();
            $resC = $stmtC->get_result();
            while ($row = $resC->fetch_assoc()) {
                $comments[] = $row;
            }
            $stmtC->close();

            echo json_encode([
                'status' => 'success',
                'ticket' => $ticket,
                'comments' => $comments
            ]);
            exit();
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
            exit();
        }
    }

    // =========================================================================
    // 6. Save (Create or Update) SEO Ticket
    // =========================================================================
    if ($action == "save_ticket") {
        try {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $projectId = (int)$_POST['project_id'];
            $title = trim($_POST['title']);
            $category = $_POST['category'];
            $priority = $_POST['priority'];
            $status = $_POST['status'];
            $targetUrl = trim($_POST['target_url'] ?? '');
            $targetKeywords = trim($_POST['target_keywords'] ?? '');
            $requiredLinks = !empty($_POST['required_links_count']) ? (int)$_POST['required_links_count'] : 0;
            $completedLinks = !empty($_POST['completed_links_count']) ? (int)$_POST['completed_links_count'] : 0;
            $assignedTo = !empty($_POST['assigned_to']) ? (int)$_POST['assigned_to'] : null;
            $dueDate = !empty($_POST['due_date']) ? $_POST['due_date'] : null;
            $description = trim($_POST['description'] ?? '');
            $submissionProof = trim($_POST['submission_proof_links'] ?? '');
            $currentUserId = (int)($_SESSION['login_id'] ?? 1);

            if (empty($title) || $projectId <= 0) {
                echo json_encode(['status' => 'error', 'message' => 'Please select a Project and enter a Task Title.']);
                exit();
            }

            if ($id == 0) {
                // Generate next code e.g. SEO-1001
                $rMax = $conn->query("SELECT MAX(id) AS m FROM tbl_seo_tickets");
                $nextNum = 1001 + (int)$rMax->fetch_assoc()['m'];
                $ticketCode = "SEO-" . $nextNum;

                $stmt = $conn->prepare("INSERT INTO tbl_seo_tickets 
                    (ticket_code, project_id, title, category, priority, status, target_url, target_keywords, required_links_count, completed_links_count, assigned_to, created_by, due_date, description, submission_proof_links) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                
                $stmt->bind_param("sissssssiisisss", 
                    $ticketCode, $projectId, $title, $category, $priority, $status, 
                    $targetUrl, $targetKeywords, $requiredLinks, $completedLinks, 
                    $assignedTo, $currentUserId, $dueDate, $description, $submissionProof);
                
                $stmt->execute();
                $newId = $conn->insert_id;
                $stmt->close();

                // Initial comment log
                $initialMsg = "SEO Task initialized with priority **$priority** and target quota of **$requiredLinks** links.";
                $conn->query("INSERT INTO tbl_seo_ticket_comments (ticket_id, user_id, comment_type, comment) 
                              VALUES ($newId, $currentUserId, 'comment', '$initialMsg')");

                echo json_encode(['status' => 'success', 'message' => "Ticket $ticketCode created successfully!", 'id' => $newId, 'code' => $ticketCode]);
                exit();
            } else {
                $stmt = $conn->prepare("UPDATE tbl_seo_tickets SET 
                    project_id = ?, title = ?, category = ?, priority = ?, status = ?, 
                    target_url = ?, target_keywords = ?, required_links_count = ?, completed_links_count = ?, 
                    assigned_to = ?, due_date = ?, description = ?, submission_proof_links = ? 
                    WHERE id = ?");
                
                $stmt->bind_param("issssssiissssi", 
                    $projectId, $title, $category, $priority, $status, 
                    $targetUrl, $targetKeywords, $requiredLinks, $completedLinks, 
                    $assignedTo, $dueDate, $description, $submissionProof, $id);
                
                $stmt->execute();
                $stmt->close();

                echo json_encode(['status' => 'success', 'message' => "SEO ticket updated successfully!"]);
                exit();
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            exit();
        }
    }

    // =========================================================================
    // 7. Quick Status Update
    // =========================================================================
    if ($action == "change_status") {
        try {
            $ticketId = (int)$_POST['id'];
            $newStatus = trim($_POST['status']);
            $validStatuses = ['Open', 'In Progress', 'Under Review', 'Completed', 'Closed'];

            if (!in_array($newStatus, $validStatuses)) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid status']);
                exit();
            }

            $currentUserId = (int)($_SESSION['login_id'] ?? 1);

            $stmt = $conn->prepare("UPDATE tbl_seo_tickets SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $newStatus, $ticketId);
            $stmt->execute();
            $stmt->close();

            // Log activity comment
            $msg = "Workflow status updated to **$newStatus**.";
            $stmtC = $conn->prepare("INSERT INTO tbl_seo_ticket_comments (ticket_id, user_id, comment_type, comment) VALUES (?, ?, 'status_change', ?)");
            $stmtC->bind_param("iis", $ticketId, $currentUserId, $msg);
            $stmtC->execute();
            $stmtC->close();

            echo json_encode(['status' => 'success', 'message' => "Status changed to $newStatus"]);
            exit();
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            exit();
        }
    }

    // =========================================================================
    // 8. Add Proof Link Submission / Specialist Comment
    // =========================================================================
    if ($action == "add_comment") {
        try {
            $ticketId = (int)$_POST['ticket_id'];
            $comment = trim($_POST['comment'] ?? '');
            $linksAdded = isset($_POST['links_submitted_count']) ? (int)$_POST['links_submitted_count'] : 0;
            $currentUserId = (int)($_SESSION['login_id'] ?? 1);

            if (empty($comment)) {
                echo json_encode(['status' => 'error', 'message' => 'Please enter a comment or proof links.']);
                exit();
            }

            $commentType = ($linksAdded > 0) ? 'proof_submission' : 'comment';

            $stmt = $conn->prepare("INSERT INTO tbl_seo_ticket_comments (ticket_id, user_id, comment_type, comment, links_submitted_count) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iissi", $ticketId, $currentUserId, $commentType, $comment, $linksAdded);
            $stmt->execute();
            $stmt->close();

            if ($linksAdded > 0) {
                $conn->query("UPDATE tbl_seo_tickets SET completed_links_count = completed_links_count + $linksAdded WHERE id = $ticketId");
            }

            echo json_encode(['status' => 'success', 'message' => 'Update posted successfully!']);
            exit();
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            exit();
        }
    }
}
?>
