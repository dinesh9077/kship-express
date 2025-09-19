<?php include'include/header_2.php';?>
<?php if(empty($this->session->userdata('landing_plan'))): ?>
<div class="menu">
    <?php include'include/left_sideber_2.php';?>
</div>
<?php endif ?>

<?php echo $main_content;?>
<?php include'include/footer.php';?>