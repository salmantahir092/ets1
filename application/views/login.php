<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="<?php echo base_url(); ?>assets/css/style.css" rel='stylesheet' type='text/css'/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Employee Tracking System</title>
    <meta name="description" content="">
    <meta name="author" content="templatemo">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,400italic,700' rel='stylesheet'
          type='text/css'>
    <link href="<?php echo base_url(); ?>assets/css/font-awesome.min.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/templatemo-style.css" rel="stylesheet">
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
</head>
<body>
<!-----start-main---->
<div class="main">
    <div class="login-form">
        <h1><b>Welcome To ETS</b></h1>

        <div class="head">
            <img src="<?php echo base_url(); ?>assets/images/logo.png" alt=""/>
        </div>
        <form action="index" class="templatemo-login-form" method="POST">
            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-user fa-fw"></i></div>
                    <input name="email" type="email" class="form-control" placeholder="someone@example.com"
                           required="true">
                </div>
            </div>
            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-key fa-fw"></i></div>
                    <input name="password" type="password" class="form-control" placeholder="••••••" required="true">
                </div>
            </div>

            <div class="form-group">
                <button type="submit" class="templatemo-green-button width-100">Login</button>
            </div>
            <div class="form-group">
                <p><a href="forgot_pass">Having trouble ?</a></p>
            </div>
            <div><?php
                if (isset($_GET['invalid'])) {
                    echo 'Access denied!';
                } else
                    echo $response; ?></div>
        </form>
    </div>
    <!--//End-login-form-->
    <!-----start-copyright---->
    <div class="copy-right">
        <p>Copyright &copy; 2016-2017 <a href="#">ETS Team </a>. All rights
            reserved .</p>
    </div>
    <!-----//end-copyright---->
</div>
<!-----//end-main---->
</body>
</html>