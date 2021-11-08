<!DOCTYPE html>
<html>
<head>
    <title>Customer Login</title>
    <link rel="stylesheet" type="text/css" href="<?=asset_url('assets/ext/jquery-ui/jquery-ui.min.css')?>">
    <link rel="stylesheet" type="text/css" href="<?=asset_url('assets/ext/bootstrap/css/bootstrap.min.css')?>">
    <link rel="stylesheet" type="text/css" href="<?=asset_url('assets/css/login.css')?>">
    <link rel="stylesheet" type="text/css" href="<?=asset_url('assets/css/general.css')?>">
    <link rel="icon" type="image/x-icon" href="<?=asset_url('assets/img/favicon.ico')?>">
    <link rel="icon" sizes="192x192" href="<?=asset_url('assets/img/logo.png')?>">
</head>

<body>
    <div id="login-frame" class="container">
        <br />
        <h2 align="center">Customer Login</h2>
        <br />
        <div class="panel panel-default">
            <div class="panel-body">
                <?php
if ($this->session->flashdata('message')) {
    echo '
                    <div class="alert alert-success">
                        ' . $this->session->flashdata("message") . '
                    </div>
                    ';
}
session_destroy();
?>
                <form method="post" action="<?php echo base_url(); ?>index.php/login/validation">
                    <div class="form-group">
                        <label>Enter Email Address</label>
                        <input type="text" name="user_email" class="form-control" value="<?php echo set_value('user_email'); ?>" />
                        <span class="text-danger"><?php echo form_error('user_email'); ?></span>
                    </div>
                    <div class="form-group">
                        <label>Enter Password</label>
                        <input type="password" name="user_password" class="form-control" value="<?php echo set_value('user_password'); ?>" />
                        <span class="text-danger"><?php echo form_error('user_password'); ?></span>
                    </div>
                    <div class="form-group">
                        <input type="submit" name="login" value="Login" class="btn btn-info" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo base_url(); ?>index.php/register">Register</a>
                    </div>

                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                </form>
            </div>
        </div>
    </div>
</body>
</html>