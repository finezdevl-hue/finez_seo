<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['name'])) {
    header("Location: ../");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow, noarchive">
    <title>Falcon | SEO Tickets - Finez SEO</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap" rel="stylesheet">

    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="../assets/css/animate.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="../assets/css/modern-theme.css?v=3.6" rel="stylesheet">
    <script src="../assets/js/finez-loader.js"></script>

    <script src="../assets/js/jquery-3.6.0.min.js"></script>
    <link href="../assets/css/plugins/sweetalert/sweetalert.css" rel="stylesheet">
    <script src="../assets/js/plugins/sweetalert/sweetalert.min.js"></script>
    <script src="../assets/js/sweetalert-finez.js"></script>
    <script src="../assets/js/pagination.js"></script>

    <style>
        /* View Visibility */
        .kanban-view-hidden {
            display: none !important;
        }

        /* Page Heading Layout */
        .page-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            margin: 0 0 20px 0 !important;
        }
        .page-heading-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 4px;
        }

        /* Luxury View Switcher (Segmented Control Pill) */
        .falcon-view-segmented {
            background: #eef5f1;
            border: 1px solid #cce4d8;
            border-radius: 9px;
            padding: 3px;
            display: inline-flex;
            align-items: center;
            gap: 3px;
            box-shadow: inset 0 1px 2px rgba(10, 30, 20, 0.04);
        }
        .falcon-view-btn {
            border: none;
            background: transparent;
            color: #496357;
            font-size: 12.5px;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 7px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.18s cubic-bezier(0.16, 1, 0.3, 1);
            outline: none !important;
        }
        .falcon-view-btn:hover {
            color: #15221d;
            background: rgba(255, 255, 255, 0.65);
        }
        .falcon-view-btn.active {
            background: #ffffff;
            color: #007745;
            font-weight: 700;
            box-shadow: 0 2px 6px rgba(0, 168, 98, 0.16), 0 1px 2px rgba(0, 0, 0, 0.04);
        }
        .falcon-view-btn i {
            font-size: 13px;
        }

        /* New Ticket Button */
        .btn-new-ticket {
            height: 38px;
            font-size: 13px;
            font-weight: 700;
            padding: 0 16px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
            border-radius: var(--falcon-border-radius);
            background: var(--falcon-primary-gradient);
            border: none;
            box-shadow: 0 2px 6px rgba(0, 168, 98, 0.25);
            color: #ffffff;
            transition: var(--transition);
        }
        .btn-new-ticket:hover {
            box-shadow: 0 4px 12px rgba(0, 168, 98, 0.35);
            transform: translateY(-1px);
            color: #ffffff;
        }

        /* Operations Toolbar Card */
        .falcon-operations-card {
            background: #ffffff;
            border: 1px solid var(--falcon-border-color);
            border-radius: var(--falcon-border-radius);
            padding: 12px 18px;
            box-shadow: var(--falcon-card-shadow);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 14px;
            margin-bottom: 20px;
        }
        .falcon-filters-group {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }
        .falcon-filter-label {
            font-size: 12.5px;
            font-weight: 800;
            color: var(--falcon-gray-800);
            display: flex;
            align-items: center;
            gap: 6px;
            margin-right: 4px;
        }
        .falcon-filter-label i {
            color: var(--falcon-primary);
        }

        /* Modern Falcon Select Dropdowns */
        .falcon-custom-select {
            height: 38px;
            border: 1px solid #d4e2dc;
            border-radius: 8px;
            background-color: #ffffff;
            padding: 0 28px 0 11px;
            font-size: 12px;
            font-weight: 600;
            color: #25362f;
            outline: none;
            transition: var(--transition);
            cursor: pointer;
            background-image: url("data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23546e63%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E");
            background-repeat: no-repeat;
            background-position: right 9px center;
            background-size: 9px auto;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }
        .falcon-custom-select:hover {
            border-color: #a9c0b6;
            background-color: #fafcfb;
        }
        .falcon-custom-select:focus {
            border-color: var(--falcon-primary);
            box-shadow: 0 0 0 3px rgba(0, 168, 98, 0.12);
            background-color: #ffffff;
        }

        /* Reset Button */
        .btn-falcon-reset {
            height: 38px;
            padding: 0 14px;
            border-radius: 8px;
            border: 1px solid #d4e2dc;
            background: #ffffff;
            color: var(--falcon-gray-700);
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: var(--transition);
        }
        .btn-falcon-reset:hover {
            background: #f4f8f6;
            border-color: #a9c0b6;
            color: var(--falcon-primary);
        }

        /* Search Wrapper */
        .falcon-search-wrapper {
            display: flex;
            align-items: center;
        }
        .falcon-search-wrapper .modern-search-group {
            max-width: 270px;
        }

        /* Badges */
        .badge-modern {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 9px;
            border-radius: 6px;
            font-size: 11.5px;
            font-weight: 700;
            letter-spacing: -0.01em;
            line-height: 1.35;
            border: 1px solid transparent;
        }
        /* Categories */
        .badge-cat-link-building {
            background: #e8f7f0;
            border-color: #c3ebd7;
            color: #007745;
        }
        .badge-cat-broken-link {
            background: #fef2f2;
            border-color: #fecaca;
            color: #b91c1c;
        }
        .badge-cat-technical {
            background: #f0fdf4;
            border-color: #bbf7d0;
            color: #166534;
        }
        .badge-cat-keyword {
            background: #faf5ff;
            border-color: #e9d5ff;
            color: #6b21a8;
        }
        .badge-cat-content {
            background: #eff6ff;
            border-color: #bfdbfe;
            color: #1e40af;
        }
        .badge-cat-audit {
            background: #fffbeb;
            border-color: #fde68a;
            color: #92400e;
        }
        .badge-cat-client {
            background: #fdf4ff;
            border-color: #f5d0fe;
            color: #86198f;
        }

        /* Priorities */
        .badge-prio-urgent {
            background: #fef2f2;
            border-color: #fecaca;
            color: #b91c1c;
        }
        .badge-prio-high {
            background: #fff7ed;
            border-color: #fed7aa;
            color: #c2410c;
        }
        .badge-prio-medium {
            background: #f0f9ff;
            border-color: #bae6fd;
            color: #0369a1;
        }
        .badge-prio-low {
            background: #f8fafc;
            border-color: #e2e8f0;
            color: #475569;
        }

        /* Statuses */
        .badge-status-open {
            background: #f1f5f9;
            border-color: #cbd5e1;
            color: #334155;
        }
        .badge-status-inprogress {
            background: #e8f7f0;
            border-color: #a7f3d0;
            color: #047857;
        }
        .badge-status-underreview {
            background: #fef9c3;
            border-color: #fde047;
            color: #854d0e;
        }
        .badge-status-completed {
            background: #dcfce7;
            border-color: #86efac;
            color: #15803d;
        }
        .badge-status-closed {
            background: #f1f5f9;
            border-color: #e2e8f0;
            color: #64748b;
        }

        /* Pill Elements */
        .code-pill {
            font-family: monospace;
            font-size: 11.5px;
            font-weight: 700;
            background: #f1f5f9;
            color: #0f172a;
            padding: 3px 8px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .progress-pill-bar {
            height: 6px;
            border-radius: 3px;
            background: #e2e8f0;
            overflow: hidden;
            margin-top: 4px;
            width: 100%;
        }
        .progress-pill-fill {
            height: 100%;
            background: var(--falcon-primary-gradient);
            border-radius: 3px;
            transition: width 0.3s ease;
        }
        .ticket-modal-section-title {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--falcon-gray-500);
            margin-bottom: 8px;
            margin-top: 16px;
            border-bottom: 1px solid var(--falcon-border-color);
            padding-bottom: 4px;
        }
        .target-url-link {
            font-size: 12px;
            color: var(--falcon-primary);
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            max-width: 280px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Kanban Board Styling */
        .kanban-board-wrapper {
            display: flex;
            gap: 18px;
            overflow-x: auto;
            padding-bottom: 24px;
        }
        .kanban-column {
            flex: 1;
            min-width: 275px;
            background: #f8faf9;
            border: 1px solid var(--falcon-border-color);
            border-radius: 10px;
            padding: 14px;
            display: flex;
            flex-direction: column;
        }
        .kanban-column-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e7f0ec;
        }
        .kanban-column-title {
            font-size: 13px;
            font-weight: 800;
            color: var(--falcon-gray-900);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .kanban-column-count {
            background: #e4eee9;
            color: #007745;
            font-size: 11.5px;
            font-weight: 800;
            padding: 2px 8px;
            border-radius: 10px;
        }
        .kanban-card-list {
            min-height: 120px;
        }
        .kanban-card {
            background: #ffffff;
            border: 1px solid #e2ebe6;
            border-radius: 9px;
            padding: 13px 15px;
            margin-bottom: 12px;
            box-shadow: 0 1px 3px rgba(10, 25, 18, 0.03);
            cursor: pointer;
            transition: var(--transition);
        }
        .kanban-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 168, 98, 0.1);
            border-color: var(--falcon-primary);
        }
        .kanban-card-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .kanban-card-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--falcon-gray-1000);
            line-height: 1.35;
            margin-bottom: 8px;
        }
        .kanban-card-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 11.5px;
            color: var(--falcon-gray-600);
            padding-top: 8px;
            border-top: 1px solid var(--falcon-border-subtle);
            margin-top: 6px;
        }

        /* Timeline Items for comments and proof logs */
        .timeline-item {
            border-left: 2px solid #d4e2dc;
            padding: 0 0 16px 14px;
            margin-left: 8px;
            position: relative;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -6px;
            top: 2px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--falcon-primary);
            border: 2px solid #ffffff;
            box-shadow: 0 0 0 2px rgba(0, 168, 98, 0.2);
        }
        .timeline-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 4px;
        }
    </style>
</head>

<body>
    <?php include '../includes/loader.php'; ?>
    <div id="wrapper">
        <!-- Falcon Vertical Navbar Start -->
        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="brand-header">
                <div class="brand-logo-badge">
                    <i class="fa fa-line-chart"></i>
                </div>
                <div>
                    <div class="brand-text-title">Finez SEO</div>
                    <div class="brand-text-subtitle"><span class="brand-status-dot"></span> PLATFORM CONSOLE</div>
                </div>
            </div>

            <div class="sidebar-section-label">SUPER ADMIN CONSOLE</div>

            <div class="sidebar-collapse" id="divMenuContainer">
                <!-- menu injected via ajax / session cache -->
            </div>
        </nav>
        <!-- Falcon Vertical Navbar End -->

        <div id="page-wrapper" class="gray-bg">
            <!-- Top Header Start -->
            <div class="falcon-topbar">
                <div style="display: flex; align-items: center; gap: 14px;">
                    <a class="navbar-minimalize minimalize-styl-2" href="#"><i class="fa fa-bars"></i> </a>
                    <span style="font-size: 14px; font-weight: 700; color: var(--falcon-gray-700);"><i class="fa fa-ticket" style="color: var(--falcon-primary); margin-right: 8px;"></i> SEO Tickets &amp; Link Building Tasks</span>
                </div>

                <div class="topbar-right-section">
                    <span class="live-status-pill hidden-xs"><span class="pulse-dot"></span> In Sync &bull; Live</span>
                    <div class="topbar-user-badge">
                        <img src="../assets/img/customer.png" class="topbar-avatar" alt="Avatar">
                        <span class="topbar-username"><?php echo htmlspecialchars($_SESSION['name'] ?? 'Specialist'); ?></span>
                    </div>
                    <a href="../includes/auth/logout" class="btn-falcon-logout">
                        <i class="fa fa-sign-out"></i>
                        <span>Log out</span>
                    </a>
                </div>
            </div>
            <!-- Top Header End -->

            <!-- Falcon Page Heading Start -->
            <div class="row wrapper page-heading" style="margin: 0 0 20px 0;">
                <div class="col-sm-7 col-xs-12">
                    <span class="page-title-badge"><i class="fa fa-ticket"></i> SEO Operations</span>
                    <h2>SEO Tickets &amp; Tasks</h2>
                    <p class="page-heading-desc">Manage client link building campaigns, broken link replacements, and SEO task workflows</p>
                </div>
                <div class="col-sm-5 col-xs-12 page-heading-actions">
                    <!-- View Switcher (Segmented Control Pill) -->
                    <div class="falcon-view-segmented">
                        <button type="button" class="falcon-view-btn active" id="btn_view_list" onclick="switchView('list')">
                            <i class="fa fa-list"></i> <span>List</span>
                        </button>
                        <button type="button" class="falcon-view-btn" id="btn_view_kanban" onclick="switchView('kanban')">
                            <i class="fa fa-th-large"></i> <span>Campaign Board</span>
                        </button>
                    </div>

                    <!-- + New SEO Ticket Button -->
                    <button type="button" class="btn btn-primary btn-new-ticket" onclick="openNewTicketModal()">
                        <i class="fa fa-plus"></i> New SEO Ticket
                    </button>
                </div>
            </div>
            <!-- Page Heading End -->

            <!-- Falcon CRM 4-Card KPI Row -->
            <div class="wrapper wrapper-content" style="padding-bottom: 0 !important;">
                <div class="row crm-kpi-row">
                    <!-- KPI 1: Active SEO Tasks -->
                    <div class="col-sm-6 col-md-3">
                        <div class="crm-kpi-card">
                            <div class="crm-kpi-info">
                                <span class="crm-kpi-label">Active SEO Tasks</span>
                                <span class="crm-kpi-value" id="kpi_active_tasks">--</span>
                                <span class="crm-kpi-subtext"><span style="color: var(--falcon-primary); font-weight: 700;"><i class="fa fa-tasks"></i> In Progress</span> &amp; Open</span>
                            </div>
                            <div class="crm-kpi-icon primary">
                                <i class="fa fa-ticket"></i>
                            </div>
                        </div>
                    </div>

                    <!-- KPI 2: Link Quota Progress -->
                    <div class="col-sm-6 col-md-3">
                        <div class="crm-kpi-card">
                            <div class="crm-kpi-info">
                                <span class="crm-kpi-label">Link Quota Progress</span>
                                <span class="crm-kpi-value" id="kpi_links_progress" style="color: var(--falcon-primary);">--</span>
                                <span class="crm-kpi-subtext"><span style="color: var(--falcon-green); font-weight: 700;"><i class="fa fa-link"></i> Live links</span> built</span>
                            </div>
                            <div class="crm-kpi-icon success">
                                <i class="fa fa-link"></i>
                            </div>
                        </div>
                    </div>

                    <!-- KPI 3: Pending QA Review -->
                    <div class="col-sm-6 col-md-3">
                        <div class="crm-kpi-card">
                            <div class="crm-kpi-info">
                                <span class="crm-kpi-label">Pending QA Review</span>
                                <span class="crm-kpi-value" id="kpi_pending_qa" style="color: var(--falcon-info);">--</span>
                                <span class="crm-kpi-subtext"><span style="color: var(--falcon-info); font-weight: 700;"><i class="fa fa-check-square-o"></i> Proofs</span> ready for lead</span>
                            </div>
                            <div class="crm-kpi-icon info">
                                <i class="fa fa-check-square-o"></i>
                            </div>
                        </div>
                    </div>

                    <!-- KPI 4: Urgent & Broken Links -->
                    <div class="col-sm-6 col-md-3">
                        <div class="crm-kpi-card">
                            <div class="crm-kpi-info">
                                <span class="crm-kpi-label">Urgent &amp; Broken Links</span>
                                <span class="crm-kpi-value" id="kpi_urgent_tasks" style="color: var(--falcon-danger);">--</span>
                                <span class="crm-kpi-subtext"><span style="color: var(--falcon-danger); font-weight: 700;"><i class="fa fa-exclamation-triangle"></i> Action</span> required</span>
                            </div>
                            <div class="crm-kpi-icon warning">
                                <i class="fa fa-exclamation-triangle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Falcon Operations Toolbar (Filters & Search) -->
            <div class="wrapper wrapper-content" style="padding-bottom: 0 !important; padding-top: 5px !important;">
                <div class="falcon-operations-card">
                    <!-- Left: Filters Group -->
                    <div class="falcon-filters-group">
                        <div class="falcon-filter-label">
                            <i class="fa fa-filter"></i> <span>Filter:</span>
                        </div>

                        <!-- Project Dropdown -->
                        <select id="filter_project" class="falcon-custom-select" onchange="triggerSearch();">
                            <option value="">All Projects</option>
                        </select>

                        <!-- SEO Category Dropdown -->
                        <select id="filter_category" class="falcon-custom-select" onchange="triggerSearch();">
                            <option value="">All Categories</option>
                            <option value="Link Building">🔗 Link Building</option>
                            <option value="Broken Link Replacement">⚠️ Broken Link Replacement</option>
                            <option value="On-Page / Technical SEO">🛠️ On-Page / Technical</option>
                            <option value="Keyword & Anchor Strategy">🎯 Keyword / Anchor</option>
                            <option value="Content Writing">📝 Content Writing</option>
                            <option value="SEO Audit / Indexing">🔍 SEO Audit / Indexing</option>
                            <option value="Client Request">💬 Client Request</option>
                        </select>

                        <!-- Priority Dropdown -->
                        <select id="filter_priority" class="falcon-custom-select" onchange="triggerSearch();">
                            <option value="">All Priorities</option>
                            <option value="Urgent">🔴 Urgent</option>
                            <option value="High">🟠 High</option>
                            <option value="Medium">🔵 Medium</option>
                            <option value="Low">⚪ Low</option>
                        </select>

                        <!-- Status Dropdown (Active in List view) -->
                        <select id="filter_status" class="falcon-custom-select" onchange="triggerSearch();">
                            <option value="">All Statuses</option>
                            <option value="Open">Open</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Under Review">Under Review (QA)</option>
                            <option value="Completed">Completed</option>
                            <option value="Closed">Closed</option>
                        </select>

                        <!-- Specialist Dropdown -->
                        <select id="filter_specialist" class="falcon-custom-select" onchange="triggerSearch();">
                            <option value="">All Specialists</option>
                        </select>

                        <!-- Reset button -->
                        <button type="button" class="btn-falcon-reset" onclick="resetFilters();" title="Reset all filters">
                            <i class="fa fa-refresh"></i> <span>Reset</span>
                        </button>
                    </div>

                    <!-- Right: Search Box -->
                    <div class="falcon-search-wrapper">
                        <div class="modern-search-group">
                            <i class="fa fa-search search-icon"></i>
                            <input type="text" placeholder="Search task, URL, keyword..." id="txt_search" class="form-control modern-search-input" onkeypress="if(event.keyCode==13){triggerSearch();return false;}">
                            <button class="modern-search-btn" onclick="triggerSearch()" type="button">
                                <span>Search</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- View 1: List View (Table + Pagination) -->
            <div class="wrapper wrapper-content animated fadeIn" id="ticket_list_view">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="ibox">
                            <div class="ibox-title">
                                <h5><i class="fa fa-ticket" style="color: var(--falcon-primary); margin-right: 8px;"></i> All SEO Tickets &amp; Campaigns</h5>
                                <div><span class="badge-modern badge-subtle-primary" id="lbl_ticket_count">Loading...</span></div>
                            </div>
                            <div class="ibox-content">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th style="width: 10%;">Code</th>
                                                <th style="width: 30%;">Task &amp; Landing Page</th>
                                                <th style="width: 14%;">Project</th>
                                                <th style="width: 13%;">Category</th>
                                                <th style="width: 9%;">Priority</th>
                                                <th style="width: 10%;">Status</th>
                                                <th style="width: 14%;">Link Quota &amp; Assignee</th>
                                                <th style="width: 10%; text-align: right;">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbl_tickets_body">
                                            <!-- Dynamically injected -->
                                        </tbody>
                                    </table>
                                </div>
                                <div id="pagination_container"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- View 2: Campaign Board View (Kanban) -->
            <div class="wrapper wrapper-content animated fadeIn kanban-view-hidden" id="ticket_kanban_view">
                <div class="kanban-board-wrapper">
                    <!-- Column 1: Open -->
                    <div class="kanban-column" id="kanban_lane_Open">
                        <div class="kanban-column-header">
                            <span class="kanban-column-title"><i class="fa fa-inbox text-muted"></i> Open</span>
                            <span class="kanban-column-count" id="count_lane_Open">0</span>
                        </div>
                        <div class="kanban-card-list" id="cards_lane_Open"></div>
                    </div>

                    <!-- Column 2: In Progress -->
                    <div class="kanban-column" id="kanban_lane_InProgress">
                        <div class="kanban-column-header">
                            <span class="kanban-column-title"><i class="fa fa-spinner text-primary"></i> In Progress</span>
                            <span class="kanban-column-count" id="count_lane_InProgress">0</span>
                        </div>
                        <div class="kanban-card-list" id="cards_lane_InProgress"></div>
                    </div>

                    <!-- Column 3: Under Review (QA) -->
                    <div class="kanban-column" id="kanban_lane_UnderReview">
                        <div class="kanban-column-header">
                            <span class="kanban-column-title"><i class="fa fa-check-square-o text-warning"></i> QA Review</span>
                            <span class="kanban-column-count" id="count_lane_UnderReview">0</span>
                        </div>
                        <div class="kanban-card-list" id="cards_lane_UnderReview"></div>
                    </div>

                    <!-- Column 4: Completed -->
                    <div class="kanban-column" id="kanban_lane_Completed">
                        <div class="kanban-column-header">
                            <span class="kanban-column-title"><i class="fa fa-check-circle text-success"></i> Completed</span>
                            <span class="kanban-column-count" id="count_lane_Completed">0</span>
                        </div>
                        <div class="kanban-card-list" id="cards_lane_Completed"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- ==================================================================== -->
    <!-- Modal: Add / Edit SEO Ticket                                         -->
    <!-- ==================================================================== -->
    <div class="modal inmodal" id="modalTicket" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content animated fadeIn">
                <div class="modal-header" style="padding: 16px 20px;">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="modal_ticket_title" style="font-size: 1.25rem;"><i class="fa fa-ticket" style="color: var(--falcon-primary); margin-right: 6px;"></i> Create SEO Ticket</h4>
                </div>
                <form id="form_ticket" onsubmit="saveTicket(event)">
                    <input type="hidden" id="ticket_id" name="id" value="0">
                    <div class="modal-body" style="padding: 20px 24px;">
                        
                        <!-- Row 1: Project & Title -->
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="form_project_id">Client Project / Website <span class="text-danger">*</span></label>
                                    <select class="form-control" id="form_project_id" name="project_id" required>
                                        <option value="">Select Project</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-8">
                                <div class="form-group">
                                    <label for="form_title">Task Title / SEO Requirement <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="form_title" name="title" placeholder="e.g. Build 10 high-DA guest posts with anchor 'Dubai Safari'" required>
                                </div>
                            </div>
                        </div>

                        <!-- Row 2: Category, Priority, Status, Required Links Quota -->
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="form_category">SEO Category</label>
                                    <select class="form-control" id="form_category" name="category">
                                        <option value="Link Building">🔗 Link Building</option>
                                        <option value="Broken Link Replacement">⚠️ Broken Link Replacement</option>
                                        <option value="On-Page / Technical SEO">🛠️ On-Page / Technical</option>
                                        <option value="Keyword & Anchor Strategy">🎯 Keyword / Anchor</option>
                                        <option value="Content Writing">📝 Content Writing</option>
                                        <option value="SEO Audit / Indexing">🔍 SEO Audit / Indexing</option>
                                        <option value="Client Request">💬 Client Request</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="form_priority">Priority</label>
                                    <select class="form-control" id="form_priority" name="priority">
                                        <option value="Medium">🔵 Medium</option>
                                        <option value="High">🟠 High</option>
                                        <option value="Urgent">🔴 Urgent</option>
                                        <option value="Low">⚪ Low</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="form_status">Status</label>
                                    <select class="form-control" id="form_status" name="status">
                                        <option value="Open">Open</option>
                                        <option value="In Progress">In Progress</option>
                                        <option value="Under Review">Under Review (QA)</option>
                                        <option value="Completed">Completed</option>
                                        <option value="Closed">Closed</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="form_req_links">Required Links Quota</label>
                                    <input type="number" min="0" class="form-control" id="form_req_links" name="required_links_count" placeholder="e.g. 10">
                                </div>
                            </div>
                        </div>

                        <!-- Row 3: Target URL & Target Keywords -->
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="form_target_url">Target Landing Page URL</label>
                                    <input type="url" class="form-control" id="form_target_url" name="target_url" placeholder="https://clientdomain.com/landing-page">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="form_target_keywords">Target Keywords &amp; Anchors</label>
                                    <input type="text" class="form-control" id="form_target_keywords" name="target_keywords" placeholder="e.g. desert safari dubai, quad bike rental">
                                </div>
                            </div>
                        </div>

                        <!-- Row 4: Assignee & Due Date -->
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="form_assigned_to">Assigned SEO Specialist</label>
                                    <select class="form-control" id="form_assigned_to" name="assigned_to">
                                        <option value="">Unassigned</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="form_due_date">Due Date</label>
                                    <input type="date" class="form-control" id="form_due_date" name="due_date">
                                </div>
                            </div>
                        </div>

                        <!-- Row 5: Description & Guidelines -->
                        <div class="form-group">
                            <label for="form_description">Task Instructions &amp; DA/DR Requirements</label>
                            <textarea class="form-control" id="form_description" name="description" rows="3" placeholder="Provide guidelines, minimum Domain Authority, content requirements, or directory specifications..."></textarea>
                        </div>

                    </div>
                    <div class="modal-footer" style="padding: 14px 24px;">
                        <button type="button" class="btn btn-white" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="btn_save_ticket"><i class="fa fa-check"></i> Save Ticket</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ==================================================================== -->
    <!-- Modal: View SEO Ticket & Submit Proof Links                          -->
    <!-- ==================================================================== -->
    <div class="modal inmodal" id="modalViewTicket" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content animated fadeIn">
                <div class="modal-header" style="padding: 16px 20px; display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <span class="code-pill" id="view_ticket_code">SEO-XXXX</span>
                        <span id="view_ticket_cat_badge" class="badge-modern badge-cat-link-building" style="margin-left: 8px;">Link Building</span>
                        <span id="view_ticket_prio_badge" class="badge-modern badge-prio-high" style="margin-left: 4px;">High</span>
                    </div>
                    <div>
                        <button type="button" class="btn btn-white btn-sm" onclick="editTicketFromView();" style="margin-right: 6px;"><i class="fa fa-pencil"></i> Edit</button>
                        <button type="button" class="close" data-dismiss="modal" style="margin-top: 0;"><span aria-hidden="true">&times;</span></button>
                    </div>
                </div>
                <div class="modal-body" style="padding: 20px 24px;">
                    <!-- Title -->
                    <h3 id="view_ticket_title" style="margin-top: 0; font-weight: 800; color: var(--falcon-gray-1100); font-size: 1.35rem; line-height: 1.3;">Task Title</h3>
                    
                    <!-- Quick Meta Grid -->
                    <div style="background: #f8faf9; border: 1px solid var(--falcon-border-color); border-radius: 8px; padding: 12px 16px; margin: 14px 0 20px 0; display: flex; flex-wrap: wrap; gap: 20px; font-size: 12.5px;">
                        <div>
                            <span style="color: var(--falcon-gray-500); display: block; font-size: 11px; font-weight: 700; text-transform: uppercase;">Project</span>
                            <strong id="view_ticket_project" style="color: var(--falcon-gray-900);">Project Name</strong>
                        </div>
                        <div>
                            <span style="color: var(--falcon-gray-500); display: block; font-size: 11px; font-weight: 700; text-transform: uppercase;">Status</span>
                            <span id="view_ticket_status_badge" class="badge-modern badge-status-inprogress">In Progress</span>
                        </div>
                        <div>
                            <span style="color: var(--falcon-gray-500); display: block; font-size: 11px; font-weight: 700; text-transform: uppercase;">SEO Specialist</span>
                            <strong id="view_ticket_assignee" style="color: var(--falcon-gray-900);">Specialist Name</strong>
                        </div>
                        <div>
                            <span style="color: var(--falcon-gray-500); display: block; font-size: 11px; font-weight: 700; text-transform: uppercase;">Due Date</span>
                            <strong id="view_ticket_due_date" style="color: var(--falcon-gray-900);">2026-10-25</strong>
                        </div>
                        <div>
                            <span style="color: var(--falcon-gray-500); display: block; font-size: 11px; font-weight: 700; text-transform: uppercase;">Backlinks Built</span>
                            <strong id="view_ticket_links_count" style="color: var(--falcon-primary);">0 / 10 Links</strong>
                        </div>
                    </div>

                    <!-- Target URL & Keywords Box -->
                    <div style="background: #ffffff; border: 1px solid var(--falcon-border-color); border-radius: 8px; padding: 12px 16px; margin-bottom: 20px;">
                        <div class="row">
                            <div class="col-sm-6">
                                <span style="font-size: 11px; font-weight: 700; color: var(--falcon-gray-500); text-transform: uppercase; display: block; margin-bottom: 4px;">Target Landing Page</span>
                                <a id="view_ticket_url" href="#" target="_blank" class="target-url-link"><i class="fa fa-external-link"></i> None</a>
                            </div>
                            <div class="col-sm-6">
                                <span style="font-size: 11px; font-weight: 700; color: var(--falcon-gray-500); text-transform: uppercase; display: block; margin-bottom: 4px;">Target Keywords / Anchors</span>
                                <span id="view_ticket_keywords" style="font-weight: 700; color: var(--falcon-gray-900); font-size: 12.5px;">None</span>
                            </div>
                        </div>
                    </div>

                    <!-- Workflow Quick Change Row -->
                    <div style="margin-bottom: 20px; display: flex; align-items: center; gap: 8px; flex-wrap: wrap; background: #ffffff; border: 1px dashed var(--falcon-border-color); padding: 10px 14px; border-radius: 8px;">
                        <span style="font-size: 12px; font-weight: 700; color: var(--falcon-gray-700);"><i class="fa fa-exchange"></i> Quick Workflow:</span>
                        <button type="button" class="btn btn-white btn-xs" onclick="quickStatusChange('In Progress')">Start Working</button>
                        <button type="button" class="btn btn-white btn-xs" onclick="quickStatusChange('Under Review')"><i class="fa fa-check-square-o"></i> Submit for QA Review</button>
                        <button type="button" class="btn btn-primary btn-xs" onclick="quickStatusChange('Completed')"><i class="fa fa-check"></i> Approve &amp; Mark Completed</button>
                    </div>

                    <!-- Description -->
                    <div class="ticket-modal-section-title">Task Instructions &amp; Requirements</div>
                    <div id="view_ticket_desc" style="font-size: 13.5px; color: var(--falcon-gray-800); white-space: pre-wrap; margin-bottom: 14px;">No instructions provided.</div>

                    <!-- Comments & Proof Submissions Timeline -->
                    <div class="ticket-modal-section-title" style="margin-top: 24px;"><i class="fa fa-comments-o"></i> Proof Submissions &amp; Activity Log</div>
                    <div id="view_comments_container" style="max-height: 280px; overflow-y: auto; margin-bottom: 16px;">
                        <!-- Injected via AJAX -->
                    </div>

                    <!-- Add Proof Submission / Comment Form -->
                    <div style="background: #ffffff; border: 1px solid var(--falcon-border-color); border-radius: 8px; padding: 14px;">
                        <div style="font-size: 12.5px; font-weight: 700; color: var(--falcon-gray-800); margin-bottom: 8px;">Submit Live Backlink Proofs or Specialist Update</div>
                        <div class="row">
                            <div class="col-sm-9">
                                <textarea id="txt_comment" class="form-control" rows="2" placeholder="Paste live backlink URLs, directory links, or campaign notes..."></textarea>
                            </div>
                            <div class="col-sm-3">
                                <input type="number" min="0" id="num_links_added" class="form-control" placeholder="+ Links built (e.g. 2)" style="margin-bottom: 8px;">
                                <button type="button" class="btn btn-primary btn-block" onclick="submitComment();" style="height: 38px;">
                                    <i class="fa fa-send"></i> Submit Proof
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="../assets/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
    <script src="../assets/js/inspinia.js"></script>
    <script src="../assets/js/plugins/pace/pace.min.js"></script>
    <script src="../assets/js/menu.js?v=4.2"></script>

    <script>
        var currentActiveView = 'list';
        var currentTicketPage = 1;
        var activeViewTicketId = 0;
        var cachedProjects = [];
        var cachedSpecialists = [];

        $(document).ready(function() {
            loadMenu();
            loadFilterOptions();
            loadKPIs();
            loadTickets(1);
        });

        // =====================================================================
        // View Switcher (List vs Campaign Board)
        // =====================================================================
        function switchView(view) {
            currentActiveView = view;
            if (view === 'list') {
                $('#btn_view_list').addClass('active');
                $('#btn_view_kanban').removeClass('active');
                $('#ticket_list_view').removeClass('kanban-view-hidden');
                $('#ticket_kanban_view').addClass('kanban-view-hidden');
                $('#filter_status').show();
                loadTickets(currentTicketPage);
            } else {
                $('#btn_view_kanban').addClass('active');
                $('#btn_view_list').removeClass('active');
                $('#ticket_kanban_view').removeClass('kanban-view-hidden');
                $('#ticket_list_view').addClass('kanban-view-hidden');
                $('#filter_status').hide();
                loadCampaignBoard();
            }
        }

        // =====================================================================
        // Load Filter Options & KPIs
        // =====================================================================
        function loadFilterOptions() {
            $.ajax({
                type: "POST",
                url: "api/tickets",
                data: { action: 'get_filter_options' },
                dataType: "json",
                success: function(res) {
                    if (res.status === 'success') {
                        cachedProjects = res.projects;
                        cachedSpecialists = res.specialists;

                        // Fill Filter Project
                        var projHtm = '<option value="">All Projects</option>';
                        var modalProjHtm = '<option value="">Select Project</option>';
                        for (var i = 0; i < res.projects.length; i++) {
                            projHtm += '<option value="' + res.projects[i].id + '">' + res.projects[i].name + '</option>';
                            modalProjHtm += '<option value="' + res.projects[i].id + '">' + res.projects[i].name + '</option>';
                        }
                        $('#filter_project').html(projHtm);
                        $('#form_project_id').html(modalProjHtm);

                        // Fill Specialists
                        var specHtm = '<option value="">All Specialists</option>';
                        var modalSpecHtm = '<option value="">Unassigned</option>';
                        for (var j = 0; j < res.specialists.length; j++) {
                            specHtm += '<option value="' + res.specialists[j].login_id + '">' + res.specialists[j].name + '</option>';
                            modalSpecHtm += '<option value="' + res.specialists[j].login_id + '">' + res.specialists[j].name + '</option>';
                        }
                        $('#filter_specialist').html(specHtm);
                        $('#form_assigned_to').html(modalSpecHtm);
                    }
                }
            });
        }

        function loadKPIs() {
            $.ajax({
                type: "POST",
                url: "api/tickets",
                data: { action: 'get_kpis' },
                dataType: "json",
                success: function(res) {
                    if (res.status === 'success') {
                        $('#kpi_active_tasks').text(res.active_tasks);
                        $('#kpi_links_progress').text(res.links_completed + ' / ' + res.links_quota);
                        $('#kpi_pending_qa').text(res.pending_qa);
                        $('#kpi_urgent_tasks').text(res.urgent_tasks);
                    }
                }
            });
        }

        function triggerSearch() {
            if (currentActiveView === 'list') {
                loadTickets(1);
            } else {
                loadCampaignBoard();
            }
        }

        function resetFilters() {
            $('#txt_search').val('');
            $('#filter_project').val('');
            $('#filter_category').val('');
            $('#filter_priority').val('');
            $('#filter_status').val('');
            $('#filter_specialist').val('');
            triggerSearch();
        }

        function getCategoryBadgeClass(cat) {
            if (!cat) return 'badge-cat-link-building';
            var c = cat.toLowerCase();
            if (c.indexOf('broken') !== -1) return 'badge-cat-broken-link';
            if (c.indexOf('technical') !== -1 || c.indexOf('on-page') !== -1) return 'badge-cat-technical';
            if (c.indexOf('keyword') !== -1 || c.indexOf('anchor') !== -1) return 'badge-cat-keyword';
            if (c.indexOf('content') !== -1) return 'badge-cat-content';
            if (c.indexOf('audit') !== -1 || c.indexOf('indexing') !== -1) return 'badge-cat-audit';
            if (c.indexOf('client') !== -1) return 'badge-cat-client';
            return 'badge-cat-link-building';
        }

        // =====================================================================
        // List View Loading
        // =====================================================================
        function loadTickets(page) {
            currentTicketPage = page;
            if (window.FinezLoader) {
                FinezLoader.tableLoader('#tbl_tickets_body', 'Loading SEO tasks & tickets...', 7);
            }
            var payload = {
                action: 'load_data',
                page: page,
                val: $('#txt_search').val(),
                project_id: $('#filter_project').val(),
                category: $('#filter_category').val(),
                priority: $('#filter_priority').val(),
                status: $('#filter_status').val(),
                assigned_to: $('#filter_specialist').val()
            };

            $.ajax({
                type: "POST",
                url: "api/tickets",
                data: payload,
                dataType: "json",
                success: function(data) {
                    var total = data[0].total_rows;
                    $('#lbl_ticket_count').html('<i class="fa fa-ticket"></i> ' + total + ' SEO Tasks');

                    var rows = data[1];
                    var htm = "";

                    if (!rows || rows.length === 0) {
                        htm += "<tr><td colspan='8' style='text-align: center; padding: 48px 20px; color: var(--falcon-gray-600);'>";
                        htm += "<i class='fa fa-ticket' style='font-size: 38px; color: var(--falcon-gray-400); display: block; margin-bottom: 12px;'></i>";
                        htm += "<strong style='font-size: 15px; color: var(--falcon-gray-900);'>No SEO tickets found</strong><br>";
                        htm += "<small>Adjust your search filters or click '+ New SEO Ticket' to create a link building or technical task.</small>";
                        htm += "</td></tr>";
                    } else {
                        for (var i = 0; i < rows.length; i++) {
                            var t = rows[i];
                            var catClass = getCategoryBadgeClass(t.category);
                            var prioClass = 'badge-prio-' + t.priority.toLowerCase();
                            var statusClass = 'badge-status-' + t.status.toLowerCase().replace(/\s+/g, '');

                            var targetUrlHtm = t.target_url ? "<a href='" + t.target_url + "' target='_blank' class='target-url-link' title='" + t.target_url + "'><i class='fa fa-globe'></i> " + t.target_url + "</a>" : "";

                            var pct = 0;
                            if (parseInt(t.required_links_count) > 0) {
                                pct = Math.min(100, Math.round((parseInt(t.completed_links_count) / parseInt(t.required_links_count)) * 100));
                            }

                            htm += "<tr>";
                            // Code
                            htm += "<td><span class='code-pill'>" + t.ticket_code + "</span></td>";
                            
                            // Title & Target Landing Page
                            htm += "<td>";
                            htm += "<a href='javascript:void(0);' onclick='viewTicketDetails(" + t.id + ");' style='color: var(--falcon-gray-1000); font-weight: 700; font-size: 0.92rem; display: block; line-height: 1.3;'>";
                            htm += t.title;
                            htm += "</a>";
                            if (targetUrlHtm) {
                                htm += "<div style='margin-top: 4px;'>" + targetUrlHtm + "</div>";
                            }
                            htm += "</td>";

                            // Project
                            htm += "<td><strong style='color: var(--falcon-gray-900); font-size: 12.5px;'>" + (t.project_name || 'N/A') + "</strong></td>";

                            // Category
                            htm += "<td><span class='badge-modern " + catClass + "'>" + t.category + "</span></td>";

                            // Priority
                            htm += "<td><span class='badge-modern " + prioClass + "'>" + t.priority + "</span></td>";

                            // Status
                            htm += "<td><span class='badge-modern " + statusClass + "'>" + t.status + "</span></td>";

                            // Link Quota & Assignee
                            htm += "<td>";
                            htm += "<div style='display: flex; justify-content: space-between; font-size: 11.5px; font-weight: 700; color: var(--falcon-gray-800);'>";
                            htm += "<span><i class='fa fa-link text-success'></i> " + t.completed_links_count + " / " + t.required_links_count + "</span>";
                            htm += "<span class='text-muted'>" + pct + "%</span>";
                            htm += "</div>";
                            htm += "<div class='progress-pill-bar'><div class='progress-pill-fill' style='width: " + pct + "%;'></div></div>";
                            htm += "<div style='font-size: 11.5px; color: var(--falcon-gray-600); margin-top: 4px;'><i class='fa fa-user-circle-o'></i> " + t.assignee_name + "</div>";
                            htm += "</td>";

                            // Actions
                            htm += "<td style='text-align: right;'>";
                            htm += "<div class='btn-action-group'>";
                            htm += "<button type='button' class='btn-action-edit' onclick='viewTicketDetails(" + t.id + ");' title='View & Proofs'><i class='fa fa-eye'></i> View</button>";
                            htm += "<button type='button' class='btn-action-backlink' onclick='openEditModal(" + t.id + ");' title='Edit Task'><i class='fa fa-pencil'></i></button>";
                            htm += "</div>";
                            htm += "</td>";

                            htm += "</tr>";
                        }
                    }

                    $('#tbl_tickets_body').html(htm);

                    // Pagination
                    var pageHtm = paginate(total, page);
                    $('#pagination_container').html(pageHtm);
                }
            });
        }

        // Pagination hook expected by pagination.js
        function loadData(page) {
            loadTickets(page);
        }

        // =====================================================================
        // Campaign Board View Loading (Kanban)
        // =====================================================================
        function loadCampaignBoard() {
            var payload = {
                action: 'load_campaign_board',
                val: $('#txt_search').val(),
                project_id: $('#filter_project').val(),
                category: $('#filter_category').val(),
                priority: $('#filter_priority').val(),
                assigned_to: $('#filter_specialist').val()
            };

            $.ajax({
                type: "POST",
                url: "api/tickets",
                data: payload,
                dataType: "json",
                success: function(res) {
                    if (res.status === 'success') {
                        var lanes = res.lanes;
                        renderKanbanLane('Open', lanes['Open'] || []);
                        renderKanbanLane('InProgress', lanes['In Progress'] || []);
                        renderKanbanLane('UnderReview', lanes['Under Review'] || []);
                        renderKanbanLane('Completed', lanes['Completed'] || []);
                    }
                }
            });
        }

        function renderKanbanLane(laneKey, cards) {
            $('#count_lane_' + laneKey).text(cards.length);
            var htm = "";

            if (cards.length === 0) {
                htm = "<div style='text-align: center; padding: 24px 10px; color: var(--falcon-gray-400); font-size: 12px;'>No tasks in this lane</div>";
            } else {
                for (var i = 0; i < cards.length; i++) {
                    var c = cards[i];
                    var prioClass = 'badge-prio-' + c.priority.toLowerCase();
                    var catClass = getCategoryBadgeClass(c.category);

                    var pct = 0;
                    if (parseInt(c.required_links_count) > 0) {
                        pct = Math.min(100, Math.round((parseInt(c.completed_links_count) / parseInt(c.required_links_count)) * 100));
                    }

                    htm += "<div class='kanban-card' onclick='viewTicketDetails(" + c.id + ")'>";
                    htm += "<div class='kanban-card-top'>";
                    htm += "<span class='code-pill'>" + c.ticket_code + "</span>";
                    htm += "<span class='badge-modern " + prioClass + "'>" + c.priority + "</span>";
                    htm += "</div>";

                    htm += "<div class='kanban-card-title'>" + c.title + "</div>";

                    htm += "<div style='display: flex; gap: 5px; margin-bottom: 8px;'>";
                    htm += "<span class='badge-modern " + catClass + "'>" + c.category + "</span>";
                    htm += "</div>";

                    if (parseInt(c.required_links_count) > 0) {
                        htm += "<div style='margin-bottom: 8px;'>";
                        htm += "<div style='display: flex; justify-content: space-between; font-size: 11px; font-weight: 700; color: var(--falcon-gray-700);'>";
                        htm += "<span><i class='fa fa-link'></i> " + c.completed_links_count + "/" + c.required_links_count + "</span>";
                        htm += "<span>" + pct + "%</span>";
                        htm += "</div>";
                        htm += "<div class='progress-pill-bar'><div class='progress-pill-fill' style='width: " + pct + "%;'></div></div>";
                        htm += "</div>";
                    }

                    htm += "<div class='kanban-card-meta'>";
                    htm += "<span><i class='fa fa-globe'></i> " + (c.project_name || 'N/A') + "</span>";
                    htm += "<span><i class='fa fa-user'></i> " + c.assignee_name + "</span>";
                    htm += "</div>";

                    htm += "</div>";
                }
            }
            $('#cards_lane_' + laneKey).html(htm);
        }

        // =====================================================================
        // Modal Handlers: Create & Edit Ticket
        // =====================================================================
        function openNewTicketModal() {
            $('#form_ticket')[0].reset();
            $('#ticket_id').val('0');
            $('#modal_ticket_title').html('<i class="fa fa-ticket" style="color: var(--falcon-primary); margin-right: 6px;"></i> Create SEO Ticket');
            $('#btn_save_ticket').html('<i class="fa fa-check"></i> Create Ticket');
            $('#modalTicket').modal('show');
        }

        function openEditModal(ticketId) {
            $.ajax({
                type: "POST",
                url: "api/tickets",
                data: { action: 'get_ticket_details', id: ticketId },
                dataType: "json",
                success: function(res) {
                    if (res.status === 'success') {
                        var t = res.ticket;
                        $('#ticket_id').val(t.id);
                        $('#form_project_id').val(t.project_id);
                        $('#form_title').val(t.title);
                        $('#form_category').val(t.category);
                        $('#form_priority').val(t.priority);
                        $('#form_status').val(t.status);
                        $('#form_target_url').val(t.target_url || '');
                        $('#form_target_keywords').val(t.target_keywords || '');
                        $('#form_req_links').val(t.required_links_count);
                        $('#form_assigned_to').val(t.assigned_to || '');
                        $('#form_due_date').val(t.due_date || '');
                        $('#form_description').val(t.description || '');

                        $('#modal_ticket_title').html('<i class="fa fa-pencil" style="color: var(--falcon-primary); margin-right: 6px;"></i> Edit SEO Ticket #' + t.ticket_code);
                        $('#btn_save_ticket').html('<i class="fa fa-check"></i> Save Changes');
                        $('#modalTicket').modal('show');
                    }
                }
            });
        }

        function saveTicket(e) {
            e.preventDefault();
            var formData = $('#form_ticket').serialize() + '&action=save_ticket';

            $.ajax({
                type: "POST",
                url: "api/tickets",
                data: formData,
                dataType: "json",
                success: function(res) {
                    if (res.status === 'success') {
                        $('#modalTicket').modal('hide');
                        swal({
                            title: "Saved!",
                            text: res.message,
                            type: "success",
                            timer: 1800,
                            showConfirmButton: false
                        });
                        loadKPIs();
                        triggerSearch();
                    } else {
                        swal("Error", res.message || "Failed to save ticket", "error");
                    }
                },
                error: function() {
                    swal("Error", "Server error while saving ticket", "error");
                }
            });
        }

        // =====================================================================
        // View Ticket & Activity Thread
        // =====================================================================
        function viewTicketDetails(ticketId) {
            activeViewTicketId = ticketId;
            $.ajax({
                type: "POST",
                url: "api/tickets",
                data: { action: 'get_ticket_details', id: ticketId },
                dataType: "json",
                success: function(res) {
                    if (res.status === 'success') {
                        var t = res.ticket;
                        $('#view_ticket_code').text(t.ticket_code);
                        $('#view_ticket_title').text(t.title);
                        $('#view_ticket_project').text(t.project_name || 'N/A');
                        $('#view_ticket_assignee').text(t.assignee_name);
                        $('#view_ticket_due_date').text(t.due_date || 'No deadline');
                        $('#view_ticket_links_count').text(t.completed_links_count + ' / ' + t.required_links_count + ' Links');
                        $('#view_ticket_desc').text(t.description || 'No instructions provided.');

                        // Target Landing page
                        if (t.target_url) {
                            $('#view_ticket_url').attr('href', t.target_url).html('<i class="fa fa-globe"></i> ' + t.target_url).show();
                        } else {
                            $('#view_ticket_url').attr('href', '#').text('None');
                        }

                        // Target Keywords
                        $('#view_ticket_keywords').text(t.target_keywords || 'None');

                        // Category badge
                        var catClass = getCategoryBadgeClass(t.category);
                        $('#view_ticket_cat_badge').attr('class', 'badge-modern ' + catClass).text(t.category);

                        // Priority badge
                        var prioClass = 'badge-prio-' + t.priority.toLowerCase();
                        $('#view_ticket_prio_badge').attr('class', 'badge-modern ' + prioClass).text(t.priority);

                        // Status badge
                        var statusClass = 'badge-status-' + t.status.toLowerCase().replace(/\s+/g, '');
                        $('#view_ticket_status_badge').attr('class', 'badge-modern ' + statusClass).text(t.status);

                        // Render Comments
                        renderComments(res.comments);

                        $('#modalViewTicket').modal('show');
                    }
                }
            });
        }

        function renderComments(comments) {
            var htm = "";
            if (!comments || comments.length === 0) {
                htm = "<div style='text-align: center; padding: 16px; color: var(--falcon-gray-500); font-size: 12.5px;'>No proof links or updates submitted yet.</div>";
            } else {
                for (var i = 0; i < comments.length; i++) {
                    var c = comments[i];
                    var isStatusChange = (c.comment_type === 'status_change');
                    var isProof = (c.comment_type === 'proof_submission');
                    var itemClass = isStatusChange ? 'timeline-item status-change' : (isProof ? 'timeline-item' : 'timeline-item');
                    var icon = isStatusChange ? 'fa fa-refresh text-success' : (isProof ? 'fa fa-link text-success' : 'fa fa-comment-o text-primary');

                    htm += "<div class='" + itemClass + "'>";
                    htm += "<div class='timeline-header'>";
                    htm += "<strong><i class='" + icon + "' style='margin-right: 5px;'></i> " + c.author_name + "</strong>";
                    htm += "<span class='text-muted' style='font-size: 11px;'>" + c.created_at;
                    if (parseInt(c.links_submitted_count) > 0) {
                        htm += " &bull; <span class='badge-modern badge-subtle-success'><i class='fa fa-link'></i> +" + c.links_submitted_count + " Links Built</span>";
                    }
                    htm += "</span>";
                    htm += "</div>";
                    htm += "<div style='font-size: 13px; color: var(--falcon-gray-900); white-space: pre-wrap;'>" + c.comment + "</div>";
                    htm += "</div>";
                }
            }
            $('#view_comments_container').html(htm);
        }

        function submitComment() {
            var comment = $('#txt_comment').val().trim();
            var linksAdded = $('#num_links_added').val();

            if (!comment) {
                swal("Wait", "Please enter your submission notes or live backlink URLs.", "warning");
                return;
            }

            $.ajax({
                type: "POST",
                url: "api/tickets",
                data: {
                    action: 'add_comment',
                    ticket_id: activeViewTicketId,
                    comment: comment,
                    links_submitted_count: linksAdded || 0
                },
                dataType: "json",
                success: function(res) {
                    if (res.status === 'success') {
                        $('#txt_comment').val('');
                        $('#num_links_added').val('');
                        viewTicketDetails(activeViewTicketId);
                        loadKPIs();
                        triggerSearch();
                    } else {
                        swal("Error", res.message || "Failed to post update", "error");
                    }
                }
            });
        }

        function quickStatusChange(newStatus) {
            $.ajax({
                type: "POST",
                url: "api/tickets",
                data: {
                    action: 'change_status',
                    id: activeViewTicketId,
                    status: newStatus
                },
                dataType: "json",
                success: function(res) {
                    if (res.status === 'success') {
                        viewTicketDetails(activeViewTicketId);
                        loadKPIs();
                        triggerSearch();
                    } else {
                        swal("Error", res.message, "error");
                    }
                }
            });
        }

        function editTicketFromView() {
            $('#modalViewTicket').modal('hide');
            openEditModal(activeViewTicketId);
        }
    </script>
</body>

</html>
