<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
// Administrative Access Only (Restricted from employees)
if (!isset($_SESSION['login_id']) || (int)$_SESSION['login_id'] !== 1) {
    header("Location: dashboard.php");
    exit();
}

}
if (!isset($_SESSION['name'])) {
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Falcon | Database Backups - Finez SEO</title>

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

    <style>
        .sql-file-badge {
            width: 38px;
            height: 38px;
            border-radius: 9px;
            background: var(--falcon-primary-bg-subtle);
            color: var(--falcon-primary);
            border: 1px solid var(--falcon-primary-border-subtle);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            flex-shrink: 0;
        }
        .server-folder-banner {
            background: linear-gradient(135deg, #092c1d 0%, #051d13 100%);
            border-radius: var(--falcon-border-radius);
            padding: 20px 24px;
            margin-bottom: 24px;
            color: #ffffff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
        }
        .server-folder-code {
            font-family: monospace;
            background: rgba(255, 255, 255, 0.12);
            color: #86efac;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12.5px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            display: inline-block;
            margin-top: 4px;
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
                    <span style="font-size: 14px; font-weight: 700; color: var(--falcon-gray-700);"><i class="fa fa-database" style="color: var(--falcon-primary); margin-right: 8px;"></i> Database Administration &amp; Snapshots</span>
                </div>

                <div class="topbar-right-section">
                    <span class="live-status-pill hidden-xs"><span class="pulse-dot"></span> Server Online &bull; Active</span>
                    <div class="topbar-user-badge">
                        <img src="../assets/img/customer.png" class="topbar-avatar" alt="Avatar">
                        <span class="topbar-username"><?php echo htmlspecialchars($_SESSION['name'] ?? 'Admin'); ?></span>
                    </div>
                    <a href="../includes/auth/logout.php" class="btn-falcon-logout">
                        <i class="fa fa-sign-out"></i>
                        <span>Log out</span>
                    </a>
                </div>
            </div>
            <!-- Top Header End -->

            <!-- Falcon Page Heading Start -->
            <div class="row wrapper page-heading" style="margin: 0 0 20px 0; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap;">
                <div class="col-sm-7 page-heading-left">
                    <span class="page-title-badge"><i class="fa fa-database"></i> System Operations</span>
                    <h2>Database Backups</h2>
                    <p class="page-heading-desc">Export database snapshots, preserve backups on the server disk, and download to your PC</p>
                </div>
                <div class="col-sm-5 page-heading-right" style="display: flex; align-items: center; justify-content: flex-end; gap: 12px; margin-top: 10px; flex-wrap: wrap;">
                    <button type="button" class="btn btn-white" onclick="loadBackupsList();" style="height: 40px; font-size: 13px; font-weight: 600; padding: 0 14px; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fa fa-refresh"></i> Refresh
                    </button>
                    <button type="button" class="btn btn-primary" id="btn_create_backup" onclick="createAndDownloadBackup();" style="height: 40px; font-size: 13px; font-weight: 700; padding: 0 18px; display: inline-flex; align-items: center; gap: 8px; white-space: nowrap;">
                        <i class="fa fa-download"></i> Create &amp; Download Backup
                    </button>
                </div>
            </div>
            <!-- Page Heading End -->

            <!-- Falcon CRM 3-Card KPI Row -->
            <div class="wrapper wrapper-content" style="padding-bottom: 0 !important; padding-top: 0 !important;">
                <div class="row crm-kpi-row">
                    <!-- KPI 1: Total Backups on Server -->
                    <div class="col-sm-4 col-xs-12">
                        <div class="crm-kpi-card">
                            <div class="crm-kpi-info">
                                <span class="crm-kpi-label">Server Backups</span>
                                <span class="crm-kpi-value" id="kpi_total_backups">--</span>
                                <span class="crm-kpi-subtext"><span style="color: var(--falcon-primary); font-weight: 700;"><i class="fa fa-check-circle"></i> Preserved</span> on server disk</span>
                            </div>
                            <div class="crm-kpi-icon primary">
                                <i class="fa fa-database"></i>
                            </div>
                        </div>
                    </div>

                    <!-- KPI 2: Server Storage Used -->
                    <div class="col-sm-4 col-xs-12">
                        <div class="crm-kpi-card">
                            <div class="crm-kpi-info">
                                <span class="crm-kpi-label">Total Backup Storage</span>
                                <span class="crm-kpi-value" id="kpi_total_storage" style="color: var(--falcon-gray-1000);">--</span>
                                <span class="crm-kpi-subtext"><span style="color: var(--falcon-info); font-weight: 700;"><i class="fa fa-hdd-o"></i> Allocated</span> storage</span>
                            </div>
                            <div class="crm-kpi-icon info">
                                <i class="fa fa-hdd-o"></i>
                            </div>
                        </div>
                    </div>

                    <!-- KPI 3: Latest Backup Timestamp -->
                    <div class="col-sm-4 col-xs-12">
                        <div class="crm-kpi-card">
                            <div class="crm-kpi-info">
                                <span class="crm-kpi-label">Latest Snapshot</span>
                                <span class="crm-kpi-value" id="kpi_latest_backup" style="font-size: 1.05rem; font-weight: 800; color: var(--falcon-green); letter-spacing: 0; line-height: 1.25;">Checking...</span>
                                <span class="crm-kpi-subtext"><span style="color: var(--falcon-green); font-weight: 700;"><i class="fa fa-shield"></i> Safe</span> recovery point</span>
                            </div>
                            <div class="crm-kpi-icon success">
                                <i class="fa fa-shield"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Server Storage Location Callout Banner -->
            <div class="wrapper wrapper-content" style="padding-bottom: 0 !important; padding-top: 5px !important;">
                <div class="server-folder-banner">
                    <div>
                        <div style="font-size: 14.5px; font-weight: 800; display: flex; align-items: center; gap: 8px;">
                            <i class="fa fa-folder-open-o text-success"></i> Server Backup Storage Directory
                        </div>
                        <div style="color: rgba(255, 255, 255, 0.75); font-size: 12.5px; margin-top: 3px;">
                            Backups are automatically saved to the server folder below AND can be downloaded directly to your local computer:
                        </div>
                        <div class="server-folder-code" id="lbl_server_folder">Loading server path...</div>
                    </div>
                    <div>
                        <span class="badge-modern badge-subtle-success" style="background: rgba(16, 185, 129, 0.2) !important; color: #86efac !important; border: 1px solid rgba(16, 185, 129, 0.4);">
                            <i class="fa fa-lock"></i> Protected by .htaccess
                        </span>
                    </div>
                </div>
            </div>

            <!-- Backups Table List -->
            <div class="wrapper wrapper-content animated fadeIn">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="ibox">
                            <div class="ibox-title">
                                <h5><i class="fa fa-archive" style="color: var(--falcon-primary); margin-right: 8px;"></i> Available Server Backups</h5>
                                <div><span class="badge-modern badge-subtle-primary" id="lbl_status_pill">In Sync</span></div>
                            </div>
                            <div class="ibox-content">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th style="width: 40%;">Backup File Name</th>
                                                <th style="width: 15%;">File Size</th>
                                                <th style="width: 25%;">Created Date &amp; Time</th>
                                                <th style="width: 20%; text-align: right;">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbl_backups_body">
                                            <!-- Dynamically injected -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Hidden iframe for background file downloads -->
    <iframe id="iframe_downloader" style="display: none;"></iframe>

    <!-- Scripts -->
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="../assets/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
    <script src="../assets/js/inspinia.js"></script>
    <script src="../assets/js/plugins/pace/pace.min.js"></script>
    <script src="../assets/js/menu.js?v=4.2"></script>

    <script>
        $(document).ready(function() {
            loadMenu();
            loadBackupsList();
        });

        // =====================================================================
        // Load Backups List & KPIs
        // =====================================================================
        function loadBackupsList() {
            if (window.FinezLoader) {
                FinezLoader.tableLoader('#tbl_backups_body', 'Scanning server backup snapshots...', 4);
            }
            $.ajax({
                type: "POST",
                url: "api/backup.php",
                data: { action: 'list_backups' },
                dataType: "json",
                success: function(res) {
                    if (res.status === 'success') {
                        // Update KPIs
                        $('#kpi_total_backups').text(res.total_count);
                        $('#kpi_total_storage').text(res.total_size_formatted);
                        $('#kpi_latest_backup').text(res.latest_backup);
                        $('#lbl_server_folder').text(res.server_folder);

                        var backups = res.backups;
                        var htm = "";

                        if (!backups || backups.length === 0) {
                            htm += "<tr><td colspan='4' style='text-align: center; padding: 48px 20px; color: var(--falcon-gray-600);'>";
                            htm += "<i class='fa fa-database' style='font-size: 38px; color: var(--falcon-gray-400); display: block; margin-bottom: 12px;'></i>";
                            htm += "<strong style='font-size: 15px; color: var(--falcon-gray-900);'>No backups generated yet</strong><br>";
                            htm += "<small>Click 'Create &amp; Download Backup' to generate your first full database snapshot.</small>";
                            htm += "</td></tr>";
                        } else {
                            for (var i = 0; i < backups.length; i++) {
                                var b = backups[i];
                                htm += "<tr>";
                                
                                // File Name with icon
                                htm += "<td>";
                                htm += "<div style='display: flex; align-items: center; gap: 12px;'>";
                                htm += "<div class='sql-file-badge'><i class='fa fa-file-code-o'></i></div>";
                                htm += "<div>";
                                htm += "<strong style='color: var(--falcon-gray-1000); font-size: 13.5px; display: block;'>" + b.filename + "</strong>";
                                htm += "<span style='color: var(--falcon-gray-500); font-size: 11.5px;'><i class='fa fa-check text-success'></i> Verified SQL snapshot &bull; " + b.created_ago + "</span>";
                                htm += "</div>";
                                htm += "</div>";
                                htm += "</td>";

                                // File Size
                                htm += "<td>";
                                htm += "<span class='badge-modern badge-modern-slate' style='font-size: 12px !important; font-weight: 700;'>" + b.size_formatted + "</span>";
                                htm += "</td>";

                                // Created Date
                                htm += "<td>";
                                htm += "<div style='color: var(--falcon-gray-900); font-weight: 600; font-size: 13px;'><i class='fa fa-calendar-o text-muted'></i> " + b.created_at + "</div>";
                                htm += "</td>";

                                // Actions
                                htm += "<td style='text-align: right;'>";
                                htm += "<div class='btn-action-group'>";
                                htm += "<a href='" + b.download_url + "' class='btn-action-edit' style='text-decoration: none;' title='Download to computer'><i class='fa fa-download'></i> Download</a>";
                                htm += "<button type='button' class='btn-action-backlink' onclick='confirmDeleteBackup(\"" + b.filename + "\");' style='color: var(--falcon-danger);' title='Delete from server'><i class='fa fa-trash'></i> Delete</button>";
                                htm += "</div>";
                                htm += "</td>";

                                htm += "</tr>";
                            }
                        }

                        $('#tbl_backups_body').html(htm);
                    }
                },
                error: function() {
                    $('#tbl_backups_body').html("<tr><td colspan='4' class='text-center text-danger'>Failed to load backups list.</td></tr>");
                }
            });
        }

        // =====================================================================
        // Create Backup & Auto-Download to System
        // =====================================================================
        function createAndDownloadBackup() {
            var btn = $('#btn_create_backup');
            var originalHtm = btn.html();

            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Generating Backup...');

            $.ajax({
                type: "POST",
                url: "api/backup.php",
                data: { action: 'create_backup' },
                dataType: "json",
                success: function(res) {
                    btn.prop('disabled', false).html(originalHtm);

                    if (res.status === 'success') {
                        // 1. Trigger direct browser download to local system
                        $('#iframe_downloader').attr('src', res.download_url);

                        // 2. Notify user with SweetAlert
                        swal({
                            title: "Backup Generated!",
                            text: "Snapshot '" + res.filename + "' (" + res.size_formatted + ") was saved to the server folder and is now downloading to your computer.",
                            type: "success",
                            confirmButtonText: "OK",
                            confirmButtonColor: "#00a862"
                        });

                        // 3. Reload list
                        loadBackupsList();
                    } else {
                        swal("Backup Failed", res.message || "An error occurred while creating the backup.", "error");
                    }
                },
                error: function(xhr, status, error) {
                    btn.prop('disabled', false).html(originalHtm);
                    swal("Error", "Server error while generating backup: " + error, "error");
                }
            });
        }

        // =====================================================================
        // Delete Backup from Server
        // =====================================================================
        function confirmDeleteBackup(filename) {
            swal({
                title: "Delete Backup?",
                text: "Are you sure you want to remove '" + filename + "' from the server folder? This action cannot be undone.",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#ef4444",
                confirmButtonText: "Yes, delete it",
                cancelButtonText: "Cancel",
                closeOnConfirm: false
            }, function() {
                $.ajax({
                    type: "POST",
                    url: "api/backup.php",
                    data: { action: 'delete_backup', filename: filename },
                    dataType: "json",
                    success: function(res) {
                        if (res.status === 'success') {
                            swal("Deleted!", res.message, "success");
                            loadBackupsList();
                        } else {
                            swal("Error", res.message || "Failed to delete backup", "error");
                        }
                    },
                    error: function() {
                        swal("Error", "Server error while deleting backup", "error");
                    }
                });
            });
        }
    </script>
</body>

</html>
