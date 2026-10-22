<?php
session_start();
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {    
  include '../app_common/db_connect.php';

    try{
        $sql = "SELECT login_id,name FROM tbl_seo_login WHERE user_name = ? AND password = ?";    
        $types="ss";
        $parameters = [
        $_POST['user_name'],
        $_POST['password']];
        
        $result=app_exec_getresult($sql,$parameters,$types);
    
        while ($row = $result->fetch_assoc()) {
        $_SESSION['name'] = $row['name'];
        $_SESSION['user_name'] = $_POST['user_name'];
        $_SESSION['id']= $row["login_id"];
        $_SESSION['login_id']= $row["login_id"];
        
       
        }
        
        if ($result->num_rows > 0) {
            header("Location: ../seo/dashboard.php");
            exit();
        } else {
            header("Location: ../index.php?error=invalid");
            exit();
        }

    }

    // catch (mysqli_sql_exception $e) {
       
    //     throw new Exception('Oops! Something went wrong.');
    // }

    // catch (MySQLException $e) {
       
    //     throw new Exception('Oops! Something went wrong.');
    // }
    
    // catch (Exception $e){
    //     throw new Exception('Oops! Something went wrong.');
    // }

    // catch (\Illuminate\Database\QueryException $e){
    //     throw new Exception('Oops! Something went wrong.');
    // }

    // catch (Exception $e) {
    //     http_response_code(500);   
    //     echo "Oops! Something went wrong." . $e->getMessage();
    //     return;
    // }
    
    // catch(QueryException $qe){
    //     throw new Exception('Oops! Something went wrong.');
    // }

    // catch(ServerException $ex){
    //     throw new Exception('Oops! Something went wrong.');
    // }

    // catch(DatabaseException $ex){
    //     throw new Exception('Oops! Something went wrong.');
    // }

    // catch (PDOException $e) {
    //     throw new Exception('Oops! Something went wrong.');
    // }

    // catch (mysqli_sql_exception $e) {
    //     throw new Exception('Oops! Something went wrong.');
    // }

    catch (Throwable $e) {
        throw new Exception('Oops! Something went wrong.');
        
    }
    
}
?>

<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin | Login</title>

    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../font-awesome/css/font-awesome.css" rel="stylesheet">

    <link href="../css/animate.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
    </script>

</head>

<body class="gray-bg">

    <div class="middle-box text-center loginscreen animated fadeInDown">
        <div>
            <p>Login in. To see it in action.</p>
            <form class="m-t" role="form"action="../index.php" method="POST">
                <p  style="color: red">Invalid Usernaem or Password</p>
                <form action="../index.php" method="POST"> 
                    <button type="submit" class="btn btn-primary block full-width m-b"><?phpheader("Location: ../index.php");?>retry</button>
                </form>
                <!-- <a href="forgot_password.php"><small>Forgot password?</small></a> -->
            </form>
        </div>
    </div>

    <!-- Mainly scripts -->
    <script src="../js/jquery-3.6.0.min.js"></script>   
    <script src="../js/bootstrap.min.js"></script>

</body>

</html>



