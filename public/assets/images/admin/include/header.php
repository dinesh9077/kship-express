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

        <link rel="stylesheet" href="<?php echo base_url() ?>assets/admin/css/admin_style.css?var=<?php echo settings()->version ?>&time=<?=time();?>"> 
        <!-- Theme style -->
		<?php if(is_admin()): ?> 
			<link rel="stylesheet" href="<?php echo base_url() ?>assets/admin/css/skins/theme_<?php echo settings()->theme ?>.css">   
			<?php if (text_dir() == 'rtl'): ?> 
			<?php if (settings()->theme == 1): ?>  
				<link rel="stylesheet" href="<?php echo base_url() ?>assets/admin/css/custom-rtl.css"> 
			<?php else: ?> 
				<link rel="stylesheet" href="<?php echo base_url() ?>	assets/admin/css/custom-rtl-dark.css"> 
			<?php endif ?> 
			<link rel="stylesheet" href="<?php echo base_url()?>assets/admin/css/bootstrap-rtl.min.css" crossorigin="anonymous"> 
			<?php endif ?> 
		<?php else: ?> 
			<link rel="stylesheet" href="<?php echo base_url() ?>assets/admin/css/skins/theme_<?php echo $this->business->mode ?>.css">   
			<?php if (text_dir() == 'rtl'): ?> 
			<?php if ($this->business->mode == 1): ?>  
				<link rel="stylesheet" href="<?php echo base_url() ?>assets/admin/css/custom-rtl.css"> 
			<?php else: ?> 
				<link rel="stylesheet" href="<?php echo base_url() ?>	assets/admin/css/custom-rtl-dark.css"> 
			<?php endif ?> 
			<link rel="stylesheet" href="<?php echo base_url()?>assets/admin/css/bootstrap-rtl.min.css" crossorigin="anonymous"> 
			<?php endif ?> 
		<?php endif ?> 
        <!-- end theme style -->
        
        <!-- DataTables -->
        <link href="<?php echo base_url() ?>assets/admin/js/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        
        <link href="<?php echo base_url() ?>assets/admin/css/bootstrap-datepicker.min.css" rel="stylesheet">
        <link href="<?php echo base_url() ?>assets/admin/css/icons.css" rel="stylesheet">
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/front/css/simple-line-icons.css">
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/front/font/flaticon.css">
        <link href="<?php echo base_url() ?>assets/admin/css/bootstrap-switch.min.css" rel="stylesheet">
        <link href="<?php echo base_url() ?>assets/admin/css/select2.min.css" rel="stylesheet" />
        <link href="<?php echo base_url() ?>assets/admin/css/themify.min.css" rel="stylesheet" />
        <link href="<?php echo base_url() ?>assets/admin/css/bootstrap4-toggle.min.css" rel="stylesheet" />
        <link href="<?php echo base_url() ?>assets/admin/css/summernote.css" rel="stylesheet" />
        <link href="<?php echo base_url() ?>assets/admin/css/custom.css" rel="stylesheet" />
        
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans&display=swap" rel="stylesheet">
        
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        
        <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@200;300;400;600;700;800;900&display=swap" rel="stylesheet">
        
        <meta name="description" content="<?php echo html_escape($settings->description) ?>" />
        <meta name="keywords" content="<?php echo html_escape($settings->keywords) ?>" />
        <meta property="og:type" content="Accounting services" />
        <meta property="og:title" content="<?php echo html_escape($settings->site_name) ?> - <?php echo html_escape($settings->site_title) ?>" />
        <meta property="og:description" content="<?php echo html_escape($settings->description) ?>" />
        <meta property="og:url" content="https://accountieons.com/" />
        <meta property="og:site_name" content="Accountieons" />
        <meta property="og:image" content="https://accountieons.com/uploads/medium/acc_medium-400x81.png" />
        
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
			.highcharts-credits {
				display: none;
			}
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
            <header class="main-header">
                <?php if(empty($this->session->userdata('landing_plan'))): ?>

                <?php if (is_admin()): ?>
                <a target="_blank" href="<?php echo base_url() ?>" class="switch_businesss logo text-centers">
                    <span class="logo-lg">
                        <img width="40px" class="mr-5" src="<?php echo base_url($settings->favicon) ?>" alt="<?php echo html_escape($settings->site_name); ?>"> <span>Your Accountieons account</span>
                    </span>
                </a>
                <?php else: ?>
                <?php if (!is_admin() && auth('role') != 'viewer'){ ?> 
				 <a href="#" class="switch_business logo text-centers">
                    <span class="logo-lg">
                        <img width="40px" src="<?php echo (!empty(user()->image))?base_url(user()->image):base_url("assets/images/avatar.png"); ?>" alt="<?php echo html_escape($settings->site_name); ?>" style="width: 45px; height: 45px; border-radius: 50%"> 
                        <span><?php echo html_escape($this->business->name); ?> </span>
                    </span> 
                    <span class="buss-arrow pull-right"><i class="icon-arrow-right"></i></span>
                </a>
				<?php }else{ ?>
                <a href="#" class="switch_business logo text-centers">
                    <span class="logo-lg">
                        <img width="40px" src="<?php echo base_url($settings->favicon) ?>" alt="<?php echo html_escape($settings->site_name); ?>" style="width: 45px; height: 45px; border-radius: 50%"> 
                        <span><?php echo html_escape($this->business->name); ?> </span>
                        
                    </span> 
                    <span class="buss-arrow pull-right"><i class="icon-arrow-right"></i></span>
                </a>
                <?php } ?>
                
                <div class="business_switch_panel animate-ltr" style="display: none;">
                    <div class="buss_switch_panel_header align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <img width="30px" src="<?php echo base_url($settings->favicon) ?>" alt="<?php echo html_escape($settings->site_name); ?>"> 
                            <span class="acc">Your Accountieons account</span>
                        </div>
                        <span class="business_close pull-<?php echo($settings->dir == 'rtl') ? 'left' : 'right'; ?>">×</span>
                    </div>
                    
                    <div class="buss_switch_panel_body">
                        <ul class="switcher_business_menu pb-20">
                            <?php foreach (get_my_business() as $mybuss): ?>
                            <li class="business_menu_item <?php if($this->business->uid == $mybuss->uid){echo "default";} ?>">
                                <a class="business_menu_item_link" href="<?php echo base_url('admin/profile/switch_business/'.$mybuss->uid) ?>">
                                    <span class="business-menu_item_label">
                                        <?php echo $mybuss->name ?>
                                        <?php if ($this->business->uid == $mybuss->uid): ?>
                                        <span class="is_default pull-right"><i class="flaticon-checked text-success"></i></span>
                                        <?php endif ?>
                                    </span>
                                </a>
                            </li>
                            <?php endforeach ?>
                            <div class="mt-15">
                            <span style="color: black"><i class="icon-plus"></i> </span><a href="<?php echo base_url('admin/business'); ?>" class="view_link">Create a new business</a>
                            </div>
                        </ul>
                        
                        
                        
                        <div class="switcher_business_menu my-15 pb-20 ">
                            <p>You're signed in as <strong> <?php echo auth('email'); //html_escape($user->email); ?></strong></p>
                           
                            <?php if (auth('role') == 'user' || auth('role') == 'subadmin'): ?>
                            <a class="new_business_link" href="<?php echo base_url('admin/business') ?>"><i class="icon-briefcase"></i> <span><?php echo trans('manage-business') ?></span></a>
                            
                            <a class="new_business_link" href="<?php echo base_url('admin/role_management') ?>"><i class="icon-people"></i> <span><?php echo trans('manage-users') ?></span></a>
                            
                            <a class="new_business_link" href="<?php echo base_url('admin/business/invoice_customize') ?>"><i class="fa fa-paint-brush"></i> <span><?php echo trans('invoice-customization') ?></span></a>
                            <?php endif; ?>
                            
                            <a class="new_business_link" href="<?php echo base_url('admin/profile') ?>"><i class="flaticon-user-1"></i> <span><?php echo trans('manage-profile') ?></span></a>
                            
                            <a class="new_business_link" href="<?php echo base_url('auth/logout') ?>"><i class="icon-logout"></i> <span><?php echo trans('sign-out') ?></span></a>
                        </div>
                        
                        <a class="view_link" href="<?php echo base_url('terms') ?>" style="color: #4d6575; font-weight: normal !important">Terms</a>&nbsp;<span style="opacity:0.3">•</span>&nbsp;<a class="view_link" href="<?php echo base_url('privacy') ?>" style="color: #4d6575; font-weight: normal !important">Privacy</a>
                    </div>
                    
                    <div class="buss_switch_panel_footer">
                        
                    </div>
                </div>
                <?php endif; ?>
                 <nav class="navbar navbar-static-top hidden-md">
                    <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                        <span class="sr-only">Toggle navigation</span> 
                    </a>
                </nav>
                <?php else: ?>
                  <a href="#" class="logo text-centers" style="background:transparent;"> </a>
                
           
                <?php endif ?>
            </header>
            <?php endif; ?>
            
            
                