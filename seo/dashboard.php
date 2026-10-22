<?php
session_start();
if (!isset($_SESSION['name'])) {
    header("Location: ../index.php");
    exit();
}

require_once __DIR__ . '/../app_common/db_connect.php';

$is_admin = (!empty($_SESSION['login_id']) && (int)$_SESSION['login_id'] === 1);
$current_user_id = (int)($_SESSION['login_id'] ?? 0);
$user_display_name = $_SESSION['name'] ?? 'Specialist';

// KPI counters
$total_projects = 0;
$total_backlinks = 0;
$total_users = 0;
$total_categories = 0;

$emp_total_backlinks = 0;
$emp_month_backlinks = 0;
$emp_active_tasks = 0;
$emp_active_projects = 0;

$recent_backlinks = [];
$top_projects = [];
$top_categories = [];
$emp_assigned_tickets = [];

$chart_months = [];
$chart_labels = [];
$chart_data = [];

try {
    if ($is_admin) {
        // =====================================================================
        // Super Admin Statistics (Global System-Wide)
        // =====================================================================
        $res_p = app_exec_query("SELECT COUNT(*) AS c FROM tbl_seo_project");
        if ($res_p) $total_projects = (int)$res_p->fetch_assoc()['c'];

        $res_b = app_exec_query("SELECT COUNT(*) AS c FROM tbl_seo_backlinks");
        if ($res_b) $total_backlinks = (int)$res_b->fetch_assoc()['c'];

        $res_u = app_exec_query("SELECT COUNT(*) AS c FROM tbl_seo_login WHERE login_id != 1");
        if ($res_u) $total_users = (int)$res_u->fetch_assoc()['c'];

        $res_c = app_exec_query("SELECT COUNT(*) AS c FROM tbl_seo_content_type_master");
        if ($res_c) $total_categories = (int)$res_c->fetch_assoc()['c'];

        // Global recent 6 backlinks
        $sql_rec = "SELECT b.id, b.links, b.date_time, p.name AS proj_name, c.name AS cat_name, l.name AS user_name 
                    FROM tbl_seo_backlinks b 
                    LEFT JOIN tbl_seo_project p ON b.project_id=p.id 
                    LEFT JOIN tbl_seo_content_type_master c ON b.content=c.id 
                    LEFT JOIN tbl_seo_login l ON b.login_id=l.login_id 
                    ORDER BY b.id DESC LIMIT 6";
        $res_rec = app_exec_query($sql_rec);
        if ($res_rec && $res_rec->num_rows > 0) {
            while ($row = $res_rec->fetch_assoc()) {
                $recent_backlinks[] = $row;
            }
        }

        // Global top projects
        $sql_proj = "SELECT p.id, p.name, p.url, COUNT(b.id) AS backlink_count 
                     FROM tbl_seo_project p 
                     LEFT JOIN tbl_seo_backlinks b ON p.id = b.project_id 
                     GROUP BY p.id, p.name, p.url 
                     ORDER BY backlink_count DESC LIMIT 5";
        $res_proj = app_exec_query($sql_proj);
        if ($res_proj && $res_proj->num_rows > 0) {
            while ($row = $res_proj->fetch_assoc()) {
                $top_projects[] = $row;
            }
        }

        // Global top categories breakdown
        $sql_cats = "SELECT c.name, COUNT(b.id) AS total 
                     FROM tbl_seo_backlinks b 
                     JOIN tbl_seo_content_type_master c ON b.content=c.id 
                     GROUP BY c.id, c.name 
                     ORDER BY total DESC LIMIT 5";
        $res_cats = app_exec_query($sql_cats);
        if ($res_cats && $res_cats->num_rows > 0) {
            while ($row = $res_cats->fetch_assoc()) {
                $top_categories[] = $row;
            }
        }

    } else {
        // =====================================================================
        // Employee / Specialist Statistics (Personal Dashboard)
        // =====================================================================
        // 1. Total backlinks submitted by this employee
        $res_eb = app_exec_query("SELECT COUNT(*) AS c FROM tbl_seo_backlinks WHERE login_id = $current_user_id");
        if ($res_eb) $emp_total_backlinks = (int)$res_eb->fetch_assoc()['c'];

        // 2. Backlinks submitted in past 30 days
        $res_em = app_exec_query("SELECT COUNT(*) AS c FROM tbl_seo_backlinks WHERE login_id = $current_user_id AND (date_time >= DATE_SUB(NOW(), INTERVAL 30 DAY) OR (MONTH(date_time) = MONTH(CURRENT_DATE()) AND YEAR(date_time) = YEAR(CURRENT_DATE())))");
        if ($res_em) $emp_month_backlinks = (int)$res_em->fetch_assoc()['c'];

        // 3. Active assigned tickets for this employee
        $res_et = app_exec_query("SELECT COUNT(*) AS c FROM tbl_seo_tickets WHERE assigned_to = $current_user_id AND status NOT IN ('Completed', 'Closed')");
        if ($res_et) $emp_active_tasks = (int)$res_et->fetch_assoc()['c'];

        // 4. Projects this employee has submitted links for
        $res_ep = app_exec_query("SELECT COUNT(DISTINCT project_id) AS c FROM tbl_seo_backlinks WHERE login_id = $current_user_id");
        if ($res_ep) $emp_active_projects = (int)$res_ep->fetch_assoc()['c'];
        if ($emp_active_projects === 0) {
            $res_tp = app_exec_query("SELECT COUNT(*) AS c FROM tbl_seo_project");
            if ($res_tp) $emp_active_projects = (int)$res_tp->fetch_assoc()['c'];
        }

        // 5. Active tickets assigned to this employee
        $sql_tickets = "SELECT t.id, t.ticket_code, t.title, t.category, t.priority, t.status, t.due_date, 
                               t.required_links_count, t.completed_links_count, p.name AS proj_name 
                        FROM tbl_seo_tickets t 
                        LEFT JOIN tbl_seo_project p ON t.project_id = p.id 
                        WHERE t.assigned_to = $current_user_id AND t.status NOT IN ('Completed', 'Closed') 
                        ORDER BY FIELD(t.priority, 'Urgent', 'High', 'Medium', 'Low'), t.due_date ASC 
                        LIMIT 6";
        $res_tickets = app_exec_query($sql_tickets);
        if ($res_tickets && $res_tickets->num_rows > 0) {
            while ($row = $res_tickets->fetch_assoc()) {
                $emp_assigned_tickets[] = $row;
            }
        }

        // 6. Recent backlinks submitted by this employee
        $sql_rec = "SELECT b.id, b.links, b.date_time, p.name AS proj_name, c.name AS cat_name 
                    FROM tbl_seo_backlinks b 
                    LEFT JOIN tbl_seo_project p ON b.project_id=p.id 
                    LEFT JOIN tbl_seo_content_type_master c ON b.content=c.id 
                    WHERE b.login_id = $current_user_id 
                    ORDER BY b.id DESC LIMIT 6";
        $res_rec = app_exec_query($sql_rec);
        if ($res_rec && $res_rec->num_rows > 0) {
            while ($row = $res_rec->fetch_assoc()) {
                $recent_backlinks[] = $row;
            }
        }

        // 7. Categories breakdown for this employee
        $sql_cats = "SELECT c.name, COUNT(b.id) AS total 
                     FROM tbl_seo_backlinks b 
                     JOIN tbl_seo_content_type_master c ON b.content=c.id 
                     WHERE b.login_id = $current_user_id 
                     GROUP BY c.id, c.name 
                     ORDER BY total DESC LIMIT 5";
        $res_cats = app_exec_query($sql_cats);
        if ($res_cats && $res_cats->num_rows > 0) {
            while ($row = $res_cats->fetch_assoc()) {
                $top_categories[] = $row;
            }
        }
        if (empty($top_categories)) {
            $sql_fallback_cats = "SELECT c.name, COUNT(b.id) AS total 
                                  FROM tbl_seo_backlinks b 
                                  JOIN tbl_seo_content_type_master c ON b.content=c.id 
                                  GROUP BY c.id, c.name 
                                  ORDER BY total DESC LIMIT 5";
            $res_fb = app_exec_query($sql_fallback_cats);
            if ($res_fb) {
                while ($row = $res_fb->fetch_assoc()) {
                    $top_categories[] = $row;
                }
            }
        }

        // 8. Top projects contributed by this employee
        $sql_proj = "SELECT p.id, p.name, p.url, COUNT(b.id) AS backlink_count 
                     FROM tbl_seo_project p 
                     JOIN tbl_seo_backlinks b ON p.id = b.project_id 
                     WHERE b.login_id = $current_user_id 
                     GROUP BY p.id, p.name, p.url 
                     ORDER BY backlink_count DESC LIMIT 5";
        $res_proj = app_exec_query($sql_proj);
        if ($res_proj && $res_proj->num_rows > 0) {
            while ($row = $res_proj->fetch_assoc()) {
                $top_projects[] = $row;
            }
        }
    }

    // Chart Months: Last 8 active campaign months in the system
    $res_m = app_exec_query("SELECT DISTINCT DATE_FORMAT(date_time, '%Y-%m') as ym 
                             FROM tbl_seo_backlinks 
                             WHERE date_time > '2023-01-01' 
                             ORDER BY ym DESC LIMIT 8");
    if ($res_m && $res_m->num_rows > 0) {
        while ($rm = $res_m->fetch_assoc()) {
            $chart_months[] = $rm['ym'];
        }
        $chart_months = array_reverse($chart_months);
    }
    if (empty($chart_months)) {
        for ($i = 5; $i >= 0; $i--) {
            $chart_months[] = date('Y-m', strtotime("-$i months"));
        }
    }

    foreach ($chart_months as $ym) {
        $chart_labels[] = date('M Y', strtotime($ym . '-01'));
        if ($is_admin) {
            $sql_c = "SELECT COUNT(*) AS c FROM tbl_seo_backlinks WHERE DATE_FORMAT(date_time, '%Y-%m') = '$ym'";
        } else {
            $sql_c = "SELECT COUNT(*) AS c FROM tbl_seo_backlinks WHERE login_id = $current_user_id AND DATE_FORMAT(date_time, '%Y-%m') = '$ym'";
        }
        $rc = app_exec_query($sql_c);
        $chart_data[] = ($rc && $rrow = $rc->fetch_assoc()) ? (int)$rrow['c'] : 0;
    }

} catch (Exception $e) {
    // Graceful fallback
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Falcon | <?php echo $is_admin ? 'Executive Dashboard' : 'Employee Dashboard'; ?> - Finez SEO</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap" rel="stylesheet">

    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="../css/animate.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
    <link href="../css/modern-theme.css?v=3.7" rel="stylesheet">
    <script src="../app_js/finez-loader.js"></script>

    <script src="../js/jquery-3.6.0.min.js"></script>
    <script src="../js/plugins/chartJs/Chart.min.js"></script>
</head>

<body>
    <?php include __DIR__ . '/../app_common/loader.php'; ?>
    <div id="wrapper">

        <!-- Falcon Vertical Navbar (Sidebar) -->
        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="brand-header">
                <div class="brand-logo-icon">
                    <i class="fa fa-line-chart"></i>
                </div>
                <div>
                    <div class="brand-text-title">Finez SEO</div>
                    <div class="brand-text-subtitle"><span class="brand-status-dot"></span> <?php echo $is_admin ? 'PLATFORM CONSOLE' : 'EMPLOYEE WORKSPACE'; ?></div>
                </div>
            </div>

            <div class="sidebar-section-label"><?php echo $is_admin ? 'SUPER ADMIN CONSOLE' : 'EMPLOYEE WORKSPACE'; ?></div>

            <div class="sidebar-collapse" id="divMenuContainer">
                <!-- menu injected via ajax / session cache -->
            </div>
        </nav>
        <!-- Falcon Vertical Navbar End -->

        <div id="page-wrapper" class="gray-bg">
            <!-- Top Header Start (Clean Falcon Layout) -->
            <div class="falcon-topbar">
                <div style="display: flex; align-items: center; gap: 14px;">
                    <a class="navbar-minimalize minimalize-styl-2" href="#"><i class="fa fa-bars"></i> </a>
                    <span style="font-size: 14px; font-weight: 700; color: var(--falcon-gray-700);">
                        <i class="fa fa-th-large" style="color: var(--falcon-primary); margin-right: 8px;"></i> 
                        <?php echo $is_admin ? 'CRM Executive Overview' : 'Specialist Performance Dashboard'; ?>
                    </span>
                </div>

                <div class="topbar-right-section">
                    <span class="live-status-pill hidden-xs">
                        <span class="pulse-dot"></span> 
                        <?php echo $is_admin ? 'In Sync &bull; Live' : 'Specialist &bull; Online'; ?>
                    </span>
                    <div class="topbar-user-badge">
                        <img src="../img/customer.png" class="topbar-avatar" alt="Avatar">
                        <span class="topbar-username"><?php echo htmlspecialchars($user_display_name); ?></span>
                    </div>
                    <a href="../app_login_manager/logout.php" class="btn-falcon-logout">
                        <i class="fa fa-sign-out"></i>
                        <span>Log out</span>
                    </a>
                </div>
            </div>
            <!-- Top Header End -->

            <!-- Falcon CRM Page Heading Start -->
            <div class="row wrapper page-heading" style="margin: 0 0 20px 0;">
                <div class="col-sm-7">
                    <?php if ($is_admin): ?>
                        <span class="page-title-badge"><i class="fa fa-tachometer"></i> Executive Overview</span>
                        <h2>Welcome back, <?php echo htmlspecialchars($user_display_name); ?>!</h2>
                        <p class="page-heading-desc">Real-time performance overview across all active SEO domains and backlink campaigns</p>
                    <?php else: ?>
                        <span class="page-title-badge"><i class="fa fa-user-circle"></i> Employee Specialist Workspace</span>
                        <h2>Welcome back, <?php echo htmlspecialchars($user_display_name); ?>!</h2>
                        <p class="page-heading-desc">Your daily link building production, assigned SEO tickets, and campaign milestones</p>
                    <?php endif; ?>
                </div>

                <div class="col-sm-5" style="display: flex; align-items: center; justify-content: flex-end; gap: 12px; margin-top: 10px; flex-wrap: wrap;">
                    <?php if ($is_admin): ?>
                        <a href="projects.php" class="btn btn-primary" style="height: 40px; font-size: 13px; font-weight: 700; padding: 0 16px; display: inline-flex; align-items: center; gap: 6px;">
                            <i class="fa fa-plus"></i> Add Project
                        </a>
                        <a href="backlink_list.php" class="btn btn-white" style="height: 40px; font-size: 13px; font-weight: 600; padding: 0 14px; display: inline-flex; align-items: center; gap: 6px;">
                            <i class="fa fa-history"></i> Audit Trail
                        </a>
                    <?php else: ?>
                        <a href="backlinks.php" class="btn btn-primary" style="height: 40px; font-size: 13px; font-weight: 700; padding: 0 16px; display: inline-flex; align-items: center; gap: 6px;">
                            <i class="fa fa-plus"></i> Submit Backlink
                        </a>
                        <a href="tickets.php" class="btn btn-white" style="height: 40px; font-size: 13px; font-weight: 600; padding: 0 14px; display: inline-flex; align-items: center; gap: 6px;">
                            <i class="fa fa-ticket"></i> My Tasks
                        </a>
                        <a href="backlink_list.php" class="btn btn-white" style="height: 40px; font-size: 13px; font-weight: 600; padding: 0 14px; display: inline-flex; align-items: center; gap: 6px;">
                            <i class="fa fa-history"></i> My History
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <!-- Page Heading End -->

            <!-- Falcon CRM 4-Card KPI Row -->
            <div class="wrapper wrapper-content" style="padding-bottom: 0 !important; padding-top: 0 !important;">
                <div class="row crm-kpi-row">
                    <?php if ($is_admin): ?>
                        <!-- KPI 1: Active Projects -->
                        <div class="col-sm-6 col-md-3">
                            <div class="crm-kpi-card">
                                <div class="crm-kpi-info">
                                    <span class="crm-kpi-label">Active Projects</span>
                                    <span class="crm-kpi-value"><?php echo number_format($total_projects); ?></span>
                                    <span class="crm-kpi-subtext"><span style="color: var(--falcon-green); font-weight: 700;"><i class="fa fa-arrow-up"></i> 100%</span> active</span>
                                </div>
                                <div class="crm-kpi-icon primary">
                                    <i class="fa fa-folder-open"></i>
                                </div>
                            </div>
                        </div>

                        <!-- KPI 2: Total Backlinks -->
                        <div class="col-sm-6 col-md-3">
                            <div class="crm-kpi-card">
                                <div class="crm-kpi-info">
                                    <span class="crm-kpi-label">Total Backlinks</span>
                                    <span class="crm-kpi-value" style="color: var(--falcon-primary);"><?php echo number_format($total_backlinks); ?></span>
                                    <span class="crm-kpi-subtext"><span style="color: var(--falcon-green); font-weight: 700;"><i class="fa fa-link"></i> Live</span> submissions</span>
                                </div>
                                <div class="crm-kpi-icon success">
                                    <i class="fa fa-link"></i>
                                </div>
                            </div>
                        </div>

                        <!-- KPI 3: Total Employees -->
                        <div class="col-sm-6 col-md-3">
                            <div class="crm-kpi-card">
                                <div class="crm-kpi-info">
                                    <span class="crm-kpi-label">Total Employees</span>
                                    <span class="crm-kpi-value"><?php echo number_format($total_users); ?></span>
                                    <span class="crm-kpi-subtext"><span style="color: var(--falcon-cyan); font-weight: 700;"><i class="fa fa-users"></i> Employee</span> directory</span>
                                </div>
                                <div class="crm-kpi-icon info">
                                    <i class="fa fa-users"></i>
                                </div>
                            </div>
                        </div>

                        <!-- KPI 4: Categories -->
                        <div class="col-sm-6 col-md-3">
                            <div class="crm-kpi-card">
                                <div class="crm-kpi-info">
                                    <span class="crm-kpi-label">Categories</span>
                                    <span class="crm-kpi-value"><?php echo number_format($total_categories); ?></span>
                                    <span class="crm-kpi-subtext"><span style="color: var(--falcon-warning); font-weight: 700;"><i class="fa fa-tags"></i> Platform</span> types</span>
                                </div>
                                <div class="crm-kpi-icon warning">
                                    <i class="fa fa-tags"></i>
                                </div>
                            </div>
                        </div>

                    <?php else: ?>
                        <!-- EMPLOYEE KPI 1: My Total Backlinks -->
                        <div class="col-sm-6 col-md-3">
                            <div class="crm-kpi-card">
                                <div class="crm-kpi-info">
                                    <span class="crm-kpi-label">My Backlinks</span>
                                    <span class="crm-kpi-value" style="color: var(--falcon-primary);"><?php echo number_format($emp_total_backlinks); ?></span>
                                    <span class="crm-kpi-subtext"><span style="color: var(--falcon-green); font-weight: 700;"><i class="fa fa-check-circle"></i> Lifetime</span> published</span>
                                </div>
                                <div class="crm-kpi-icon primary">
                                    <i class="fa fa-link"></i>
                                </div>
                            </div>
                        </div>

                        <!-- EMPLOYEE KPI 2: Past 30 Days Output -->
                        <div class="col-sm-6 col-md-3">
                            <div class="crm-kpi-card">
                                <div class="crm-kpi-info">
                                    <span class="crm-kpi-label">Recent Output (30 Days)</span>
                                    <span class="crm-kpi-value" style="color: var(--falcon-gray-1000);"><?php echo number_format($emp_month_backlinks); ?></span>
                                    <span class="crm-kpi-subtext"><span style="color: var(--falcon-primary); font-weight: 700;"><i class="fa fa-calendar-check-o"></i> Active</span> velocity</span>
                                </div>
                                <div class="crm-kpi-icon success">
                                    <i class="fa fa-line-chart"></i>
                                </div>
                            </div>
                        </div>

                        <!-- EMPLOYEE KPI 3: My Assigned Tasks -->
                        <div class="col-sm-6 col-md-3">
                            <div class="crm-kpi-card">
                                <div class="crm-kpi-info">
                                    <span class="crm-kpi-label">My Assigned Tasks</span>
                                    <span class="crm-kpi-value" style="color: <?php echo $emp_active_tasks > 0 ? 'var(--falcon-warning)' : 'var(--falcon-gray-900)'; ?>;">
                                        <?php echo number_format($emp_active_tasks); ?>
                                    </span>
                                    <span class="crm-kpi-subtext"><span style="color: var(--falcon-warning); font-weight: 700;"><i class="fa fa-clock-o"></i> Pending</span> completion</span>
                                </div>
                                <div class="crm-kpi-icon warning">
                                    <i class="fa fa-ticket"></i>
                                </div>
                            </div>
                        </div>

                        <!-- EMPLOYEE KPI 4: Active Campaigns -->
                        <div class="col-sm-6 col-md-3">
                            <div class="crm-kpi-card">
                                <div class="crm-kpi-info">
                                    <span class="crm-kpi-label">Active Campaigns</span>
                                    <span class="crm-kpi-value"><?php echo number_format($emp_active_projects); ?></span>
                                    <span class="crm-kpi-subtext"><span style="color: var(--falcon-info); font-weight: 700;"><i class="fa fa-folder-open"></i> Projects</span> in progress</span>
                                </div>
                                <div class="crm-kpi-icon info">
                                    <i class="fa fa-globe"></i>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Falcon CRM Analytics & Breakdown Row -->
                <div class="row">
                    <!-- Left: Publication Velocity Chart -->
                    <div class="col-lg-8">
                        <div class="ibox">
                            <div class="ibox-title">
                                <h5>
                                    <i class="fa fa-area-chart" style="color: var(--falcon-primary); margin-right: 8px;"></i> 
                                    <?php echo $is_admin ? 'Campaign Link Velocity &amp; Monthly Activity' : 'My Link Building Velocity (Monthly Submissions)'; ?>
                                </h5>
                                <div><span class="badge-modern badge-subtle-primary"><i class="fa fa-bolt"></i> <?php echo $is_admin ? 'Global Volume' : 'Personal Performance'; ?></span></div>
                            </div>
                            <div class="ibox-content" style="padding: 20px 24px !important;">
                                <div style="height: 240px; position: relative;">
                                    <canvas id="velocityChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Content Type Breakdown -->
                    <div class="col-lg-4">
                        <div class="ibox">
                            <div class="ibox-title">
                                <h5>
                                    <i class="fa fa-pie-chart" style="color: var(--falcon-primary); margin-right: 8px;"></i> 
                                    <?php echo $is_admin ? 'Platform Channels (All Projects)' : 'My Content Types &amp; Channels'; ?>
                                </h5>
                            </div>
                            <div class="ibox-content" style="padding: 16px 20px !important;">
                                <?php if (!empty($top_categories)): ?>
                                    <?php 
                                    $cat_colors = ['#00a862', '#02a8b5', '#727cf5', '#f5803e', '#27bcfd'];
                                    $max_val = max(1, $top_categories[0]['total']);
                                    foreach ($top_categories as $idx => $cat): 
                                        $percent = round(($cat['total'] / $max_val) * 100);
                                        $color = $cat_colors[$idx % count($cat_colors)];
                                    ?>
                                        <div style="margin-bottom: 14px;">
                                            <div style="display: flex; justify-content: space-between; font-size: 13px; font-weight: 700; margin-bottom: 6px;">
                                                <span style="color: var(--falcon-gray-900);"><?php echo htmlspecialchars($cat['name']); ?></span>
                                                <span style="color: var(--falcon-gray-700);"><?php echo number_format($cat['total']); ?> links</span>
                                            </div>
                                            <div style="height: 8px; background: var(--falcon-gray-200); border-radius: 6px; overflow: hidden;">
                                                <div style="width: <?php echo $percent; ?>%; height: 100%; background: <?php echo $color; ?>; border-radius: 6px;"></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p style="color: var(--falcon-gray-600); text-align: center; padding: 24px 0; font-size: 13px;">No category submissions yet</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Falcon CRM Bottom Tables Row -->
                <div class="row">
                    <?php if ($is_admin): ?>
                        <!-- ADMIN VIEW: Recent Global Backlinks -->
                        <div class="col-lg-8">
                            <div class="ibox">
                                <div class="ibox-title">
                                    <h5><i class="fa fa-history" style="color: var(--falcon-primary); margin-right: 8px;"></i> Recent Backlink Submissions</h5>
                                    <a href="backlink_list.php" style="font-size: 13px; font-weight: 700;">View All &rarr;</a>
                                </div>
                                <div class="ibox-content">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Date &amp; Time</th>
                                                    <th>Target URL</th>
                                                    <th>Category</th>
                                                    <th>Employee</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($recent_backlinks)): ?>
                                                    <?php foreach ($recent_backlinks as $b): 
                                                        $displayLink = (strpos($b['links'], 'http') === 0) ? $b['links'] : 'https://' . $b['links'];
                                                    ?>
                                                        <tr>
                                                            <td><span class="badge-modern badge-modern-slate"><i class="fa fa-clock-o"></i> <?php echo htmlspecialchars($b['date_time']); ?></span></td>
                                                            <td><a href="<?php echo htmlspecialchars($displayLink); ?>" target="_blank" class="url-tag" title="<?php echo htmlspecialchars($b['links']); ?>"><i class="fa fa-external-link"></i> <?php echo htmlspecialchars($b['links']); ?></a></td>
                                                            <td><span class="badge-modern badge-subtle-primary"><?php echo htmlspecialchars($b['cat_name'] ?? 'General'); ?></span></td>
                                                            <td><span class="badge-modern badge-subtle-success"><i class="fa fa-user-circle"></i> <?php echo htmlspecialchars($b['user_name'] ?? 'Team'); ?></span></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr><td colspan="4" style="text-align: center; padding: 24px; color: var(--falcon-gray-600); font-size: 15px;">No backlink entries yet</td></tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ADMIN VIEW: Top Company Projects -->
                        <div class="col-lg-4">
                            <div class="ibox">
                                <div class="ibox-title">
                                    <h5><i class="fa fa-globe" style="color: var(--falcon-primary); margin-right: 8px;"></i> Top Active Campaigns</h5>
                                    <a href="projects.php" style="font-size: 14.5px; font-weight: 700;">All Projects &rarr;</a>
                                </div>
                                <div class="ibox-content">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Project Name</th>
                                                    <th style="text-align: right;">Backlinks</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($top_projects)): ?>
                                                    <?php foreach ($top_projects as $p): ?>
                                                        <tr>
                                                            <td>
                                                                <strong style="color: var(--falcon-gray-1100);"><?php echo htmlspecialchars($p['name']); ?></strong>
                                                            </td>
                                                            <td style="text-align: right;">
                                                                <span class="badge-modern badge-subtle-primary"><?php echo number_format($p['backlink_count']); ?> links</span>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr><td colspan="2" style="text-align: center; padding: 24px; color: var(--falcon-gray-600);">No projects available</td></tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php else: ?>
                        <!-- EMPLOYEE VIEW: My Assigned Tasks & Tickets -->
                        <div class="col-lg-8">
                            <div class="ibox">
                                <div class="ibox-title">
                                    <h5><i class="fa fa-ticket" style="color: var(--falcon-primary); margin-right: 8px;"></i> My Assigned SEO Tasks &amp; Tickets</h5>
                                    <a href="tickets.php" style="font-size: 13px; font-weight: 700;">Tickets Board &rarr;</a>
                                </div>
                                <div class="ibox-content">
                                    <?php if (!empty($emp_assigned_tickets)): ?>
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>Code</th>
                                                        <th>Project &amp; Task Title</th>
                                                        <th>Priority</th>
                                                        <th>Quota Progress</th>
                                                        <th>Due Date</th>
                                                        <th style="text-align: right;">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($emp_assigned_tickets as $t): 
                                                        $req = max(1, (int)$t['required_links_count']);
                                                        $comp = (int)$t['completed_links_count'];
                                                        $pct = min(100, round(($comp / $req) * 100));
                                                        $prio = strtolower($t['priority'] ?? 'medium');
                                                    ?>
                                                        <tr>
                                                            <td>
                                                                <span class="badge-modern badge-modern-slate" style="font-family: monospace; font-weight: 700;">
                                                                    <?php echo htmlspecialchars($t['ticket_code']); ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <div style="font-weight: 700; color: var(--falcon-gray-1000); font-size: 13px;">
                                                                    <?php echo htmlspecialchars($t['title']); ?>
                                                                </div>
                                                                <div style="font-size: 11.5px; color: var(--falcon-gray-500); margin-top: 2px;">
                                                                    <i class="fa fa-folder-o"></i> <?php echo htmlspecialchars($t['proj_name'] ?? 'General Campaign'); ?> &bull; <span class="text-muted"><?php echo htmlspecialchars($t['category']); ?></span>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <span class="badge-prio-<?php echo $prio; ?>">
                                                                    <?php echo htmlspecialchars($t['priority']); ?>
                                                                </span>
                                                            </td>
                                                            <td style="min-width: 110px;">
                                                                <div style="display: flex; justify-content: space-between; font-size: 11px; font-weight: 700; margin-bottom: 3px;">
                                                                    <span><?php echo $comp; ?>/<?php echo $req; ?> Links</span>
                                                                    <span><?php echo $pct; ?>%</span>
                                                                </div>
                                                                <div style="height: 6px; background: var(--falcon-gray-200); border-radius: 4px; overflow: hidden;">
                                                                    <div style="width: <?php echo $pct; ?>%; height: 100%; background: var(--falcon-primary); border-radius: 4px;"></div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div style="font-size: 12px; color: var(--falcon-gray-800); font-weight: 600;">
                                                                    <i class="fa fa-calendar-o text-muted"></i> <?php echo htmlspecialchars($t['due_date'] ?? 'No date'); ?>
                                                                </div>
                                                            </td>
                                                            <td style="text-align: right;">
                                                                <a href="tickets.php" class="btn btn-xs btn-primary" style="font-weight: 700; padding: 4px 10px; border-radius: 6px;">
                                                                    Work &rarr;
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <div style="padding: 36px 20px; text-align: center; color: var(--falcon-gray-600);">
                                            <div style="width: 48px; height: 48px; border-radius: 50%; background: #e8f7f0; color: #00a862; display: inline-flex; align-items: center; justify-content: center; font-size: 22px; margin-bottom: 12px;">
                                                <i class="fa fa-check"></i>
                                            </div>
                                            <h4 style="font-size: 15px; font-weight: 800; color: var(--falcon-gray-1000); margin: 0 0 6px 0;">All caught up!</h4>
                                            <p style="font-size: 13px; color: var(--falcon-gray-600); max-width: 420px; margin: 0 auto 16px auto;">
                                                You have no pending tasks assigned at the moment. You can view all project tickets or submit new backlinks.
                                            </p>
                                            <div style="display: flex; gap: 8px; justify-content: center;">
                                                <a href="tickets.php" class="btn btn-primary btn-sm" style="font-weight: 700; border-radius: 6px;">
                                                    <i class="fa fa-ticket"></i> View All Tickets
                                                </a>
                                                <a href="backlinks.php" class="btn btn-white btn-sm" style="font-weight: 600; border-radius: 6px;">
                                                    <i class="fa fa-plus"></i> Submit Backlink
                                                </a>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- EMPLOYEE VIEW: My Recent Submissions -->
                        <div class="col-lg-4">
                            <div class="ibox">
                                <div class="ibox-title">
                                    <h5><i class="fa fa-history" style="color: var(--falcon-primary); margin-right: 8px;"></i> My Recent Submissions</h5>
                                    <a href="backlink_list.php" style="font-size: 13px; font-weight: 700;">Full Log &rarr;</a>
                                </div>
                                <div class="ibox-content" style="padding: 12px 16px !important;">
                                    <?php if (!empty($recent_backlinks)): ?>
                                        <div style="display: flex; flex-direction: column; gap: 10px;">
                                            <?php foreach ($recent_backlinks as $b): 
                                                $displayLink = (strpos($b['links'], 'http') === 0) ? $b['links'] : 'https://' . $b['links'];
                                            ?>
                                                <div style="padding: 10px 12px; border: 1px solid var(--falcon-border-color); border-radius: 8px; background: #ffffff;">
                                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                                                        <span class="badge-modern badge-subtle-primary" style="font-size: 10.5px !important;">
                                                            <?php echo htmlspecialchars($b['cat_name'] ?? 'General'); ?>
                                                        </span>
                                                        <span style="font-size: 11px; color: var(--falcon-gray-500);">
                                                            <i class="fa fa-clock-o"></i> <?php echo date('d M, H:i', strtotime($b['date_time'])); ?>
                                                        </span>
                                                    </div>
                                                    <div style="font-size: 12.5px; font-weight: 600; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                        <a href="<?php echo htmlspecialchars($displayLink); ?>" target="_blank" class="url-tag" style="padding: 0; background: none; border: none; font-size: 12.5px;" title="<?php echo htmlspecialchars($b['links']); ?>">
                                                            <i class="fa fa-external-link text-muted"></i> <?php echo htmlspecialchars($b['links']); ?>
                                                        </a>
                                                    </div>
                                                    <div style="font-size: 11px; color: var(--falcon-gray-600); margin-top: 3px;">
                                                        <i class="fa fa-folder-o text-muted"></i> Project: <strong style="color: var(--falcon-gray-800);"><?php echo htmlspecialchars($b['proj_name'] ?? 'Campaign'); ?></strong>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <div style="text-align: center; padding: 28px 10px; color: var(--falcon-gray-600); font-size: 13px;">
                                            <i class="fa fa-link" style="font-size: 28px; color: var(--falcon-gray-400); margin-bottom: 8px; display: block;"></i>
                                            No backlinks submitted yet.<br>
                                            <a href="backlinks.php" class="btn btn-primary btn-xs" style="margin-top: 10px; font-weight: 700;">Submit Your First Link</a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
            <!-- Wrapper Content End -->
        </div>
        <!-- Page Wrapper End -->

    </div>

    <!-- Scripts -->
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="../js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
    <script src="../js/inspinia.js"></script>
    <script src="../app_menu/menu.js?v=4.2"></script>

    <script>
        $(document).ready(function() {
            loadMenu();

            // Render Falcon Velocity Chart
            try {
                var ctx = document.getElementById("velocityChart").getContext("2d");
                var gradient = ctx.createLinearGradient(0, 0, 0, 200);
                gradient.addColorStop(0, "rgba(0, 168, 98, 0.35)");
                gradient.addColorStop(1, "rgba(0, 168, 98, 0.0)");

                var chartLabels = <?php echo json_encode($chart_labels); ?>;
                var chartDataValues = <?php echo json_encode($chart_data); ?>;

                var chartData = {
                    labels: chartLabels,
                    datasets: [{
                        label: "<?php echo $is_admin ? 'Backlinks Published' : 'My Backlinks'; ?>",
                        fillColor: gradient,
                        strokeColor: "#00a862",
                        pointColor: "#00a862",
                        pointStrokeColor: "#ffffff",
                        pointHighlightFill: "#ffffff",
                        pointHighlightStroke: "#00a862",
                        data: chartDataValues
                    }]
                };

                var chartOptions = {
                    scaleShowGridLines: true,
                    scaleGridLineColor: "rgba(216, 226, 239, 0.6)",
                    scaleGridLineWidth: 1,
                    bezierCurve: true,
                    bezierCurveTension: 0.3,
                    pointDot: true,
                    pointDotRadius: 4,
                    pointDotStrokeWidth: 2,
                    datasetStroke: true,
                    datasetStrokeWidth: 2.5,
                    datasetFill: true,
                    responsive: true,
                    maintainAspectRatio: false
                };

                new Chart(ctx).Line(chartData, chartOptions);
            } catch(e) {
                console.log("Chart rendering error: ", e);
            }
        });
    </script>
</body>

</html>
