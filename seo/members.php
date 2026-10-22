<?php
session_start();
// Administrative Access Only (Restricted from employees)
if (!isset($_SESSION['login_id']) || (int)$_SESSION['login_id'] !== 1) {
    header("Location: dashboard");
    exit();
}

if (!isset($_SESSION['name'])) {
    header("Location: ../");
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
    <title>Falcon | Employees - Finez SEO</title>

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
    <link rel="stylesheet" href="../assets/css/plugins/cropper/cropper.min.css">
    
    <script>
        $(document).ready(function() {          
            loadData(1);
        });  
        
        function searchMembers(){
            loadData(1);
        }

        function loadData(page) {
            $('#hdn_current_page').val(page);
            if (window.FinezLoader) {
                FinezLoader.cardLoader('#table_members', 'Loading employee directory...');
            }
            $.ajax({               
                type: "POST",
                url: "api/members",
                data: {
                    action: 'load_members_data',
                    page: page, 
                    val: $('#txt_search').val()
                },
                success: function(data) {  
                    var obj = jQuery.parseJSON(data);
                    var totalrows = obj[0].total_rows;
                    
                    // Update KPI counter
                    $('#kpi_total_members').text(totalrows);
                    
                    var htm = "";
                    htm += "<div class='row'>";
                    if (!obj[1] || obj[1].length === 0) {
                        htm += "<div class='col-lg-12'>";
                        htm += "<div class='ibox' style='padding: 60px 20px; text-align: center; color: var(--falcon-gray-600);'>";
                        htm += "<i class='fa fa-users' style='font-size: 42px; color: var(--falcon-gray-400); display: block; margin-bottom: 14px;'></i>";
                        htm += "<strong style='font-size: 16px; color: var(--falcon-gray-900);'>No employees found</strong><br><small style='font-size: 14px;'>Click '+ Add Employee' above to create a new profile.</small>";
                        htm += "</div></div>";
                    } else {
                        for (var i = 0; i < obj[1].length; i++) {
                            var member = obj[1][i];
                            var fullName = (member.first_name + " " + (member.middle_name || "") + " " + (member.last_name || "")).trim();
                            var safeFirst = member.first_name ? member.first_name.replace(/"/g, '&quot;') : '';
                            var safeMiddle = member.middle_name ? member.middle_name.replace(/"/g, '&quot;') : '';
                            var safeLast = member.last_name ? member.last_name.replace(/"/g, '&quot;') : '';
                            var safeUser = member.user_name ? member.user_name.replace(/"/g, '&quot;') : '';
                            var safePhone = member.phone ? member.phone.replace(/"/g, '&quot;') : '';

                            var initial = (safeFirst && safeFirst.length > 0) ? safeFirst.charAt(0).toUpperCase() : 'U';
                            var bgIndex = (member.id) ? (member.id % 5) : (i % 5);

                            htm += "<div class='col-lg-3 col-md-4 col-sm-6'>";
                            htm += "<div class='falcon-member-card'>";
                            
                            htm += "<div class='member-card-header gradient-" + bgIndex + "'>";
                            htm += "<span class='member-role-pill'><i class='fa fa-star' style='color: var(--falcon-yellow); margin-right: 4px;'></i> Specialist</span>";
                            htm += "</div>";

                            htm += "<div class='member-card-body'>";
                            htm += "<div class='member-avatar-box'>";
                            htm += "<div class='member-initial-avatar avatar-bg-" + bgIndex + "'>" + initial + "</div>";
                            htm += "<span class='member-status-dot online' title='Active Specialist'></span>";
                            htm += "</div>";

                            htm += "<h4 class='member-card-name' title='" + fullName + "'>" + fullName + "</h4>";
                            htm += "<div class='member-user-handle'><span class='badge-modern badge-subtle-primary'><i class='fa fa-user-circle'></i> @" + safeUser + "</span></div>";

                            htm += "<div class='member-card-meta'>";
                            if (safePhone) {
                                htm += "<div class='member-meta-item'><i class='fa fa-phone' style='color: var(--falcon-green);'></i> <strong style='color: var(--falcon-gray-900);'>" + safePhone + "</strong></div>";
                            } else {
                                htm += "<div class='member-meta-item' style='color: var(--falcon-gray-500);'><i class='fa fa-phone-square'></i> No phone listed</div>";
                            }
                            htm += "<div class='member-meta-item'><i class='fa fa-id-badge' style='color: var(--falcon-primary);'></i> Specialist ID: #" + member.id + "</div>";
                            htm += "</div>"; // member-card-meta

                            htm += "</div>"; // member-card-body

                            htm += "<div class='member-card-footer'>";
                            if (safePhone) {
                                htm += "<a href='tel:" + safePhone + "' class='btn-member-action btn-member-call' title='Call Specialist'><i class='fa fa-phone'></i></a>";
                            }
                            htm += "<button type='button' onclick='fetchmemberDetails(" + member.id + ",\"" + safeFirst + "\",\"" + safeMiddle + "\",\"" + safeLast + "\",\"" + safeUser + "\",\"" + safePhone + "\")' class='btn-member-action btn-member-edit' title='Edit Profile'><i class='fa fa-pencil'></i> Edit</button>";
                            htm += "<button type='button' onclick='popuCreateLogin(\"" + safeFirst + "\",\"" + safeUser + "\")' class='btn-member-action btn-member-login' title='Manage Access'><i class='fa fa-key'></i> Access</button>";
                            htm += "</div>"; // member-card-footer

                            htm += "</div>"; // falcon-member-card
                            htm += "</div>"; // col-lg-3
                        }
                    }
                    htm += "</div>"; // row

                    var htmpage = paginate(totalrows, page);
                    htm += htmpage;

                    $('#table_members').html(htm);
                },
                error: function(xhr, status, error) {
                    console.log('AJAX error: ', status, error);
                }
            });
            loadMenu();
        }

        function popuCreateLogin(name, user_name) { 
            $("#name").val(name);
            $("#user_name").val(user_name);
            $('#loginModal').modal('show');
        }

        function saveLogin() { 
            swal({
                title: "Save Login Credentials?",
                text: "Confirm creating or updating login access for this user.",
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
                    if ($('#password').val() !== $('#confirmpassword').val()) {
                        alertwarning('Passwords do not match!');
                        return;
                    }
                    var data = {
                        action: 'save_login',
                        name: $('#name').val(),
                        user_name: $('#user_name').val(),
                        password: $('#password').val(),
                        confirmPassword: $('#confirmpassword').val(),
                    };
                    $.ajax({
                        type: "POST",
                        url: "api/members",
                        data: data,
                        success: function(response) {
                            closePopupLogin();
                            alertsuccess('Saved Successfully');
                        },
                        error: function (xhr, status){
                            var msgObj = JSON.parse(xhr.responseText);
                            alerterror(msgObj, xhr);
                            $('#login_form')[0].reset();
                        }
                    });
                }
            }); 
        }

        function closePopupLogin(){
            $('#login_form')[0].reset();
            $('#loginModal').modal('hide');
        }

        function fetchmemberDetails(id, first_name, middle_name, last_name, user_name, phone){
            $("#txt_first_name").val(first_name);
            $("#txt_middle_name").val(middle_name);
            $("#txt_last_name").val(last_name);
            $("#txt_phone").val(phone);
            $("#txt_user_name").val(user_name);
            $("#hdn_id").val(id);
            $("#modal_member_title").text("Edit Employee Profile");
            $('#clientModal').modal('show');
        }

        function popupMemberDetails(id) {
            $("#member_form")[0].reset();
            $("#hdn_id").val(id);
            $("#modal_member_title").text("Add New Employee");
            $('#clientModal').modal('show');
        }

        function saveMembers() { 
            const first_name = $('#txt_first_name').val().trim();
            const user_name = $('#txt_user_name').val().trim();
           
            if (first_name === "") {
                alertinfo("First Name cannot be empty.");
                return;
            }

            if (user_name === "") {
                alertinfo("Username cannot be empty.");
                return;
            }

            swal({
                title: "Save Employee Profile?",
                text: "Confirm saving employee record.",
                type: "info",
                showCancelButton: true,
                confirmButtonColor: "#00a862",
                confirmButtonText: "Yes, Save",
                cancelButtonText: "Cancel",
                closeOnConfirm: false,
                closeOnCancel: true
            },
            function (isConfirm) {
                if (isConfirm){
                    var data = {
                        action: 'save_members',
                        first_name: $('#txt_first_name').val(),
                        middle_name: $('#txt_middle_name').val(),
                        last_name: $('#txt_last_name').val(),
                        phone: $('#txt_phone').val(),
                        user_name: $('#txt_user_name').val(),
                        id: $("#hdn_id").val(),
                    };
                    $.ajax({
                        type: "POST",
                        url: "api/members",
                        data: data,
                        success: function(response) {
                            closepopupMemberDetails();
                            alertsuccess('Saved Successfully');
                            loadData($('#hdn_current_page').val());
                        },
                        error: function (xhr, status){
                            var msgObj = JSON.parse(xhr.responseText);
                            alerterror(msgObj, xhr);
                            $('#member_form')[0].reset();
                        }
                    });
                }
            }); 
        }

        function closepopupMemberDetails(){
            $("hdn_id").val(0);
            $('#member_form')[0].reset();
            $('#clientModal').modal('hide');
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
                    <span style="font-size: 14px; font-weight: 700; color: var(--falcon-gray-700);"><i class="fa fa-users" style="color: var(--falcon-primary); margin-right: 8px;"></i> Employees Directory</span>
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
                    <span class="page-title-badge"><i class="fa fa-users"></i> Employees Directory</span>
                    <h2>Employees</h2>
                    <p class="page-heading-desc">Manage company employees, link builders, and system access accounts</p>
                </div>
                <div class="col-sm-5" style="display: flex; align-items: center; justify-content: flex-end; gap: 12px; margin-top: 10px;">
                    <div class="modern-search-group">
                        <i class="fa fa-search search-icon"></i>
                        <input type="text" placeholder="Search employees..." id="txt_search" name="search" class="form-control modern-search-input" onkeypress="if(event.keyCode==13){searchMembers();return false;}">
                        <button class="modern-search-btn" onclick="searchMembers()" type="button">
                            <span>Search</span>
                        </button>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="popupMemberDetails('0')" style="height: 40px; font-size: 13px; font-weight: 700; padding: 0 16px; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap;">
                        <i class="fa fa-user-plus"></i> Add Employee
                    </button>
                </div>
            </div>
            <!-- Page Heading End -->

            <!-- Falcon CRM KPI Row -->
            <div class="wrapper wrapper-content" style="padding-bottom: 0 !important;">
                <div class="row crm-kpi-row">
                    <div class="col-sm-4">
                        <div class="crm-kpi-card">
                            <div class="crm-kpi-info">
                                <span class="crm-kpi-label">Active Employees</span>
                                <span class="crm-kpi-value" id="kpi_total_members">--</span>
                                <span class="crm-kpi-subtext"><span style="color: var(--falcon-green); font-weight: 700;"><i class="fa fa-check-circle"></i> Active</span> specialists</span>
                            </div>
                            <div class="crm-kpi-icon primary">
                                <i class="fa fa-users"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="crm-kpi-card">
                            <div class="crm-kpi-info">
                                <span class="crm-kpi-label">Login Authentication</span>
                                <span class="crm-kpi-value" style="font-size: 1.65rem; font-weight: 800; color: var(--falcon-gray-1000);">Secured Access</span>
                                <span class="crm-kpi-subtext"><span style="color: var(--falcon-cyan); font-weight: 700;"><i class="fa fa-shield"></i> Protected</span> credentials</span>
                            </div>
                            <div class="crm-kpi-icon info">
                                <i class="fa fa-shield"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="crm-kpi-card">
                            <div class="crm-kpi-info">
                                <span class="crm-kpi-label">Department</span>
                                <span class="crm-kpi-value" style="font-size: 1.65rem; font-weight: 800; color: var(--falcon-gray-1000);">Link Building</span>
                                <span class="crm-kpi-subtext"><span style="color: var(--falcon-warning); font-weight: 700;"><i class="fa fa-link"></i> Backlinks</span> &amp; Outreach</span>
                            </div>
                            <div class="crm-kpi-icon warning">
                                <i class="fa fa-line-chart"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Falcon CRM KPI Row End -->

            <!-- Modal for Add/Edit Member -->
            <div class="modal inmodal" id="clientModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content animated fadeIn">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" onclick="closepopupMemberDetails();"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                            <h4 class="modal-title" id="modal_member_title"><i class="fa fa-user-plus" style="color: var(--falcon-primary); margin-right: 8px;"></i> Employee Profile</h4>
                        </div>
                        <form method="POST" id="member_form" enctype="multipart/form-data">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label for="txt_first_name">First Name <span style="color: var(--falcon-danger);">*</span></label>
                                        <input type="text" id="txt_first_name" name="first_name" placeholder="First Name" class="form-control" required>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="txt_middle_name">Middle Name</label>
                                        <input type="text" id="txt_middle_name" name="middle_name" placeholder="Middle Name (optional)" class="form-control">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label for="txt_last_name">Last Name</label>
                                        <input type="text" id="txt_last_name" name="last_name" placeholder="Last Name" class="form-control">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="txt_phone">Phone Number</label>
                                        <input type="text" id="txt_phone" name="phone" placeholder="e.g. +1 555-0123" class="form-control">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="txt_user_name">Username / Handle <span style="color: var(--falcon-danger);">*</span></label>
                                    <input type="text" id="txt_user_name" name="user_name" placeholder="e.g. john.doe" class="form-control" required>
                                </div>
                            </div>
                         
                            <div class="modal-footer">
                                <button type="button" class="btn btn-white" onclick="closepopupMemberDetails();">Cancel</button>
                                <button type="button" class="btn btn-primary" onclick="saveMembers();"><i class="fa fa-check"></i> Save Employee</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div> 
            <!-- Modal End -->

            <!-- Modal for Login Setup -->
            <div class="modal inmodal" id="loginModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content animated fadeIn">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" onclick="closePopupLogin();"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                            <h4 class="modal-title"><i class="fa fa-key" style="color: var(--falcon-warning); margin-right: 8px;"></i> Employee Login Access</h4>
                        </div>
                        <form method="POST" id="login_form">
                            <div class="modal-body">
                                <p style="font-size: 14.5px; color: var(--falcon-gray-600); margin-bottom: 18px;">
                                    Configure or update credentials for this employee to log in to the SEO CRM Suite.
                                </p>
                                <div class="form-group">
                                    <label for="name">Display Name</label>
                                    <input type="text" id="name" name="name" placeholder="Employee Name" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="user_name">Username</label>
                                    <input type="text" id="user_name" name="user_name" placeholder="Username" class="form-control" readonly style="background: var(--falcon-gray-100); font-weight: 600;">
                                </div>
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label for="password">Password</label>
                                        <input type="password" id="password" name="password" placeholder="••••••••" class="form-control" required>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="confirmpassword">Confirm Password</label>
                                        <input type="password" id="confirmpassword" name="confirmpassword" placeholder="••••••••" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-white" onclick="closePopupLogin();">Cancel</button>
                                <button type="button" class="btn btn-primary" onclick="saveLogin();"><i class="fa fa-check"></i> Save Login</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Modal End -->

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
