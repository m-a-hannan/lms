<?php
// Load app configuration and database connection.
require_once dirname(__DIR__, 2) . '/includes/config.php';
include(ROOT_PATH . '/app/includes/connection.php');
?>
<!-- Designation management page layout. -->
<!DOCTYPE html>
<html lang="en">
<?php

// Handle designation form submission.
if (isset($_POST['save'])) {
      // Read the designation name from the form.
      $name = $_POST['designation_name'];

      // Insert the new designation record.
      $sql = "insert into designation_info(designation_name) values ('$name')";
		  $result = $conn->query($sql);
		
      // Redirect after successful insert.
      if ($result == 1) {
        echo "Successfully Inserted!";
        header("Location: designation.php");
      }
}

?>

<head>
  <?php  // Shared header resources for the admin layout. ?>
  <?php  include(ROOT_PATH . '/app/includes/header_resources.php');   ?>
</head>



<body>
  <!-- Page container layout. -->
  <section id="container">

  <?php
  // Top header and navigation bar.
  include(ROOT_PATH . '/app/includes/header.php');

  include(ROOT_PATH . '/app/includes/nav.php');

?>
  

    <!--main content start-->
    <section id="main-content">
      <section class="wrapper">
        <!-- Page title. -->
        <h3> Designation Information</h3>
        <!-- BASIC FORM ELELEMNTS -->
        <div class="row mt">
          <div class="col-lg-12">
            <div class="form-panel">
             
              <!-- Designation creation form. -->
              <form class="form-horizontal style-form" action="designation.php" method="post">
                <div class="form-group">
                  <label class="col-sm-2 col-sm-2 control-label">Designation Name<span style="color:red">*</span></label>
                  <div class="col-sm-6">
                    <input type="text" name="designation_name" class="form-control" required>
                  </div>
                </div>
				
				<div class="form-group">
                  <div class="col-sm-8" align="center">
                    <!-- Submit action. -->
                    <input type="submit" name="save" class="btn btn-info">
                  </div>
                </div>
                
              </form>
            </div>
          </div>
          <!-- col-lg-12-->
        </div>
        
          
      
        <!-- /row -->
      </section>
      <!-- /wrapper -->
    </section>
    <!-- /MAIN CONTENT -->
    <!--main content end-->
    <!--footer start-->
    
    <!--footer end-->
  </section>

<?php   // Shared footer scripts. ?>
<?php   include(ROOT_PATH . '/app/includes/footer_resources.php');  ?>
</body>

</html>
