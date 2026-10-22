<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if(isset($_POST['action']) && !empty($_POST['action'])) {
        $action = $_POST['action'];

    // action start
    if($action=="load_menu_data"){ 
        try{
            // $sql = "SELECT m.name,m.parent_id,m.id,m.nav_url FROM tbl_mail_mgr_menu m
            // JOIN tbl_mail_mgr_client_menu_map cm ON m.id = cm.menu_id
            // WHERE cm.login_id = " . $_SESSION['login_id'];

        $sql ="SELECT qry1.menu_id as menu_id,qry1.menu_name as menu_name,qry1.nav_url as nav_url,qry1.menu_parent_id as 
        menu_parent_id,qry1.menu_level as menu_level,qry1.sub_menu as sub_menu,qry1.icon AS icon FROM (SELECT b.id as menu_id,b.name as menu_name,b.nav_url as 
        nav_url, b.parent_id as menu_parent_id,b.menu_level as menu_level,b.sub_menu as sub_menu,b.icon FROM tbl_seo_menu b LEFT OUTER JOIN 
        tbl_seo_menu as a on b.parent_id=a.id ) AS qry1 INNER JOIN (select menu_id from 
        tbl_seo_menu_map where login_id='" . $_SESSION['login_id'] . "') as c on qry1.menu_id=c.menu_id 
        WHERE qry1.nav_url NOT IN ('set_menu', 'set_menu.php')
        order by
        FIELD(qry1.nav_url, 'projects', 'backlinks', 'backlink_list', 'tickets', 'members', 'backup', 'projects.php', 'backlinks.php', 'backlink_list.php', 'tickets.php', 'members.php', 'backup.php'),
        qry1.menu_parent_id,qry1.menu_id,qry1.menu_level";

      

            $result =  app_exec_query($sql);
            if ($result && $result->num_rows > 0) {
                    $nav_url = '';
                    $menu_name = '';
                    $menu_parent_id = '';
                    $menu_id = '';
                    $menu_level = '';
                    $sub_menu = '';
                    $prev_menu_parent_id = 0;
                    $sub_menu_counter=0;
                    $sub_menu_count_holder=0;
                    $stringval='';
                    $stringval.="<ul class=\"nav metismenu\" id=\"side-menu\">";
                    $stringval.="<li><a href=\"dashboard\"><i class=\"fa fa-th-large\"></i> <span class=\"nav-label\">Dashboard</span></a></li>";
                // Fetch all rows as an associative array
                while ($row = $result->fetch_assoc()) {
                    
                    $nav_url = preg_replace('/\.php$/', '', $row['nav_url']);
                    if ($nav_url === 'set_menu') {
                        continue;
                    }
                    $menu_name = $row['menu_name'];
                    if ($nav_url === 'members' || strtolower($menu_name) === 'members') {
                        $menu_name = 'Employees';
                    }
                    $menu_parent_id = $row['menu_parent_id'];
                    $menu_id = $row['menu_id'];
                    $menu_level = $row['menu_level'];
                    $sub_menu = $row['sub_menu'];
                    $icon = $row['icon'];
                    if($sub_menu>0){
                        $sub_menu_count_holder=$sub_menu;
                    }
                    
                   

                    // Condition Verified
                    if($menu_level == 1 && $sub_menu == 0){
                        $stringval.="<li><a href=\"$nav_url\"><i class=\"$icon\"></i> <span class=\"nav-label\">$menu_name</span></a></li>";
                    }
                    
                    if($sub_menu_count_holder>0){                       
                        If($menu_level == 1 && $sub_menu > 0 ){                            
                            $stringval.="<li>";
                            $stringval.="<a href=\"$nav_url\"><i class=\"$icon\"></i> <span class=\"nav-label\">$menu_name</span><span class=\"fa arrow\"></span></a>";                                                       
                        }       
                        If($menu_level==2 && $sub_menu_counter==0 && $prev_menu_parent_id == $menu_parent_id){
                            //echo "ul start sub_menu_counter ul start". $sub_menu_counter ."prev_menu_parent_id" . $prev_menu_parent_id. "menu_parent_id".$menu_parent_id;
                            $stringval.="<ul class=\"nav nav-second-level collapse\">";
                            $sub_menu_counter++;
                        }
                        if($sub_menu_counter>0){
                            $stringval.="<li><a href=\"$nav_url\"><i class=\"$icon\"></i>$menu_name</a></li>";
                        }
                        $sub_menu_count_holder--; 
                        if($sub_menu_count_holder==0){
                            //echo "ul end sub_menu_counter  ". $sub_menu_counter ."prev_menu_parent_id" . $prev_menu_parent_id. "menu_parent_id".$menu_parent_id;
                            $stringval.="</ul>";
                            $stringval.="</li>";
                            $sub_menu_counter=0;
                            $sub_menu_count_holder=0;
                        }
                        //$sub_menu_counter++;    
                    }    
                    
                 
                      
                    $prev_menu_parent_id=$menu_parent_id;
                
                   
                                     
                   

                    // echo"<li>";
                    // echo"<a href='$nav_url'><i class='fa fa-desktop'></i> <span class='nav-label'>$name</span><span class='fa arrow'></span></a>";
                    // echo"<ul class='nav nav-second-level collapse'>";
                    // echo"<li><a href='subnav_url'>sub_name</a></li>";
                    // echo"</ul>";
                    // echo"</li>";
                }
                $stringval.="</ul>";
                //array();
                //echo json_encode("response_html"=>$stringval);
                echo $stringval;
            } 
        }
        catch (Throwable $e) {
            throw new Exception('Oops! Something went wrong.');
        }
    }
}         
        
?>