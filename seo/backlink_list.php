<?php
session_start();
if (!isset($_SESSION['name'])) {
    header("Location: ../");
    exit();
}
if(isset($_POST['id'])){
    $_SESSION['id']=$_POST['id'];
}
$is_admin = (!empty($_SESSION['login_id']) && (int)$_SESSION['login_id'] === 1);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow, noarchive">
    <title>Falcon | Backlink History - Finez SEO</title>

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
        select.status-dropdown, select#selected_project {
            height: 38px;
            border-radius: 8px;
            border: 1px solid var(--falcon-border-color);
            padding: 0 12px;
            background-color: #ffffff;
            font-size: 12.5px;
            font-weight: 600;
            color: var(--falcon-gray-900);
            min-width: 190px;
            cursor: pointer;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
            transition: var(--transition);
        }
        select.status-dropdown:focus, select#selected_project:focus {
            border-color: var(--falcon-primary);
            box-shadow: 0 0 0 3px rgba(0, 168, 98, 0.15);
            outline: none;
        }
        .modal-backdrop {
            z-index: 2040 !important;
        }
        #newFeesModel {
            z-index: 2050 !important;
            position: fixed !important;
        }
        #newFeesModel .modal-dialog {
            z-index: 2060 !important;
            position: relative !important;
        }
        #newFeesModel .modal-content {
            position: relative !important;
            z-index: 2070 !important;
            background: #ffffff !important;
        }
        #btn_add_backlink {
            cursor: pointer !important;
            user-select: none;
        }
    </style>

    <script>
        var allProjectsList = [];

        $(document).ready(function() {
            loadMenu();
            load_project();
            load_modal_projects();
            load_content_categories();

            $(document).on('click', '#btn_add_backlink', function(e) {
                e.preventDefault();
                e.stopPropagation();
                addBacklinks(e);
            });
        });  

        function load_project() {
            $.ajax({
                type: "POST",
                url: "api/backlink_list",
                data: { action: 'load_project' },
                success: function(data) {
                    $('#status-container').html(data);
                    loadData(1);
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error: ', status, error);
                }
            });
        }

        function load_modal_projects() {
            $.ajax({
                type: "POST",
                url: "api/backlinks",
                data: { action: 'load_all_projects' },
                dataType: "json",
                success: function(res) {
                    if (res.status === 'success') {
                        allProjectsList = res.projects;
                        var modalHtm = "<option value='' disabled selected>Select Client Project</option>";
                        for (var i = 0; i < allProjectsList.length; i++) {
                            var p = allProjectsList[i];
                            modalHtm += "<option value='" + p.id + "'>" + p.name + " (" + p.url + ")</option>";
                        }
                        $('#modal_project_id').html(modalHtm);
                    }
                }
            });
        }

        function load_content_categories() {
            $.ajax({
                type: "POST",
                url: "api/backlinks",
                data: { action: 'load_content' },
                dataType: "json",
                success: function(res) {
                    var items = res[0] || [];
                    var htm = "<label for='selected_content'>Content Category <span class='text-danger'>*</span></label>";
                    htm += "<select id='selected_content' class='form-control' required>";
                    htm += "<option value='' disabled selected>Select Category</option>";
                    for (var i = 0; i < items.length; i++) {
                        htm += "<option value='" + items[i].id + "'>" + items[i].name + "</option>";
                    }
                    htm += "</select>";
                    $('#contents').html(htm);
                }
            });
        }

        function loadData(page) {
            $('#hdn_current_page').val(page);
            if (window.FinezLoader) {
                FinezLoader.cardLoader('#table_client', 'Fetching backlink audit records...');
            }
            $.ajax({               
                type: "POST",
                url: "api/backlink_list",
                data: {
                    action: 'load_data',
                    page: page,
                    project: $("#selected_project").val(),
                },
                success: function(data) {
                    var obj = jQuery.parseJSON(data);
                    var totalrows = obj[0].total_rows;
                    var isAdmin = obj[0].is_admin;
                    var boxTitle = isAdmin ? "All Backlink Submissions (" + totalrows + ")" : "My Backlink History (" + totalrows + ")";
                    var badgeHtm = isAdmin ? "<span class='badge-modern badge-subtle-primary'><i class='fa fa-database'></i> Team Audit Log</span>" : "<span class='badge-modern badge-subtle-success'><i class='fa fa-user-circle'></i> My Submissions</span>";
                    var htm = "";

                    htm += "<div class='row'>";
                    htm += "<div class='col-lg-12'>";
                    htm += "<div class='ibox'>";
                    htm += "<div class='ibox-title'>";
                    htm += "<h5><i class='fa fa-history' style='color: var(--falcon-primary); margin-right: 8px;'></i> " + boxTitle + "</h5>";
                    htm += "<div>" + badgeHtm + "</div>";
                    htm += "</div>";
                    htm += "<div class='ibox-content'>";
                    htm += "<div class='table-responsive'>";
                    htm += "<table class='table table-hover'>";
                    htm += "<thead>";
                    htm += "<tr>";
                    htm += "<th style='width: 15%;'>Date & Time</th>";
                    htm += "<th style='width: 33%;'>Published Backlink URL</th>";
                    htm += "<th style='width: 15%;'>Category / Type</th>";
                    htm += "<th style='width: 12%;'>Username</th>";
                    htm += "<th style='width: 11%;'>Password</th>";
                    htm += "<th style='width: 14%;'>Submitted By</th>";
                    htm += "</tr>";
                    htm += "</thead>";
                    htm += "<tbody>";

                    if (!obj[1] || obj[1].length === 0) {
                        var emptyMsg = isAdmin ? "No submissions logged for this project" : "No backlinks submitted by you yet";
                        var emptySub = isAdmin ? "Select a project from above or click '+ Add Backlink' to publish links." : "Click '+ Add Backlink' to publish your first backlink for this campaign.";
                        htm += "<tr><td colspan='6' style='text-align: center; padding: 48px 20px; color: var(--falcon-gray-600);'>";
                        htm += "<i class='fa fa-history' style='font-size: 36px; color: var(--falcon-gray-400); display: block; margin-bottom: 12px;'></i>";
                        htm += "<strong style='font-size: 15px; color: var(--falcon-gray-900);'>" + emptyMsg + "</strong><br>";
                        htm += "<small>" + emptySub + "</small>";
                        htm += "</td></tr>";
                    } else {
                        for (var i = 0; i < obj[1].length; i++) {
                            var safeLink = obj[1][i].links ? obj[1][i].links.replace(/"/g, '&quot;') : '';
                            var displayLink = safeLink.indexOf('http') === 0 ? safeLink : 'https://' + safeLink;
                            var categoryName = obj[1][i].name || 'General';
                            var usernameVal = obj[1][i].user_name || '-';
                            var passwordVal = obj[1][i].password || '-';
                            var employeeName = obj[1][i].employee || 'Employee';

                            htm += "<tr>";
                            htm += "<td><span class='badge-modern badge-modern-slate'><i class='fa fa-clock-o'></i> " + obj[1][i].date_time + "</span></td>";
                            htm += "<td><a href='" + displayLink + "' target='_blank' class='url-tag' title='" + safeLink + "'><i class='fa fa-external-link'></i> " + safeLink + "</a></td>";
                            htm += "<td><span class='badge-modern badge-subtle-primary'>" + categoryName + "</span></td>";
                            htm += "<td><code style='color: var(--falcon-gray-800); background: var(--falcon-gray-100); padding: 4px 10px; border-radius: 6px; font-size: 0.92rem; font-weight: 600;'>" + usernameVal + "</code></td>";
                            htm += "<td><code style='color: var(--falcon-gray-600); background: var(--falcon-gray-100); padding: 4px 10px; border-radius: 6px; font-size: 0.92rem; font-weight: 600;'>" + passwordVal + "</code></td>";
                            htm += "<td><span class='badge-modern badge-subtle-success'><i class='fa fa-user-circle'></i> " + employeeName + "</span></td>";
                            htm += "</tr>";
                        }
                    }

                    htm += "</tbody>";
                    htm += "</table>";
                    htm += "</div>";

                    var htmpage = paginate(totalrows, page);
                    htm += htmpage;

                    htm += "</div>";
                    htm += "</div>";
                    htm += "</div>";
                    htm += "</div>";

                    $('#table_client').html(htm);
                },
                error: function(xhr, status, error) {
                   console.log('AJAX error: ', status, error);
                }
            });
            loadMenu();
        }

        // Add Backlink Modal Handlers
        function addBacklinks(e) {
            if (e && e.preventDefault) e.preventDefault();
            if (e && e.stopPropagation) e.stopPropagation();
            try {
                var form = document.getElementById('newfees_form');
                if (form) form.reset();
            } catch (err) {
                console.warn(err);
            }
            var currentFilterProj = $('#selected_project').val();
            if (currentFilterProj && currentFilterProj != '0') {
                $('#modal_project_id').val(currentFilterProj);
            }
            $('#newFeesModel').modal('show');
        }

        function closeBacklink() {
            try {
                var form = document.getElementById('newfees_form');
                if (form) form.reset();
            } catch (err) {
                console.warn(err);
            }
            $('#newFeesModel').modal('hide');
        }

        function saveBacklink() {
            var backlinkUrl = $('#txt_links').val().trim();
            if (backlinkUrl === '') {
                alertwarning('Please enter a live backlink URL.');
                return;
            }

            var chosenProj = $('#modal_project_id').val();
            if (!chosenProj) {
                alertwarning('Please select a client project.');
                return;
            }

            var chosenCat = $('#selected_content').val();
            if (!chosenCat || chosenCat == '0') {
                alertwarning('Please select a content category.');
                return;
            }

            swal({
                title: "Save Backlink?",
                text: "Confirm saving backlink submission.",
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
                        action: 'save_backlink',
                        project_id: chosenProj,
                        links: backlinkUrl,
                        user_name: $('#txt_user_name').val().trim(),
                        password: $('#txt_password').val().trim(),
                        content: chosenCat,
                        id: 0,
                        login_id: 0
                    };
                    $.ajax({
                        type: "POST",
                        url: "api/backlinks",
                        data: data,
                        dataType: "json",
                        success: function(response) {
                            closeBacklink();
                            alertsuccess('Saved Successfully');
                            // If current view was filtering another project, switch to this project
                            if ($('#selected_project').val() != chosenProj) {
                                $('#selected_project').val(chosenProj);
                            }
                            loadData(1);
                        },
                        error: function (xhr, status) {
                            var msgObj = { text: "Failed to save backlink." };
                            try { msgObj = JSON.parse(xhr.responseText); } catch(e) {}
                            alerterror(msgObj.message || 'Error saving backlink', xhr);
                        }
                    });
                }
            });
        }
    </script>

    <script src="../assets/js/pagination.js"></script>
    <script src="../assets/js/menu.js?v=4.2"></script>
</head>

<body>
    <?php include '../includes/loader.php'; ?>
    <input type="hidden" id="hdn_current_page" value="1">
    <input type="hidden" id="hdn_id" value="0">
    <input type="hidden" id="hdn_login_id" value="0">

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

            <div class="sidebar-section-label">OPERATIONS CONSOLE</div>

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
                    <span style="font-size: 14px; font-weight: 700; color: var(--falcon-gray-700);"><i class="fa fa-history" style="color: var(--falcon-primary); margin-right: 8px;"></i> <?php echo $is_admin ? 'Global Backlinks Audit Trail' : 'My Backlink History Trail'; ?></span>
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

            <!-- Page Heading Start -->
            <div class="row wrapper page-heading" style="margin: 0 0 20px 0;">
                <div class="col-md-5 col-sm-12">
                    <span class="page-title-badge"><i class="fa fa-history"></i> <?php echo $is_admin ? 'Global Audit Trail' : 'My Submission Trail'; ?></span>
                    <h2><?php echo $is_admin ? 'Backlink History Log' : 'My Backlink History'; ?></h2>
                    <p class="page-heading-desc"><?php echo $is_admin ? 'Filter and monitor all team backlink submissions across active projects' : 'View and audit all backlinks submitted by your account'; ?></p>
                </div>
                <div class="col-md-7 col-sm-12" style="display: flex; align-items: center; justify-content: flex-end; gap: 12px; margin-top: 10px; flex-wrap: wrap;">
                    <div style="display: flex; align-items: center; gap: 6px;">
                        <label style="margin: 0; font-size: 12px; font-weight: 700; color: var(--falcon-gray-700);"><i class="fa fa-filter text-primary"></i> Project:</label>
                        <div id="status-container"></div>
                    </div>
                    <a href="backlinks" class="btn btn-white" style="height: 38px; font-size: 13px; font-weight: 600; padding: 0 14px; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap;">
                        <i class="fa fa-link text-primary"></i> Backlinks Tracker
                    </a>
                    <button type="button" id="btn_add_backlink" class="btn btn-primary" onclick="addBacklinks(event);" style="height: 38px; font-size: 13px; font-weight: 700; padding: 0 16px; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; cursor: pointer; position: relative; z-index: 5;">
                        <i class="fa fa-plus"></i> Add Backlink
                    </button>
                </div>
            </div>
            <!-- Page Heading End -->

            <!-- View Switcher Tabs -->
            <div class="row wrapper" style="margin: 0 0 15px 0;">
                <div class="col-xs-12" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                    <div style="display: flex; gap: 8px;">
                        <a href="backlinks" class="btn btn-white btn-sm" style="font-weight: 600; border-radius: 6px;"><i class="fa fa-link text-primary"></i> <?php echo $is_admin ? 'Backlinks Tracker' : 'My Backlinks'; ?></a>
                        <a href="backlink_list" class="btn btn-primary btn-sm" style="font-weight: 700; border-radius: 6px;"><i class="fa fa-history"></i> <?php echo $is_admin ? 'Global Backlink History' : 'My Backlink History'; ?></a>
                    </div>
                </div>
            </div>
            
            <div class="wrapper wrapper-content animated fadeInRight" id="table_client">
                <!-- data injected Dynamically via ajax -->
            </div>
        </div>
    </div>

    <!-- ==================================================================== -->
    <!-- Modal: Add Backlink                                                  -->
    <!-- ==================================================================== -->
    <div class="modal inmodal" id="newFeesModel" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content animated fadeIn">
                <div class="modal-header" style="padding: 16px 20px;">
                    <button type="button" class="close" data-dismiss="modal" onclick="closeBacklink();"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" style="font-size: 1.25rem;"><i class="fa fa-link" style="color: var(--falcon-primary); margin-right: 6px;"></i> Add Backlink</h4>
                </div>
                <form method="POST" id="newfees_form" onsubmit="event.preventDefault(); saveBacklink();">
                    <div class="modal-body" style="padding: 20px 24px;">
                        
                        <!-- Project Select -->
                        <div class="form-group">
                            <label for="modal_project_id">Client Project / Website <span class="text-danger">*</span></label>
                            <select id="modal_project_id" name="project_id" class="form-control" required></select>
                        </div>

                        <!-- Live Backlink URL -->
                        <div class="form-group">
                            <label for="txt_links">Live Backlink URL <span class="text-danger">*</span></label>
                            <input type="url" id="txt_links" name="links" placeholder="e.g. https://medium.com/@user/article" class="form-control" required>
                        </div>

                        <!-- Category Dropdown -->
                        <div id="contents" class="form-group"></div>

                        <!-- Credentials Row -->
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="txt_user_name">Platform Username</label>
                                    <input type="text" id="txt_user_name" name="user_name" placeholder="User ID / Handle" class="form-control">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="txt_password">Platform Password</label>
                                    <input type="text" id="txt_password" name="password" placeholder="Account Password" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer" style="padding: 14px 24px;">
                        <button type="button" class="btn btn-white" onclick="closeBacklink();">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="saveBacklink();"><i class="fa fa-check"></i> Save Backlink</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal End -->

    <!-- Scripts -->
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="../assets/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
    <script src="../assets/js/inspinia.js"></script>
    <script src="../assets/js/plugins/pace/pace.min.js"></script>
</body>

</html>
