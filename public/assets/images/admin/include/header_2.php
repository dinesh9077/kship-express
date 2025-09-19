<!DOCTYPE html>
<?php $settings = get_settings(); ?>

<html lang="en" dir="<?php echo($settings->dir); ?>">
    <head>
        
        <?php $user = get_logged_user($this->session->userdata('id')); ?>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <?php
        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $root_url = explode("/",$_SERVER['REQUEST_URI']);
        
        $rooturl = $root_url[2]."-".$root_url[3];
        
        switch ($rooturl) {
          case "invoice-details":
            echo "";
            break;
          case "estimate-details":
            echo "";
            break;
          case "bills-details":
            echo "";
            break;
          case "payment-online":
            echo "";
            break;
          default:
            echo '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">';
        }
        ?>

        
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="<?php echo base_url($settings->favicon) ?>">
        
        <title><?php echo html_escape($settings->site_name); ?> &bull; <?php if(isset($this->business->name)){echo html_escape($this->business->name).' &bull;';} ?> <?php if(isset($page_title)){echo html_escape($page_title);}else{echo "Dashboard";} ?></title>
        
        <!-- Bootstrap 4.0-->
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/admin/css/bootstrap.min.css">
        <!-- Bootstrap 4.0-->
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/admin/css/bootstrap-extend.css">
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/admin/css/font-awesome.min.css">
        <link href="<?php echo base_url() ?>assets/admin/css/toast.css" rel="stylesheet" />
        <link href="<?php echo base_url() ?>assets/admin/css/bootstrap-tagsinput.css" rel="stylesheet" />
        <link href="<?php echo base_url() ?>assets/admin/css/sweet-alert.css" rel="stylesheet" />
        <link href="<?php echo base_url() ?>assets/admin/css/animate.min.css" rel="stylesheet" />
        <!-- DataTables -->
        <link href="<?php echo base_url() ?>assets/admin/js/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        
        <!-- Theme style -->
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/admin/css/admin_style.css?var=<?php echo settings()->version ?>&time=<?=time();?>">
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/admin/css/skins/theme_<?php echo settings()->theme ?>.css">   
        
        <?php if (text_dir() == 'rtl'): ?>
        <?php if (settings()->theme == 1): ?>
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/admin/css/custom-rtl.css">
        <?php else: ?>
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/admin/css/custom-rtl-dark.css">
        <?php endif ?>
        <link rel="stylesheet" href="<?php echo base_url()?>assets/admin/css/bootstrap-rtl.min.css" crossorigin="anonymous">
        <?php endif ?>
        
        
        <link href="<?php echo base_url() ?>assets/admin/css/bootstrap-datepicker.min.css" rel="stylesheet">
        <link href="<?php echo base_url() ?>assets/admin/css/icons.css" rel="stylesheet">
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/front/css/simple-line-icons.css">
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/front/font/flaticon.css">
        <link href="<?php echo base_url() ?>assets/admin/css/bootstrap-switch.min.css" rel="stylesheet">
        <link href="<?php echo base_url() ?>assets/admin/css/select2.min.css" rel="stylesheet" />
        <link href="<?php echo base_url() ?>assets/admin/css/themify.min.css" rel="stylesheet" />
        <link href="<?php echo base_url() ?>assets/admin/css/bootstrap4-toggle.min.css" rel="stylesheet" />
        <link href="<?php echo base_url() ?>assets/admin/css/summernote.css" rel="stylesheet" />
        
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@200;300;400;600;700;800;900&display=swap" rel="stylesheet">
        
        
<style>
.content-wrapper {
     margin-top: 0px; 
}
    .menu__toggler {
  position: absolute;
  top: 1%;
  left: 20px;
  z-index: 999;
  height: 28px;
  width: 28px;
  outline: none;
  cursor: pointer;
  display: flex;
  align-items: center;
}
.menu__toggler span,
.menu__toggler span::before,
.menu__toggler span::after {
  position: absolute;
  content: "";
  width: 28px;
  height: 2.5px;
  background: #FE8B43;
  border-radius: 20px;
  transition: 500ms cubic-bezier(0.77, 0, 0.175, 1);
}
.menu__toggler span::before {
  top: -8px;
}
.menu__toggler span::after {
  top: 8px;
}
.menu__toggler.active > span {
  background: transparent;
}
.menu__toggler.active > span::before, .menu__toggler.active > span::after {
  background: #fe8b43;
  top: 0px;
}
.menu__toggler.active > span::before {
  transform: rotate(-225deg);
}
.menu__toggler.active > span::after {
  transform: rotate(225deg);
}
.menu {
  position: absolute;
  left: -30%;
  z-index: 998;
  height: 100%;
  padding-top: 2%;
  flex-direction: column;
  justify-content: center;
  transition: 300ms left cubic-bezier(0.77, 0, 0.175, 1);
}
.main-sidebar {
    background: #EBEFF4;
    position: absolute;
    border-right: 2px solid #eee;
    top: 0;
    left: 0;
    padding-top: 40%;
    min-height: 100%;
    width: 262px;
    z-index: 810;
}

.menu.active {
  left: 0;
}
.menu p {
  font-size: 1.4rem;
  margin-bottom: 1rem;
}
.content-wrapper, .main-footer{
    margin-left: 0px;
    margin-right: 0px;
}

@media only screen and(max-width: 479px) {
  .menu {
    width: 250px;
    left: -250px;
    padding-top: 5%;
  }
  .menu.active {
    left: 0;
    padding-top: 4%;
}
}

</style>
        
        <style type="text/css">
            .radio input[type="radio"],
            .radio-inline input[type="radio"],
            .checkbox input[type="checkbox"],
            .checkbox-inline input[type="checkbox"] {
            margin-right: -20px !important;
            }
            
            <?php if (auth('role') == 'viewer'): ?>
            a.on-default {
            display: none;
            }
            .add_btn{
            display: none;
            }
            .btn {
            display: none;
            }
            .hide_viewer{
            display: none;
            }
            <?php endif ?>
        </style>
        
        <!-- Color picker plugins css -->
        <link href="<?php echo base_url() ?>assets/admin/plugins/mjolnic-bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css" rel="stylesheet">
        
        <script type="text/javascript">
            var csrf_token = '<?php echo $this->security->get_csrf_hash(); ?>';
            var token_name = '<?php echo $this->security->get_csrf_token_name();?>'
        </script>
        
        
    </head>
    
    <body class="hold-transition skin-blue-light sidebar-mini">
        
        <!-- Preloader -->
        <div class="preloader">
            <div class="container text-center"><div class="spinner-llg"></div></div>
        </div>
        <!-- Preloader -->
        
        <!-- Site wrapper -->
        <div class="wrapper">
            
            <?php if (isset($page_title) && $page_title != 'Online Payment'): ?>
            
            <?php endif; ?>
            
            
                