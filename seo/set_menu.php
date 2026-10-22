<?php
session_start();
if (!isset($_SESSION['name'])) {
    header("Location: ../");
    exit();
}
// Administrative Access Only (Restricted from employees)
if (!isset($_SESSION['login_id']) || (int)$_SESSION['login_id'] !== 1) {
    header("Location: dashboard");
    exit();
}
if(isset($_POST['id'])){
    $_SESSION['id']=$_POST['id'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow, noarchive">
    <title>Falcon | Navigation Permissions - Finez SEO</title>

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
        var id = 0;

        $(document).ready(function() {          
            loadData(1);
        });  
        
        function searchMembers(){
            loadData(1);
        }

        function loadData(page) {
            $('#hdn_current_page').val(page);
            $.ajax({               
                type: "POST",
                url: "api/set_menu",
                data: {
                    action: 'load_login_data',
                    page: page, 
                    val: $('#txt_search').val()
                },
                success: function(data) {  
                    var obj = jQuery.parseJSON(data);
                    var totalrows = obj[0].total_rows;
                    var htm = "";
                    
                    htm += "<div class='row'>";
                    htm += "<div class='col-lg-12'>";
                    htm += "<div class='ibox'>";
                    htm += "<div class='ibox-title'>";
                    htm += "<h5><i class='fa fa-cogs' style='color: var(--falcon-primary); margin-right: 8px;'></i> Active User Role Permissions (" + totalrows + ")</h5>";
                    htm += "<div><span class='badge-modern badge-subtle-primary'><i class='fa fa-lock'></i> RBAC</span></div>";
                    htm += "</div>";
                    htm += "<div class='ibox-content'>";
                    htm += "<div class='table-responsive'>";
                    htm += "<table class='table'>";
                    htm += "<thead>";
                    htm += "<tr>";
                    htm += "<th style='width: 35%;'>Member Name</th>";
                    htm += "<th style='width: 40%;'>Username / Account</th>";
                    htm += "<th style='width: 25%; text-align: right;'>Navigation Permissions</th>";
                    htm += "</tr>";
                    htm += "</thead>";
                    htm += "<tbody>";

                    if (!obj[1] || obj[1].length === 0) {
                        htm += "<tr><td colspan='3' style='text-align: center; padding: 48px 20px; color: var(--falcon-gray-600);'>";
                        htm += "<i class='fa fa-user-times' style='font-size: 32px; color: var(--falcon-gray-400); display: block; margin-bottom: 10px;'></i>";
                        htm += "<strong style='font-size: 14px; color: var(--falcon-gray-900);'>No users found</strong>";
                        htm += "</td></tr>";
                    } else {
                        for (var i = 0; i < obj[1].length; i++) {
                            var safeName = obj[1][i].name ? obj[1][i].name.replace(/"/g, '&quot;') : '';
                            var safeUser = obj[1][i].user_name ? obj[1][i].user_name.replace(/"/g, '&quot;') : '';

                            htm += "<tr>";
                            htm += "<td><strong style='color: var(--falcon-gray-1100); font-weight: 600;'><i class='fa fa-user-circle' style='color: var(--falcon-primary); margin-right: 6px;'></i> " + safeName + "</strong></td>";
                            htm += "<td><span class='badge-modern badge-subtle-primary'>" + safeUser + "</span></td>";
                            htm += "<td style='text-align: right;'>";
                            htm += "<button type='button' onclick='showMenuModal(" + obj[1][i].login_id + ");' class='btn-action-edit'><i class='fa fa-pencil-square-o'></i> Edit Permissions</button>";
                            htm += "</td>";
                            htm += "</tr>";
                        }
                    }

                    htm += "</tbody>";
                    htm += "</table>";
                    htm += "</div>"; // table-responsive

                    var htmpage = paginate(totalrows, page);
                    htm += htmpage;

                    htm += "</div>"; // ibox-content
                    htm += "</div>"; // ibox
                    htm += "</div>"; // col-lg-12
                    htm += "</div>"; // row

                    $('#table_members').html(htm);
                },
                error: function(xhr, status, error) {
                    console.log('AJAX error: ', status, error);
                }
            });
            loadMenu();
            load_set_menu();
        }

        function showMenuModal(login_id) {
            fetchMenuDetails(login_id);
            id = login_id;
            document.getElementById('membersModal').style.display = 'block';
        }

        function closeMenuModal() {
            document.getElementById('membersModal').style.display = 'none';
            $("input[name='group']:checkbox").prop('checked', false);
        }

        function load_set_menu(){
            $.ajax({
                type: "POST",
                url: "api/set_menu",
                data: {
                    action: 'load_menu',
                },
                success: function(data) { 
                    var obj = jQuery.parseJSON(data);
                    var htm = "";

                    htm += "<span class='close' onclick='closeMenuModal()'>&times;</span>";
                    htm += "<h4><i class='fa fa-shield' style='color: var(--falcon-primary); margin-right: 8px;'></i> Allowed Menu Items</h4>";
                    htm += "<p style='color: var(--falcon-gray-600); font-size: 0.82rem; margin-bottom: 14px;'>Select which sections this account can view in the navigation sidebar:</p>";
                    
                    if (obj && obj[0]) {
                        for (var i = 0; i < obj[0].length; i++) {
                            htm += "<div class='i-checks members'>";
                            htm += "<label for='" + obj[0][i].menu_id + "'>";
                            htm += "<input type='checkbox' name='group' id='" + obj[0][i].menu_id + "' value='" + obj[0][i].menu_id + "'>";
                            htm += "<span>" + obj[0][i].name + "</span>";
                            htm += "</label>";
                            htm += "</div>";
                        }
                    }
                    htm += "<button type='button' onclick='addMenuToUser();' class='save-button'><i class='fa fa-check'></i> Save Permissions</button>";

                    $('#group_container').html(htm);
                },
                error: function(xhr, status, error) {
                    console.log('AJAX error: ', status, error);
                }
            });
        }

        function addMenuToUser() {
            var menu_ids = [];
            $("input[type=checkbox]:checked").each(function () {
                menu_ids.push(this.value);
            });
            
            $.ajax({               
                type: "POST",
                url: "api/set_menu",
                data: {
                    action: 'save_menu',
                    id: id, 
                    menu_ids: menu_ids
                },
                success: function(data) {  
                    sessionStorage.removeItem('finez_seo_menu');
                    alertsuccess('Permissions Saved Successfully');                
                    closeMenuModal();
                    loadData($('#hdn_current_page').val());                
                },
                error: function(xhr, status, error) {
                    console.log('AJAX error: ', status, error);
                }
            });     
        }

        function fetchMenuDetails(login_id){
            $("input[name='group']:checkbox").prop('checked', false);
            $.ajax({               
                type: "POST",
                url: "api/set_menu",
                data: {
                    action: 'fetch_menu_details',
                    id: login_id,
                },
                success: function(data) {
                    var obj = jQuery.parseJSON(data);
                    if (obj) {
                        for (var i = 0; i < obj.length; i++) {
                            var el = document.getElementById(obj[i].menu_id);
                            if (el) {
                                el.checked = true;
                            }
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.log('AJAX error: ', status, error);
                }
            });
        }
    </script>
    <script src="../assets/js/pagination.js"></script>
    <script src="../assets/js/menu.js?v=4.2"></script>
</head>

<body>
    <?php include '../includes/loader.php'; ?>
    <!-- Hidden fields -->
    <input type="hidden" id="hdn_current_page" value="0">
    <input type="hidden" id="hdn_id" value="0">
    <input type="hidden" id="hdn_file_upload" name="image" value="0">

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
                    <span style="font-size: 14px; font-weight: 700; color: var(--falcon-gray-700);"><i class="fa fa-shield" style="color: var(--falcon-primary); margin-right: 8px;"></i> Role &amp; Navigation Permissions</span>
                </div>

                <div class="topbar-right-section">
                    <span class="live-status-pill hidden-xs"><span class="pulse-dot"></span> In Sync &bull; Live</span>
                    <div class="topbar-user-badge">
                        <img src="../assets/img/customer.png" class="topbar-avatar" alt="Avatar">
                        <span class="topbar-username"><?php echo htmlspecialchars($_SESSION['name'] ?? 'Admin'); ?></span>
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
                <div class="col-sm-7">
                    <span class="page-title-badge"><i class="fa fa-shield"></i> Security &amp; Roles</span>
                    <h2>Navigation Permissions</h2>
                    <p class="page-heading-desc">Configure visible sidebar menu modules and access permissions per user</p>
                </div>
                <div class="col-sm-5" style="display: flex; align-items: center; justify-content: flex-end; gap: 10px; margin-top: 10px;">
                    <div class="modern-search-group">
                        <i class="fa fa-search search-icon"></i>
                        <input type="text" placeholder="Search user accounts..." id="txt_search" name="search" class="form-control modern-search-input" onkeypress="if(event.keyCode==13){searchMembers();return false;}">
                        <button class="modern-search-btn" onclick="searchMembers()" type="button">
                            <span>Search</span>
                        </button>
                    </div>
                </div>
            </div>
            <!-- Page Heading End -->

            <!-- Popup modal for menu permissions -->
            <div id='membersModal' class='modal'>
                <div class='modal-content' id="group_container">
                    <!-- popup data injected by ajax -->
                </div>
            </div>

            <div class="wrapper wrapper-content animated fadeInRight" id="table_members">
                <!-- Data injected by ajax -->
            </div>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="../assets/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
    <script src="../assets/js/inspinia.js"></script>
    <script src="../assets/js/plugins/pace/pace.min.js"></script>
</body>

</html>
