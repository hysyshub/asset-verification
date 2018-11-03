<?php 
session_start();

// Quote variable to make safe
function quote_smart($value)
{
    // Strip HTML & PHP tags & convert all applicable characters to HTML entities
    $value = trim(htmlentities(strip_tags($value)));    

    // Stripslashes
    if ( get_magic_quotes_gpc() )
    {
        $value = stripslashes( $value );
    }
    // Quote if not a number or a numeric string
    if ( !is_numeric( $value ) )
    {
         $value = pg_escape_string($value);
    }
    return $value;
}

if($_SESSION['user']=='')
{
	header('Location: login.php');
	exit;
}
else
{
	//error_reporting(0);
	date_default_timezone_set('Asia/Calcutta');
	include 'php/sessioncheck.php';

?>
<html>
<head>
<title>Edit Circle</title>

</head>
<body>
<?php

include 'header.php';

?>
<!-- Page Content start -->
        <div id="content" style="overflow: auto;">

            <nav class="navbar navbar-expand-lg navbar-light bg-light" style="width:100%">
                <div class="container-fluid">

                    <button type="button" id="sidebarCollapse" class="btn btn-info" style='background:#030dcf;'>
                        <i class="fas fa-align-left"></i>
                        
                    </button>
                    <button class="btn btn-dark d-inline-block d-lg-none ml-auto" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="fas fa-align-justify"></i>
                    </button>

                    <div class="collapse navbar-collapse pull-right" id="navbarSupportedContent">
                        <ul class="nav navbar-nav ml-auto">
                        	<li class="nav-item">
                                <a href="circleinfo.php" style="color:blue;text-align:right;" class="nav-link">Circles</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>  
        <div  class="col-md-12">
            <div  class="col-md-3">
            </div>
            <div  class="col-md-6">
        <h3>Edit Circle</h3>
            <?php
				$circleinfoid = quote_smart($_GET['circleinfoid']);

				if (!is_numeric($circleinfoid))
				{
					echo "ERROR : Invalid parameter value";
					exit;
				}

				$query = "SELECT * FROM circleinfo  WHERE circleinfoid='$circleinfoid'";

				$result = pg_query($conn, $query);
				
				if (!$result)
				{
					echo "ERROR : " . pg_last_error($conn);
					exit;
				}	
				if(!pg_num_rows($result)>0)
				{
					echo "No record found : " ;
					exit;
				}
				$row = pg_fetch_array($result);
				
				
			?>
            <form>
                 <input type="hidden" class="circleinfoid"  value="<?php echo $circleinfoid;?>">
	        <div class="form-group">
	            Circle Code: <input type="text" class="form-control form-control-sm circlecode" name="circlecode"  value="<?php echo $row['circlecode'];?>" >        
	        </div>
	        <div class="form-group">
	            Circle Name: <input type="text" class="form-control form-control-sm circlevalue" name="circlevalue"value="<?php echo $row['circlevalue'];?>" >   
	        </div>
	        
	        <div class="form-group status">
	                                
	        </div>
	        <div class="alert alert-success success_status" style='display:none'> <a href="#" class="close" data-dismiss="alert">×</a>
			    <h5>Success</h5>
			    <div>Circle info updated successfully!</div>
			</div>

			<div>
                <button type="button" class="btn btn-sm btn-info update_submit">Update</button>
            </div>
            </form>
            </div>
            <div  class="col-md-3">
            </div>
        </div>
    </div>
        

<?php include 'footer.php'; }?>

<script>
$(document).ready(function(){
	
	// update button click
	$('.update_submit').click(function(){               // update_submit click
		event.preventDefault();
		var circleinfoid = $('.circleinfoid').val();
		var circlecode = $('.circlecode').val();
		var circlevalue = $('.circlevalue').val();
		var task = 'update_circle_info';
		if(circlecode=='' || circlecode==null)
		{
			$('.status').html("<div class='alert alert-danger'><strong>Empty field!</strong> Please enter circle code.</div>");
			return false;
		}
		else
		if(circlevalue=='' || circlevalue==null)
		{
			$('.status').html("<div class='alert alert-danger'><strong>Empty field!</strong> Please enter circle name.</div>");
			return false;
		}
		else
		{
			$.ajax({
			type : 'post',
			url : 'updation_helper.php',
			data : 'circleinfoid='+circleinfoid+'&circlecode='+circlecode+'&circlevalue='+circlevalue+'&task='+task+'&csrf_token='+encodeURIComponent('<?php echo $_SESSION['csrf_token']; ?>'),
			success : function(res)
			{
				if(res == 'success')
				{
					$('.success_status').show();
					window.setTimeout(function () {
					    $(".success_status").fadeTo(500, 0).slideUp(500, function () {
					        $(this).remove();
					    window.location.reload();    
					    });
					}, 5000);

				}
				else
				{
					$('.status').html("<div class='alert alert-danger'><strong>"+res+"</div>");
					return false;
				}
			}
		});
		}
	});
});
</script>
</body>
</html>
