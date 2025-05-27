<?php 
require_once 'core/dbconfig.php';
require_once 'core/models.php'; 

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Accounts</title>

<!-- Bootstrap CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">

<!-- Javascript -->
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>

	<?php include 'include/navbar.php'; ?>
</head>
<body class="bg-light">

 <div class="container-fluid">
    	<div class="row justify-content-center">
    		<div class="col-md-12">
    			<div class="card shadow mt-4 p-4">
    				<div class="card-header"><h2>All Accounts</h2></div>
    				<div class="card-body">
    					<table class="table">
						  <thead>
						    <tr>
						      <th scope="col">Username</th>
						      <th scope="col">FirstName</th>
						      <th scope="col">LastName</th>
						      <th scope="col">Date Registered</th>
						      <th scope="col">Admin Status</th>
						      <th scope="col">Account Status</th>
						      <th scope="col">Suspend</th>
						    </tr>
						  </thead>
						  <tbody>
						  	<?php $getAllAccounts = getAllAccounts($pdo); ?>
						  	<?php foreach ($getAllAccounts as $row) {?>
						    <tr>
						      <td><?php echo $row['username']; ?></td>
						      <td><?php echo $row['first_name']; ?></td>
						      <td><?php echo $row['last_name']; ?></td>
						      <td><?php echo $row['date_added']; ?></td>
						      <input type="hidden" class="user_id" value="<?php echo $row['user_id']; ?>">
						      <td>
						      	<?php
						      	if ($row['is_admin'] == '1') {
						      		echo "<span>Admin</span>";
						      	}
						      	else {
						      		echo "<span>User</span>";
						      	}  
						      	?>
						      		
						      	</td>
						      <td>
						      	<?php 
						      		if ($row['is_suspended'] == null || $row['is_suspended'] == '0') {
						      			echo "<span class='text-success'>Active</span>";
						      		}
						      		else {
						      			echo "<span class='text-danger'><strong>Suspended</strong></span>";
						      		}
						      	?>
						      </td>
						      <td>
						      	<input type="checkbox" class="suspendCheckbox" data-userid="<?php echo $row['user_id']; ?>" <?php echo ($row['is_suspended'] == '1') ? 'checked' : ''; ?>>
						      </td>
						    </tr>
						  	<?php } ?>
						  </tbody>
						</table>
    				</div>
    			</div>
    		</div>
    	</div>
    </div>
    

	<!-- javascripts -->
  	<script>

	$(document).ready(function () {
		$('.suspendCheckbox').change(function () {
			let userId = $(this).data('userid');
			let isChecked = $(this).is(':checked');
			let action = isChecked ? "Suspend" : "Unsuspend";

			$.ajax({
				type: "POST",
				url: "core/handleForms.php",
				data: {
					suspend_or_unsuspend: action,
					user_id: userId,
					suspendOrUnspendUser: 1
				},
				success: function (data) {
					location.reload();
				}
			});
		});
	});

    </script>

</body>
</html>