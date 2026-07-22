

<div class="loader-main" id="loader-main" style="display: none;">
  <div id="loader"></div>
</div>
   <header class="flex-md-nowrap p-0 border-bottom bg-white box-shadow">
      <div class="container">
          <div class="row"> 
                <div class="col-md-6">
                    <a href="https://41kpsamaj-foundation.org/"><img src="assets/images/41kpsmaj-logo.png" alt="" style="max-width:100%;" /></a>
                </div>
				<div class="col-md-6 text-right top-header-link">
					  <ul class="navbar-nav">
						<?php if(isset($_SESSION['data'])){ ?>

						  <li class="nav-item">
							<a class="nav-link" href="index.php?logout=1">Logout</a>
						  </li>
						<?php }else{ ?>
						  <li class="nav-item">
							<a class="nav-link" href="./">Member Login</a>
						  </li>
						  <li class="nav-item">
							<a class="nav-link" href="./family-registration.php">New Member Registration</a>
						  </li>
						  <?php } ?>
						</ul>
				</div>
          </div>
        </div>
        <nav class="navbar navbar-expand-sm navbar-dark bg-orange">
          <div class="container">
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExample02" aria-controls="navbarsExample02" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
    
          <div class="collapse navbar-collapse" id="navbarsExample02">
            <ul class="navbar-nav mr-auto">
              <li class="nav-item">
                <a class="nav-link" href="https://41kpsamaj-foundation.org">Home</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="https://41kpsamaj-foundation.org/index.php/about/">About</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="https://41kpsamaj-foundation.org/index.php/about/">News</a>
              </li>
              <!-- <li class="nav-item">
                <a class="nav-link" href="#">Events</a>
              </li> -->
              <li class="nav-item">
                <a class="nav-link" href="https://41kpsamaj-foundation.org/index.php/gallery/">Gallery</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="https://41kpsamaj-foundation.org/index.php/contact/">Contact</a>
              </li>
            </ul>
         
          </div>
        </div>
        </nav>
     
      </header>
	  
<?php


$total_family = $obj->select("SELECT COUNT(id) FROM `family_register` WHERE status = '1'");
$total_member = $obj->select("SELECT COUNT(member_id) FROM `member_detail` WHERE delete_data='1'");
$total_male = $obj->select("SELECT COUNT(member_id) FROM `member_detail` WHERE gender = 'Male' AND delete_data='1'");
$total_female = $obj->select("SELECT COUNT(member_id) FROM `member_detail` WHERE gender = 'Female' AND delete_data='1'");
?>

	  <div class="registration-filter" style="background-color:#ccccff">
		  <div class="container">
				<div class="row">
					<div class="col-md-2">
						<label>Total Family: </label>
						(<?php echo $total_family[0][0]; ?>)
					</div>
					<div class="col-md-2">
						<label>Member: </label>
						(<?php echo $total_member[0][0]; ?>)
					</div>
					<div class="col-md-2">
						<label>Male: </label>
						(<?php echo $total_male[0][0]; ?>)
					</div>
					<div class="col-md-2">
						<label>Female: </label>
						(<?php echo $total_female[0][0]; ?>)
					</div>
				</div>
		  </div>
	  </div>