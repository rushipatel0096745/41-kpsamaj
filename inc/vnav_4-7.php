
<?php
if (isset($_GET['member_id'])) {
      $membernamesql = "SELECT surname,full_name,is_agriculture,handicapChecked,matrimonialChecked FROM member_detail WHERE member_id = '".$_GET['member_id']."'";
      $membername = $obj->select($membernamesql);

      $mname = $membername[0]['full_name']." ".$membername[0]['surname'];

}else{
  $mname = $_SESSION['data'][0]['name']." ".$_SESSION['data'][0]['surname'];
}
   
?>
          <nav class="col-md-2  d-md-block bg-light sidebar">
			<div class="side_personal_info">
				<div class="profile-photo"><img src="<?php echo $imgsidebar; ?>" class="img-fluid img-circle" id="profile_photo_view" alt=""> </div> 
					<h4><small>Member ID:</small><?php echo $_SESSION['data'][0]['famiy_no']; ?> </h4>
					<h3><?php echo $mname; ?></h3>
			</div>
			<!-- end side_personal_info -->
			
            <div class="sidebar-sticky">
              <ul class="nav flex-column">
                <li class="nav-item">
                  <a class="nav-link" href="mr-dashboard.php">
                    <span data-feather="users"></span>
                    My Family
                  </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="add-member.php">
                      <span data-feather="users"></span>
                      Add Member
                    </a>
                  </li>
                <li class="nav-item">
                    <a class="nav-link" href="" data-toggle="modal" data-target="#change-password">
                      <span data-feather="users"></span>
                      Change Password
                    </a>
                  </li>
              </ul>
  
              <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
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
                  <a class="nav-link" href="member-occupation.php?member_id=<?php echo $_GET['member_id']; ?>">
                    <span data-feather="file-text"></span>
					Occupation/Business Information
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
                    Agricultural Information
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
				
				<!--
                  <li class="nav-item">
                    <a class="nav-link" href="mr-matrimonial.html">
                      <span data-feather="file-text"></span>
                      Matrimonial
                    </a>
                  </li> 
                  <li class="nav-item">
                    <a class="nav-link" href="mr-disability-info.html">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                      Disability Info
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="mr-achievement.html">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                      Achievement Info
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="mr-event.html">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                      Event Info
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="mr-demises.html">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                      Demises Info
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="mr-emergency.html">
                      <span data-feather="file-text"></span>
                      Emergency Contact
                    </a>
                  </li> -->
              </ul>
              <?php } ?>
            </div>
          </nav>