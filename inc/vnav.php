
<?php
if (isset($_GET['member_id'])) {
      $membernamesql = "SELECT surname,full_name,is_agriculture,handicapChecked,matrimonialChecked FROM member_detail WHERE member_id = '".$_GET['member_id']."'";
      $membername = $obj->select($membernamesql);

      $mname = $membername[0]['full_name']." ".$membername[0]['surname'];

}else{
  $mname = $_SESSION['data'][0]['name']." ".$_SESSION['data'][0]['surname'];
}
   
?>
          <nav class="col-md-2  bg-light d-md-block sidebar">
			<div class="side_personal_info">
				<div class="headContent_area">
					<div class="profile-photo"><img src="<?php echo $imgsidebar; ?>" class="img-fluid img-circle" id="profile_photo_view" alt=""> </div> 
					<h4><small>Member ID:</small><?php echo $_SESSION['data'][0]['famiy_no']; ?> </h4>
					<h3><?php echo $mname; ?></h3>
					 <ul class="nav flex-column">
						<li class="nav-item">
						  <a class="nav-link" href="mr-dashboard.php">
							<span data-feather="users"></span>
							My Family
						  </a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="add-member.php">
							  <span data-feather="user-plus"></span>
							  Add New Member
							</a>
						  </li>
						<li class="nav-item">
							<a class="nav-link" href="" data-toggle="modal" data-target="#change-password">
							  <span data-feather="key"></span>
							  Change Password
							</a>
						  </li>
					  </ul>
				</div>
				<!-- end headContent_area -->
			</div>
			<!-- end side_personal_info -->
			
            <div class="sidebar-sticky">
				<div class="quickLinks_content">
				  <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mb-1"> 
					<span>Quick Links</span>
				  </h6>
				  <?php
				  if (isset($_GET['member_id'])) {
					  $is_agriculture='style="display: none;"';
					  $is_matromonial='style="display: none;"';
					  $is_disability='style="display: none;"';
					  if($membername[0]['is_agriculture']=='Yes'){
						  $is_agriculture='style="display: block;"';
					  }
					  if($membername[0]['matrimonialChecked']=='Yes'){
						  $is_matromonial='style="display: block;"';
					  }
					  if($membername[0]['handicapChecked']=='Yes'){
						  $is_disability='style="display: block;"';
					  }
				  ?>
					<ul class="nav flex-column mb-2">
						<li class="nav-item">
						  <a class="nav-link" href="add-member.php?member_id=<?php echo $_GET['member_id']; ?>">
							<span data-feather="file-text"></span>
							Personal Information
						  </a>
						</li>
						
						<li class="nav-item">
						  <a class="nav-link" href="education.php?member_id=<?php echo $_GET['member_id']; ?>">
							<span data-feather="file-text"></span>
							Educational Information
						  </a>
						</li>
						
						<li class="nav-item">
						  <a class="nav-link" href="health_information.php?member_id=<?php echo $_GET['member_id']; ?>">
							<span data-feather="file-text"></span>
							Health Information
						  </a>
						</li>
						
										
						<li class="nav-item" <?php echo $is_agriculture;?>>
						  <a class="nav-link" href="agricultural.php?member_id=<?php echo $_GET['member_id']; ?>">
							<span data-feather="file-text"></span>
							Agriculture Information
						  </a>
						</li>	
						
						
						<li class="nav-item">
						  <a class="nav-link" href="member-occupation.php?member_id=<?php echo $_GET['member_id']; ?>">
							<span data-feather="file-text"></span>
							Occupation/Business Information
						  </a>
						</li>
						
						<li class="nav-item" <?php echo $is_matromonial;?>>
						  <a class="nav-link" href="matrimonial.php?member_id=<?php echo $_GET['member_id']; ?>">
							<span data-feather="file-text"></span>
							Matrimonial Information
							</a>
						</li>
						
						<li class="nav-item" <?php echo $is_disability;?>>
						  <a class="nav-link" href="disability.php?member_id=<?php echo $_GET['member_id']; ?>">
							<span data-feather="file-text"></span>
							Disability Information
						  </a>
						</li>
				  </ul>
				  <?php } ?>
				</div>
				<!-- end quickLinks_content -->
			</div>
    </nav>