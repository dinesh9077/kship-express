<?php include'include/header.php';?>
 <?php if(empty($this->session->userdata('landing_plan'))): ?>
<?php include'include/left_sideber.php';?>
  <?php endif ?>
<?php echo $main_content;?>
<?php include'include/footer.php';?>