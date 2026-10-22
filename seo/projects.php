<?php
session_start();
if (!isset($_SESSION['name'])) {
    header("Location: ../index.php");
    exit();
}
$is_admin = (!empty($_SESSION['login_id']) && (int)$_SESSION['login_id'] === 1);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Falcon | SEO Projects - Finez SEO</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap" rel="stylesheet">

    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="../assets/css/animate.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="../assets/css/modern-theme.css?v=3.5" rel="stylesheet">
    <script src="../assets/js/finez-loader.js"></script>

    <script src="../assets/js/jquery-3.6.0.min.js"></script>
    <link href="../assets/css/plugins/sweetalert/sweetalert.css" rel="stylesheet">
    <script src="../assets/js/plugins/sweetalert/sweetalert.min.js"></script>
    <script src="../assets/js/sweetalert-finez.js"></script>

    <script>
        $(document).ready(function() {          
            loadData(1);
        });

        function searchMembers(){
            loadData(1);
        }

        // Function to load project details
        function loadData(page) {
            $('#hdn_current_page').val(page);
            if (window.FinezLoader) {
                FinezLoader.cardLoader('#table_client', 'Fetching SEO projects...');
            }
            $.ajax({               
                type: "POST",
                url: "api/projects.php",
                data: {
                    action: 'load_data',
                    page: page, 
                    val: $('#txt_search').val(),
                },
                success: function(data) {
                    var obj = jQuery.parseJSON(data);
                    var totalrows = obj[0].total_rows;
                    
                    // Update KPI counter
                    $('#kpi_total_projects').text(totalrows);

                    var htm = "";
                    htm += "<div class='row'>";
                    htm += "<div class='col-lg-12'>";
                    htm += "<div class='ibox'>";
                    htm += "<div class='ibox-title'>";
                    htm += "<h5><i class='fa fa-folder-open' style='color: var(--falcon-primary); margin-right: 8px;'></i> All Active Projects (" + totalrows + ")</h5>";
                    htm += "<div><span class='badge-modern badge-subtle-primary'><i class='fa fa-check-circle'></i> In Sync</span></div>";
                    htm += "</div>";
                    htm += "<div class='ibox-content'>";
                    htm += "<div class='table-responsive'>";
                    htm += "<table class='table'>";
                    htm += "<thead>";
                    htm += "<tr>";
                    htm += "<th style='width: 35%;'>Project Name</th>";
                    htm += "<th style='width: 40%;'>Target Website URL</th>";
                    htm += "<th style='width: 25%; text-align: right;'>Actions</th>";
                    htm += "</tr>";
                    htm += "</thead>";
                    htm += "<tbody>";

                    if (!obj[1] || obj[1].length === 0) {
                        htm += "<tr><td colspan='3' style='text-align: center; padding: 48px 20px; color: var(--falcon-gray-600);'>";
                        htm += "<i class='fa fa-folder-o' style='font-size: 32px; color: var(--falcon-gray-400); display: block; margin-bottom: 10px;'></i>";
                        htm += "<strong style='font-size: 14px; color: var(--falcon-gray-900);'>No projects found</strong><br><small>Click 'Add Project' to create your first SEO campaign.</small>";
                        htm += "</td></tr>";
                    } else {
                        for (var i = 0; i < obj[1].length; i++) {
                            var safeName = obj[1][i].name.replace(/"/g, '&quot;');
                            var safeUrl = obj[1][i].url.replace(/"/g, '&quot;');
                            var displayUrl = obj[1][i].url.indexOf('http') === 0 ? obj[1][i].url : 'https://' + obj[1][i].url;

                            htm += "<tr>";
                            htm += "<td>";
                            htm += "<div style='display: flex; align-items: center; gap: 10px;'>";
                            htm += "<div style='width: 32px; height: 32px; border-radius: 8px; background: var(--falcon-primary-bg-subtle); color: var(--falcon-primary); display: flex; align-items: center; justify-content: center; font-size: 13px; flex-shrink: 0;'><i class='fa fa-globe'></i></div>";
                            htm += "<div>";
                            htm += "<strong style='color: var(--falcon-gray-1100); font-weight: 800; font-size: 0.92rem; display: block; line-height: 1.2;'>" + safeName + "</strong>";
                            htm += "<span style='color: var(--falcon-gray-500); font-size: 0.72rem; font-weight: 600;'>ID #" + obj[1][i].id + " &bull; Active Campaign</span>";
                            htm += "</div>";
                            htm += "</div>";
                            htm += "</td>";
                            htm += "<td><a href='" + displayUrl + "' target='_blank' class='url-tag' title='" + displayUrl + "'><i class='fa fa-external-link'></i> " + safeUrl + "</a></td>";
                            htm += "<td style='text-align: right;'>";
                            var isAdmin = <?php echo $is_admin ? 'true' : 'false'; ?>;
                            htm += "<div class='btn-action-group'>";
                            if (isAdmin) {
                                htm += "<button type='button' class='btn-action-edit' onclick='editProject(" + obj[1][i].id + ",\"" + safeName + "\",\"" + safeUrl + "\");'><i class='fa fa-pencil'></i> Edit</button>";
                            }
                            htm += "<button type='button' class='btn-action-backlink' onclick='addBacklinks(" + obj[1][i].id + ",\"" + safeName + "\");'><i class='fa fa-link'></i> Backlinks</button>";
                            htm += "</div>";
                            htm += "</td>";
                            htm += "</tr>";
                        }
                    }

                    htm += "</tbody>";
                    htm += "</table>";
                    htm += "</div>"; // table-responsive

                    // Append pagination
                    var htmpage = paginate(totalrows, page);
                    htm += htmpage;

                    htm += "</div>"; // ibox-content
                    htm += "</div>"; // ibox
                    htm += "</div>"; // col-lg-12
                    htm += "</div>"; // row

                    $('#table_client').html(htm);
                },
                error: function(xhr, status, error) {
                    console.log('AJAX error: ', status, error);
                }
            });
            loadMenu();
        }

        function editProject(id, name, url) {
            $("#txt_name").val(name);
            $("#txt_url").val(url);
            $("#hdn_id").val(id);
            $("#modal_project_title").text("Edit Project");
            $('#newProjectModal').modal('show');
        }

        function addProject() {
            $('#newfees_form')[0].reset();
            $("#hdn_id").val(0);
            $("#modal_project_title").text("Add New Project");
            $('#newProjectModal').modal('show');
        }

        function closeProject() {
            $('#newfees_form')[0].reset();
            $("#hdn_id").val(0);
            $('#newProjectModal').modal('hide');
        }

        function saveProject() {
            if ($('#txt_name').val().trim() === '') {
                alertwarning('Please enter a project name.');
                return;
            }

            swal({
                title: "Save Project?",
                text: "Confirm changes to project records.",
                type: "info",
                showCancelButton: true,
                confirmButtonColor: "#00a862",
                confirmButtonText: "Yes, Save",
                cancelButtonText: "Cancel",
                closeOnConfirm: false,
                closeOnCancel: true
            },
            function (isConfirm) {
                if (isConfirm) {
                    var data = {
                        action: 'save_projects',
                        name: $('#txt_name').val(), 
                        url: $("#txt_url").val(),  
                        id: $("#hdn_id").val(), 
                    };
                    $.ajax({
                        type: "POST",
                        url: "api/projects.php",
                        data: data,
                        success: function(response) {
                            closeProject();
                            alertsuccess('Saved Successfully');
                            loadData($('#hdn_current_page').val());
                        },
                        error: function (xhr, status){
                            var msgObj = JSON.parse(xhr.responseText);
                            alerterror(msgObj, xhr);
                            $('#newfees_form')[0].reset();
                            $("#hdn_id").val(0);
                        }
                    });
                }
            });
        }

        function addBacklinks(id, websitename) { 
            $.post("backlinks.php", { 'id': id, 'websitename': websitename })
            .done(function(response) {
                window.location.href = "backlinks.php";
            });
        }
    </script>

    <script src="../assets/js/pagination.js"></script>
    <script src="../assets/js/menu.js?v=4.2"></script>
</head>

<body>
    <?php include '../includes/loader.php'; ?>
    <input type="hidden" id="hdn_current_page" value="0">
    <input type="hidden" id="hdn_id" value="0">

    <div id="wrapper">

        <!-- Falcon Vertical Navbar (Sidebar) -->
        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="brand-header">
                <div class="brand-logo-icon">
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
            <!-- Top Header Start (Clean Falcon Layout) -->
            <div class="falcon-topbar">
                <div style="display: flex; align-items: center; gap: 14px;">
                    <a class="navbar-minimalize minimalize-styl-2" href="#"><i class="fa fa-bars"></i> </a>
                    <span style="font-size: 14px; font-weight: 700; color: var(--falcon-gray-700);"><i class="fa fa-folder-open" style="color: var(--falcon-primary); margin-right: 8px;"></i> SEO Projects &amp; Campaigns</span>
                </div>

                <div class="topbar-right-section">
                    <span class="live-status-pill hidden-xs"><span class="pulse-dot"></span> In Sync &bull; Live</span>
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

            <!-- Falcon CRM Page Heading Start -->
            <div class="row wrapper page-heading" style="margin: 0 0 20px 0;">
                <div class="col-sm-7">
                    <span class="page-title-badge"><i class="fa fa-tasks"></i> Project Management</span>
                    <h2>SEO Projects</h2>
                    <p class="page-heading-desc">Manage client websites, root domains, and link building targets</p>
                </div>
                <div class="col-sm-5" style="display: flex; align-items: center; justify-content: flex-end; gap: 12px; margin-top: 10px;">
                    <div class="modern-search-group">
                        <i class="fa fa-search search-icon"></i>
                        <input type="text" placeholder="Search projects..." id="txt_search" name="search" class="form-control modern-search-input" onkeypress="if(event.keyCode==13){searchMembers();return false;}">
                        <button class="modern-search-btn" onclick="searchMembers()" type="button">
                            <span>Search</span>
                        </button>
                    </div>
                    <?php if ($is_admin): ?>
                    <button type="button" class="btn btn-primary" onclick="addProject(0)" style="height: 40px; font-size: 13px; font-weight: 700; padding: 0 16px; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap;">
                        <i class="fa fa-plus"></i> Add Project
                    </button>
                    <?php endif; ?>

                </div>
            </div>
            <!-- Page Heading End -->

            <!-- Falcon CRM KPI Row -->
            <div class="wrapper wrapper-content" style="padding-bottom: 0 !important;">
                <div class="row crm-kpi-row">
                    <div class="col-sm-4">
                        <div class="crm-kpi-card">
                            <div class="crm-kpi-info">
                                <span class="crm-kpi-label">Active Projects</span>
                                <span class="crm-kpi-value" id="kpi_total_projects">--</span>
                            </div>
                            <div class="crm-kpi-icon primary">
                                <i class="fa fa-folder-open"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="crm-kpi-card">
                            <div class="crm-kpi-info">
                                <span class="crm-kpi-label">Campaign Type</span>
                                <span class="crm-kpi-value" style="font-size: 1.35rem; font-weight: 800; color: var(--falcon-gray-1000);">Organic SEO</span>
                            </div>
                            <div class="crm-kpi-icon success">
                                <i class="fa fa-line-chart"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="crm-kpi-card">
                            <div class="crm-kpi-info">
                                <span class="crm-kpi-label">System Health</span>
                                <span class="crm-kpi-value" style="font-size: 1.35rem; font-weight: 800; color: var(--falcon-green);">100% Operational</span>
                            </div>
                            <div class="crm-kpi-icon info">
                                <i class="fa fa-heartbeat"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Content -->
            <div class="wrapper wrapper-content animated fadeInRight" id="table_client">
                <!-- data injected Dynamically via ajax -->
            </div>
        </div>
        
        <?php if ($is_admin): ?>
        <!-- Modal for Add/Edit Project -->
        <div class="modal inmodal" id="newProjectModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content animated fadeIn">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" onclick="closeProject();"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <h4 class="modal-title" id="modal_project_title"><i class="fa fa-folder-open" style="color: var(--falcon-primary); margin-right: 6px;"></i> Add Project</h4>
                    </div>
                    <form method="POST" id="newfees_form">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="txt_name">Project / Website Name</label>
                                <input type="text" id="txt_name" name="name" placeholder="e.g. Finez Technologies" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="txt_url">Website URL</label>
                                <input type="text" id="txt_url" name="url" placeholder="e.g. https://www.fineztechnologies.com" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-white" onclick="closeProject();">Cancel</button>
                            <button type="button" class="btn btn-primary" onclick="saveProject();"><i class="fa fa-check"></i> Save Project</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Modal End -->
        <?php endif; ?>

    </div>

    <!-- Scripts -->
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="../assets/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
    <script src="../assets/js/inspinia.js"></script>
    <script src="../assets/js/plugins/pace/pace.min.js"></script>
</body>

</html>
