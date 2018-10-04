<?php 
session_start();
if($_SESSION['user']=='')
{
	header('Location: login.php');
}
else
{
	error_reporting(0);
	date_default_timezone_set('Asia/Calcutta');

?>
<html>
<head>
<title>Job Data Fields</title>

</head>
<body>
<?php

include 'header.php';
include 'php/config.php';

$conn = pg_connect($conn_string);

if(!$conn)
{
	echo "ERROR : Unable to open database";
	exit;
}

$query = "SELECT J.*,L.sitecode FROM jobinfo as J JOIN location as L ON J.locationid=L.locationid WHERE J.status='0' ORDER BY J.jobinfoid ";
$result = pg_query($conn, $query);

if (!$result)
{
	echo "ERROR : " . pg_last_error($conn);
	exit;
}

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
                                <a href="jobinfo.php" style="color:blue;text-align:right;" class="nav-link">Jobs</a>
                            </li>
                            <li class="nav-item">
                                <a href="import-job-info.php"style="color:blue;text-align:right;"  class="nav-link">Import Jobs</a>
                            </li>
                            <li class="nav-item">
                                <a href="import-job-data-fields.php"style="color:blue;text-align:right;"  class="nav-link">Import Job Data Fields</a>
                            </li>
                            <li class="nav-item pull-left">
                                <a href="jobdropdown.php?jobinfoid=0" style="color:blue;text-align:right;" class="nav-link">Job Dropdown Values</a>
                            </li>
                            <li class="nav-item">
                                <a href="import-job-dropdown-fields.php"style="color:blue;text-align:right;"  class="nav-link">Import Job Dropdown Values</a>
                            </li>
                            
                        </ul>
                    </div>
                </div>
            </nav>  
		<div  class="col-md-12">
			
		<h3>Job Data Fields</h3>
			<?php
				$jobinfoid = $_GET['jobinfoid'];
			?>
			<div class="col=md-12 form-group">
				<label for="select_job" class="col-sm-4 control-label">Select Job:</label>
		        <div>
		            <select name='jobinfoid' class='jobinfoid form-control form-control-sm' style='width:200px;'>
						<option value='0'>-- Select job --</option>
						<?php
							while($row = pg_fetch_array($result))
							{
								if($row['jobinfoid']==$jobinfoid)
								{
									echo "<option value='".$row['jobinfoid']."' selected>".$row['jobno']."</option>";
								}
								else
								{
									echo "<option value='".$row['jobinfoid']."'>".$row['jobno']." [Sitecode - ".$row['sitecode']."]</option>";
								}
							}

						?>
					</select>
				</div>
			</div>
			<hr/>
			<?php
		
		if($jobinfoid!='0')
		{
	?>
		<div class='visit_info'>
			
			<table id='tieuptable' class='table table-bordered table-responsive table-condensed table-scroll table-fixed-header' style="width:100%;">
				<input type='hidden' class='jobinfoid_val' value="<?php echo $jobinfoid;?>">
				<tr>
					<td>
						<div class="col-md-12 form-group">
							<label for="scanner" class="col-sm-12 control-label">Scanner:</label>
			                <div>
			                <?php
			                		$sql_scan = "SELECT count(visittype) as count FROM visits  WHERE jobinfoid='$jobinfoid' AND visittype='2' GROUP BY visittype";
									$res_scan = pg_query($conn,$sql_scan);
									$row_scan = pg_fetch_array($res_scan);
									//echo $count = $row1['count'];
									$count_scan = $row_scan['count'];
									if( $count_scan=='' || $count_scan==null)
									{
										$count_scan='0';
									}
									$remain_scanners = 4-$count_scan;
							?>
							<p style="color:#454df3;"> You have alresdy set values for <b><?php echo $count_scan;?></b> scanners<br>
								Now you can setup <b><?php echo $remain_scanners;?></b> more scanners</p> 
			                    <select class='scanner form-control form-control-sm' style='width:200px;'>
									<option value='0' selected>-- Choose one --</option>
								
								<?php
									$j=1;
									for($i=$count_scan;$i<4;$i++)
									{	

										echo "<option value='".$j."'>".$j."</option>";
										$j=$j+1;
									}
									echo "</select>
						                </div>
									<br/>";
									if($count_scan=='0' || $count_scan=='' || $count_scan==null)
									{
										echo "<h3 style='color:red;'>No scanners found for this JOB";
									}
									else
									{
										$select = 1;
										echo "<table class='table table-bordered table-responsive table-condensed table-scroll table-fixed-header'  cellspacing='0' cellspacing='0' width='100%'>";
										$sql_scan_val = "SELECT * FROM visits WHERE jobinfoid='$jobinfoid' AND visittype='2' ORDER BY visitsid";
										$res_scan_val = pg_query($conn,$sql_scan_val);
										while($row_scan_val = pg_fetch_array($res_scan_val))
										{
											echo "<tr>";
												echo "<div class='form-group'>"; 
													echo "<td>Scanner $select description/label:</td>";
													echo "<td><input type='text' id='".$row_scan_val['visitsid']."' value='".$row_scan_val['visittypedesc']."' class='form-control form-control-sm' style='width:250px;' disabled/></td>";
													if($row_scan_val['ismandatory']=='1')
													{
														echo "<td><input type='checkbox' id='chk_".$row_scan_val['visitsid']."' class='form-control form-control-sm' style='width:20px;' checked disabled/></td>";
													}
													else
													{
													echo "<td><input type='checkbox' id='chk_".$row_scan_val['visitsid']."'  class='form-control' style='width:20px;' disabled/></td>";
													}
													
													echo "<td>";
														echo "<a class='btn btn-sm btn-danger edit_label' href='#edit_label_".$row_scan_val['visitsid']."' data-toggle='modal'>Edit</a><br/>";
													echo "</td>";
													//echo 
												echo "</div>";
											echo "</tr>";
											$select = $select+1;
											echo "<div class='modal fade' id='edit_label_".$row_scan_val['visitsid']."' role='dialog'>
											    <div class='modal-dialog'>
											        <div class='modal-content  col-md-12'>
											            <!-- Modal Header -->
											            <div class='modal-header'>
											                <button type='button' class='close' data-dismiss='modal'>
											                    <span aria-hidden='true'>&times;</span>
											                    <span class='sr-only'>Close</span>
											                </button>
											            </div>
											            
											            <!-- Modal edit label -->
											            <div class='modal-body'>
											                <h3 class='text-center'>Update Label</h3>
											                <form>
														        <div class='form-group'>
														            New label: <input type='text' class='form-control form-control-sm new_term_".$row_scan_val['visitsid']."' name='new_label' placeholder='New label' id='new_label'  value='".$row_scan_val['visittypedesc']."'><br/>";
														            if($row_scan_val['ismandatory']=='1')
														            {
														            	echo "Is Mandatory: <input type='checkbox' class='form-control form-control-sm new_chk_".$row_scan_val['visitsid']."' name='new_check' id='new_check' style='width:20px;' checked />";
														            }
														            else
														            {
														            	echo "Is Mandatory: <input type='checkbox' class='form-control form-control-sm new_chk_".$row_scan_val['visitsid']."' name='new_check' id='new_check' style='width:20px;' disabled />";
														            }
														            
														            echo "<input type='hidden' class='form-control job_val_".$row_scan_val['jobinfoid']."' id='job_val_".$row_scan_val['jobinfoid']."' value='".$row_scan_val['jobinfoid']."'>       
														        </div>
														        
											                </form>
											            </div>
											            
											            
											            <!-- Modal Footer -->
											            <div class='modal-footer'>
											                <button type='button' class='btn btn-sm btn-default' data-dismiss='modal'>Close</button>
											                <button type='button' class='btn btn-sm btn-danger btn_update_visit_desc' value='".$row_scan_val['visitsid']."'>Update</button>
											            </div>
											        </div>
											    </div>";
										}
										echo "</table>";
										echo "<div class='update_scan_status' style='width:500px;'>";
										echo "</td>";
									}
			                	?>
								<br/>
								</div>
							</td>
							<td>
								<div class="col-md-12 scanner_data form-group">
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<div class="col-md-12 form-group">
									<label for="desc" class="col-sm-12 control-label">Desc/Text box:</label>
					                <div class="col-sm-12">
					                <?php
				                		$sql_scan = "SELECT count(visittype) as count FROM visits  WHERE jobinfoid='$jobinfoid' AND visittype='1' GROUP BY visittype";
										$res_scan = pg_query($conn,$sql_scan);
										$row_scan = pg_fetch_array($res_scan);
										//echo $count = $row1['count'];
										$count_scan = $row_scan['count'];
										if( $count_scan=='' || $count_scan==null)
										{
											$count_scan='0';
										}
										$remain_scanners = 6-$count_scan;
									?>
									<p style="color:#454df3;"> You have alresdy set values for <b><?php echo $count_scan;?></b> text fields<br>
										Now you can setup <b><?php echo $remain_scanners;?></b> more text fields</p> 
				                    <select class='desc form-control form-control-sm' style='width:200px;'>
										<option value='0' selected>-- Choose one --</option>
											
									<?php
			                		
									$j=1;
									for($i=$count_scan;$i<6;$i++)
									{	

										echo "<option value='".$j."'>".$j."</option>";
										$j=$j+1;
									}
									echo "</select>
						                </div>
									<br/>";
									if($count_scan=='0' || $count_scan=='' || $count_scan==null)
									{
										echo "<h3 style='color:red;'>No description/text terms found for this JOB";
									}
									else
									{
										$select = 1;
										echo "<table class='table table-bordered table-responsive table-condensed table-scroll table-fixed-header'  cellspacing='0' cellspacing='0' width='100%'>";
										$sql_scan_val = "SELECT * FROM visits WHERE jobinfoid='$jobinfoid' AND visittype='1' ORDER BY visitsid";
										$res_scan_val = pg_query($conn,$sql_scan_val);
										while($row_scan_val = pg_fetch_array($res_scan_val))
										{
											echo "<tr>";
												echo "<div class='form-group'>"; 
													echo "<td>Desc/text $select description/label:</td>";
													echo "<td><input type='text' id='".$row_scan_val['visitsid']."' value='".$row_scan_val['visittypedesc']."' class='form-control form-control-sm' style='width:250px;' disabled/></td>";
													if($row_scan_val['ismandatory']=='1')
													{
														echo "<td><input type='checkbox' id='chk_".$row_scan_val['visitsid']."' class='form-control form-control-sm' style='width:20px;' checked disabled/></td>";
													}
													else
													{
													echo "<td><input type='checkbox' id='chk_".$row_scan_val['visitsid']."'  class='form-control form-control-sm' style='width:20px;' disabled/></td>";
													}
													
													echo "<td>";
														echo "<a class='btn btn-sm btn-danger edit_label' href='#edit_label_".$row_scan_val['visitsid']."' data-toggle='modal'>Edit</a><br/>";
													echo "</td>";
													//echo 
												echo "</div>";
											echo "</tr>";
											$select = $select+1;
											echo "<div class='modal fade' id='edit_label_".$row_scan_val['visitsid']."' role='dialog'>
											    <div class='modal-dialog'>
											        <div class='modal-content  col-md-12'>
											            <!-- Modal Header -->
											            <div class='modal-header'>
											                <button type='button' class='close' data-dismiss='modal'>
											                    <span aria-hidden='true'>&times;</span>
											                    <span class='sr-only'>Close</span>
											                </button>
											            </div>
											            
											            <!-- Modal edit label -->
											            <div class='modal-body'>
											                <h3 class='text-center'>Update Label</h3>
											                <form>
														        <div class='form-group'>
														            New label: <input type='text' class='form-control new_term_".$row_scan_val['visitsid']."' name='new_label' placeholder='New label' id='new_label'  value='".$row_scan_val['visittypedesc']."'><br/>";
														            if($row_scan_val['ismandatory']=='1')
														            {
														            	echo "Is Mandatory: <input type='checkbox' class='form-control new_chk_".$row_scan_val['visitsid']."' name='new_check' id='new_check' style='width:20px;' checked/>";
														            }
														            else
														            {
														            	echo "Is Mandatory: <input type='checkbox' class='form-control form-control-sm new_chk_".$row_scan_val['visitsid']."' name='new_check' id='new_check' style='width:20px;'/>";
														            }
														            
														            echo "<input type='hidden' class='form-control job_val_".$row_scan_val['jobinfoid']."' id='job_val_".$row_scan_val['jobinfoid']."' value='".$row_scan_val['jobinfoid']."'>

														        </div>
														        
											                </form>
											            </div>
											            
											            
											            <!-- Modal Footer -->
											            <div class='modal-footer'>
											                <button type='button' class='btn btn-sm btn-default' data-dismiss='modal'>Close</button>
											                <button type='button' class='btn btn-sm btn-danger btn_update_visit_desc' value='".$row_scan_val['visitsid']."'>Update</button>
											            </div>
											        </div>
											    </div>";
										}
										echo "</table>";
									}
			                	?>
									<br/>
								
							</td>
							<td>
								<div class="col-md-12 desc_data form-group">
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<div class="col-md-12 form-group">
									<label for="date" class="col-sm-12 control-label">Date:</label>
					                <div class="col-sm-12">
					                <?php
				                		$sql_scan = "SELECT count(visittype) as count FROM visits  WHERE jobinfoid='$jobinfoid' AND visittype='4' GROUP BY visittype";
										$res_scan = pg_query($conn,$sql_scan);
										$row_scan = pg_fetch_array($res_scan);
										//echo $count = $row1['count'];
										$count_scan = $row_scan['count'];
										if( $count_scan=='' || $count_scan==null)
										{
											$count_scan='0';
										}
										$remain_scanners = 2-$count_scan;
									?>
									<p style="color:#454df3;"> You have alresdy set values for <b><?php echo $count_scan;?></b> date terms<br>
										Now you can setup <b><?php echo $remain_scanners;?></b> more date terms</p>
					                    <select class='date form-control form-control-sm' style='width:200px;'>
											<option value='0' selected>-- Choose one --</option>
											
								<?php
			                		
									$j=1;
									for($i=$count_scan;$i<2;$i++)
									{	

										echo "<option value='".$j."'>".$j."</option>";
										$j=$j+1;
									}
									echo "</select>
						                </div>
									<br/>";
									if($count_scan=='0' || $count_scan=='' || $count_scan==null)
									{
										echo "<h3 style='color:red;'>No date terms found for this JOB";
									}
									else
									{
										$select = 1;
										echo "<table class='table table-bordered table-responsive table-condensed table-scroll table-fixed-header'  cellspacing='0' cellspacing='0' width='100%'>";
										$sql_scan_val = "SELECT * FROM visits WHERE jobinfoid='$jobinfoid' AND visittype='4' ORDER BY visitsid";
										$res_scan_val = pg_query($conn,$sql_scan_val);
										while($row_scan_val = pg_fetch_array($res_scan_val))
										{
											echo "<tr>";
												echo "<div class='form-group'>"; 
													echo "<td>Date $select description/label:</td>";
													echo "<td><input type='text' id='".$row_scan_val['visitsid']."' value='".$row_scan_val['visittypedesc']."' class='form-control form-control-sm' style='width:250px;' disabled/></td>";
													if($row_scan_val['ismandatory']=='1')
													{
														echo "<td><input type='checkbox' id='chk_".$row_scan_val['visitsid']."' class='form-control form-control-sm' style='width:20px;' checked disabled/></td>";
													}
													else
													{
													echo "<td><input type='checkbox' id='chk_".$row_scan_val['visitsid']."'  class='form-control form-control-sm' style='width:20px;' disabled/></td>";
													}
													
													echo "<td>";
														echo "<a class='btn btn-sm btn-danger edit_label' href='#edit_label_".$row_scan_val['visitsid']."' data-toggle='modal'>Edit</a><br/>";
													echo "</td>";
													//echo 
												echo "</div>";
											echo "</tr>";
											$select = $select+1;
											echo "<div class='modal fade' id='edit_label_".$row_scan_val['visitsid']."' role='dialog'>
											    <div class='modal-dialog'>
											        <div class='modal-content  col-md-12'>
											            <!-- Modal Header -->
											            <div class='modal-header'>
											                <button type='button' class='close' data-dismiss='modal'>
											                    <span aria-hidden='true'>&times;</span>
											                    <span class='sr-only'>Close</span>
											                </button>
											            </div>
											            
											            <!-- Modal edit label -->
											            <div class='modal-body'>
											                <h3 class='text-center'>Update Label</h3>
											                <form>
														        <div class='form-group'>
														            New label: <input type='text' class='form-control form-control-sm new_term_".$row_scan_val['visitsid']."' name='new_label' placeholder='New label' id='new_label'  value='".$row_scan_val['visittypedesc']."'><br/>";
														            if($row_scan_val['ismandatory']=='1')
														            {
														            	echo "Is Mandatory: <input type='checkbox' class='form-control form-control-sm new_chk_".$row_scan_val['visitsid']."' name='new_check' id='new_check' style='width:20px;' checked />";
														            }
														            else
														            {
														            	echo "Is Mandatory: <input type='checkbox' class='form-control form-control-sm new_chk_".$row_scan_val['visitsid']."' name='new_check' id='new_check' style='width:20px;' />";
														            }
														            
														            echo "<input type='hidden' class='form-control job_val_".$row_scan_val['jobinfoid']."' id='job_val_".$row_scan_val['jobinfoid']."' value='".$row_scan_val['jobinfoid']."'>       
														        </div>
														        
											                </form>
											            </div>
											            
											            
											            <!-- Modal Footer -->
											            <div class='modal-footer'>
											                <button type='button' class='btn btn-sm btn-default' data-dismiss='modal'>Close</button>
											                <button type='button' class='btn btn-sm btn-danger btn_update_visit_desc' value='".$row_scan_val['visitsid']."'>Update</button>
											            </div>
											        </div>
											    </div>";
										}
										echo "</table>";
										echo "<div class='update_scan_status' style='width:500px;'>";
										echo "</td>";
									}
			                	?>
								<br/>
								</div>
							</td>
							<td>
								<div class="col-md-12 date_data form-group">
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<div class="col-md-12 form-group">
									<label for="dropdown" class="col-sm-12 control-label">Dropdown:</label>
					                <div class="col-sm-12">
					                <?php
				                		$sql_scan = "SELECT count(visittype) as count FROM visits  WHERE jobinfoid='$jobinfoid' AND visittype='5' GROUP BY visittype";
										$res_scan = pg_query($conn,$sql_scan);
										$row_scan = pg_fetch_array($res_scan);
										//echo $count = $row1['count'];
										$count_scan = $row_scan['count'];
										if( $count_scan=='' || $count_scan==null)
										{
											$count_scan='0';
										}
										$remain_scanners = 2-$count_scan;
									?>
									<p style="color:#454df3;"> You have alresdy set values for <b><?php echo $count_scan;?></b> dropdown terms<br>
										Now you can setup <b><?php echo $remain_scanners;?></b> more dropdown terms</p>
					                    <select class='dropdown form-control form-control-sm' style='width:200px;'>
											<option value='0' selected>-- Choose one --</option>
											
								<?php
			                		
									$j=1;
									for($i=$count_scan;$i<2;$i++)
									{	

										echo "<option value='".$j."'>".$j."</option>";
										$j=$j+1;
									}
									echo "</select>
						                </div>
									<br/>";
									if($count_scan=='0' || $count_scan=='' || $count_scan==null)
									{
										echo "<h3 style='color:red;'>No dropdown/select found for this JOB";
									}
									else
									{
										$select = 1;
										echo "<table class='table table-bordered table-responsive table-condensed table-scroll table-fixed-header'  cellspacing='0' cellspacing='0' width='100%'>";
										$sql_scan_val = "SELECT * FROM visits WHERE jobinfoid='$jobinfoid' AND visittype='5' ORDER BY visitsid";
										$res_scan_val = pg_query($conn,$sql_scan_val);
										while($row_scan_val = pg_fetch_array($res_scan_val))
										{
											echo "<tr>";
												echo "<div class='form-group'>"; 
													echo "<td>Dropdown $select description/label:</td>";
													echo "<td><input type='text' id='".$row_scan_val['visitsid']."' value='".$row_scan_val['visittypedesc']."' class='form-control form-control-sm' style='width:250px;' disabled/></td>";
													if($row_scan_val['ismandatory']=='1')
													{
														echo "<td><input type='checkbox' id='chk_".$row_scan_val['visitsid']."' class='form-control form-control-sm' style='width:20px;' checked disabled/></td>";
													}
													else
													{
													echo "<td><input type='checkbox' id='chk_".$row_scan_val['visitsid']."'  class='form-control form-control-sm' style='width:20px;' disabled/></td>";
													}
													
													echo "<td>";
														echo "<a class='btn btn-sm btn-danger edit_label' href='#edit_label_".$row_scan_val['visitsid']."' data-toggle='modal'>Edit</a><br/>";
													echo "</td>";
													//echo 
												echo "</div>";
											echo "</tr>";
											$select = $select+1;
											echo "<div class='modal fade' id='edit_label_".$row_scan_val['visitsid']."' role='dialog'>
											    <div class='modal-dialog'>
											        <div class='modal-content  col-md-12'>
											            <!-- Modal Header -->
											            <div class='modal-header'>
											                <button type='button' class='close' data-dismiss='modal'>
											                    <span aria-hidden='true'>&times;</span>
											                    <span class='sr-only'>Close</span>
											                </button>
											            </div>
											            
											            <!-- Modal edit label -->
											            <div class='modal-body'>
											                <h3 class='text-center'>Update Label</h3>
											                <form>
														        <div class='form-group'>
														            New label: <input type='text' class='form-control form-control-sm new_term_".$row_scan_val['visitsid']."' name='new_label' placeholder='New label' id='new_label'  value='".$row_scan_val['visittypedesc']."'><br/>";
														            if($row_scan_val['ismandatory']=='1')
														            {
														            	echo "Is Mandatory: <input type='checkbox' class='form-control form-control-sm new_chk_".$row_scan_val['visitsid']."' name='new_check' id='new_check' style='width:20px;' checked />";
														            }
														            else
														            {
														            	echo "Is Mandatory: <input type='checkbox' class='form-control form-control-sm new_chk_".$row_scan_val['visitsid']."' name='new_check' id='new_check' style='width:20px;' />";
														            }
														            
														            echo "<input type='hidden' class='form-control job_val_".$row_scan_val['jobinfoid']."' id='job_val_".$row_scan_val['jobinfoid']."' value='".$row_scan_val['jobinfoid']."'>       
														        </div>
														        
											                </form>
											            </div>
											            
											            
											            <!-- Modal Footer -->
											            <div class='modal-footer'>
											                <button type='button' class='btn btn-sm btn-default' data-dismiss='modal'>Close</button>
											                <button type='button' class='btn btn-sm btn-danger btn_update_visit_desc' value='".$row_scan_val['visitsid']."'>Update</button>
											            </div>
											        </div>
											    </div>";
										}
										echo "</table>";
										echo "<div class='update_scan_status' style='width:500px;'>";
										echo "</td>";
									}
			                	?>
								<br/>
								</div>
							</td>
							<td>
								<div class="col-md-12 dropdown_data form-group">
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<div class="col-md-12 form-group">
									<label for="image" class="col-sm-12 control-label">Image:</label>
					                <div class="col-sm-12">
					                <?php
				                		$sql_scan = "SELECT count(visittype) as count FROM visits  WHERE jobinfoid='$jobinfoid' AND visittype='3' GROUP BY visittype";
										$res_scan = pg_query($conn,$sql_scan);
										$row_scan = pg_fetch_array($res_scan);
										//echo $count = $row1['count'];
										$count_scan = $row_scan['count'];
										if( $count_scan=='' || $count_scan==null)
										{
											$count_scan='0';
										}
										$remain_scanners = 2-$count_scan;
									?>
									<p style="color:#454df3;"> You have alresdy set values for <b><?php echo $count_scan;?></b> image terms<br>
										Now you can setup <b><?php echo $remain_scanners;?></b> more image terms</p>
					                    <select class='image form-control form-control-sm' style='width:200px;'>
											<option value='0' selected>-- Choose one --</option>
											
								<?php
			                		
									$j=1;
									for($i=$count_scan;$i<2;$i++)
									{	

										echo "<option value='".$j."'>".$j."</option>";
										$j=$j+1;
									}
									echo "</select>
						                </div>
									<br/>";
									if($count_scan=='0' || $count_scan=='' || $count_scan==null)
									{
										echo "<h3 style='color:red;'>No image term found for this JOB";
									}
									else
									{
										$select = 1;
										echo "<table class='table table-bordered table-responsive table-condensed table-scroll table-fixed-header'  cellspacing='0' cellspacing='0' width='100%'>";
										$sql_scan_val = "SELECT * FROM visits WHERE jobinfoid='$jobinfoid' AND visittype='3' ORDER BY visitsid";
										$res_scan_val = pg_query($conn,$sql_scan_val);
										while($row_scan_val = pg_fetch_array($res_scan_val))
										{
											echo "<tr>";
												echo "<div class='form-group'>"; 
													echo "<td>Image $select description/label:</td>";
													echo "<td><input type='text' id='".$row_scan_val['visitsid']."' value='".$row_scan_val['visittypedesc']."' class='form-control form-control-sm' style='width:250px;' disabled/></td>";
													if($row_scan_val['ismandatory']=='1')
													{
														echo "<td><input type='checkbox' id='chk_".$row_scan_val['visitsid']."' class='form-control form-control-sm' style='width:20px;' checked disabled/></td>";
													}
													else
													{
													echo "<td><input type='checkbox' id='chk_".$row_scan_val['visitsid']."'  class='form-control form-control-sm' style='width:20px;' disabled/></td>";
													}
													
													echo "<td>";
														echo "<a class='btn btn-sm btn-danger edit_label' href='#edit_label_".$row_scan_val['visitsid']."' data-toggle='modal'>Edit</a><br/>";
													echo "</td>";
													//echo 
												echo "</div>";
											echo "</tr>";
											$select = $select+1;
											echo "<div class='modal fade' id='edit_label_".$row_scan_val['visitsid']."' role='dialog'>
											    <div class='modal-dialog'>
											        <div class='modal-content  col-md-12'>
											            <!-- Modal Header -->
											            <div class='modal-header'>
											                <button type='button' class='close' data-dismiss='modal'>
											                    <span aria-hidden='true'>&times;</span>
											                    <span class='sr-only'>Close</span>
											                </button>
											            </div>
											            
											            <!-- Modal edit label -->
											            <div class='modal-body'>
											                <h3 class='text-center'>Update Label</h3>
											                <form>
														        <div class='form-group'>
														            New label: <input type='text' class='form-control form-control-sm new_term_".$row_scan_val['visitsid']."' name='new_label' placeholder='New label' id='new_label'  value='".$row_scan_val['visittypedesc']."'><br/>";
														            if($row_scan_val['ismandatory']=='1')
														            {
														            	echo "Is Mandatory: <input type='checkbox' class='form-control form-control-sm new_chk_".$row_scan_val['visitsid']."' name='new_check' id='new_check' style='width:20px;' checked />";
														            }
														            else
														            {
														            	echo "Is Mandatory: <input type='checkbox' class='form-control form-control-sm new_chk_".$row_scan_val['visitsid']."' name='new_check' id='new_check' style='width:20px;' />";
														            }
														            
														            echo "<input type='hidden' class='form-control job_val_".$row_scan_val['jobinfoid']."' id='job_val_".$row_scan_val['jobinfoid']."' value='".$row_scan_val['jobinfoid']."'>       
														        </div>
														        
											                </form>
											            </div>
											            
											            
											            <!-- Modal Footer -->
											            <div class='modal-footer'>
											                <button type='button' class='btn btn-sm btn-default' data-dismiss='modal'>Close</button>
											                <button type='button' class='btn btn-sm btn-danger btn_update_visit_desc' value='".$row_scan_val['visitsid']."'>Update</button>
											            </div>
											        </div>
											    </div>";
										}
										echo "</table>";
										echo "<div class='update_scan_status' style='width:500px;'>";
										echo "</td>";
									}
			                	?>
								<br/>
								</div>
							</td>
							<td>
								<div class="col-md-12 image_data form-group">
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<input type='button' class='btn btn-sm btn-info btn-save' value="Save">
							</td>
							<td class='status'>
							</td>
						</tr>
					</table>
				</div>	
			<?php
				}
				else
				{
					echo "<h5>Please select job from list</h5>";
				}
			?>
			</div>
			
		</div>
	</div>
		
<?php 
pg_close($conn);

include 'footer.php'; 

	}
?>

<script>
$(document).ready(function(){
	var scanner_count = '0';
	var desc_count = '0';
	var date_count = '0';
	var dropdown_count = '0';
	var image_count = '0';
	var data;
	
	$('.jobinfoid').change(function(){
		var jobinfoid = $(this).val();
		window.location.assign("job-data-fields.php?jobinfoid="+jobinfoid);
		
	});
	
	var jobinfoid = $('.jobinfoid_val').val();
	
	data = 'jobinfoid='+jobinfoid;
	
	// creating elements for scanner
	$('.scanner').change(function() {
	    scanner_count = parseInt($(this).val(), 10);
	    var container = $('<div/>');
	    for(var i = 1; i <= scanner_count; i++) 
	    {
	         container.append('Lable '+i+': <input id="scanner'+i+'" name="scanner'+i+'" required class="form-control form-control-sm"/> Is mandatory <input type="checkbox" id="chk_scanner'+i+'" name="chk_scanner'+i+'"><hr/>');
	    }
	    $('.scanner_data').html(container);
	});


	// creating elements for text
	$('.desc').change(function() {
	    desc_count = parseInt($(this).val(), 10);
	    var container = $('<div />');
	    for(var i = 1; i <= desc_count; i++) 
	    {
	         container.append('Lable '+i+': <input id="desc'+i+'" name="desc'+i+'" class="form-control form-control-sm"/> Is mandatory <input type="checkbox" id="chk_desc'+i+'" name="chk_desc'+i+'"><hr/>');
	    }
	    $('.desc_data').html(container);
	});

	// creating elements for date
	$('.date').change(function() {
	    date_count = parseInt($(this).val(), 10);
	    var container = $('<div />');
	    for(var i = 1; i <= date_count; i++) 
	    {
	         container.append('Lable '+i+': <input id="date'+i+'" name="date'+i+'" class="form-control form-control-sm"/> Is mandatory <input type="checkbox" id="chk_date'+i+'" name="chk_date'+i+'"><hr/>');
	    }
	    $('.date_data').html(container);
	});

	// creating elements for dropdown
	$('.dropdown').change(function() {
	    dropdown_count = parseInt($(this).val(), 10);
	    var container = $('<div />');
	    for(var i = 1; i <= dropdown_count; i++) 
	    {
	         container.append('Lable '+i+': <input id="dropdown'+i+'" name="dropdown'+i+'" class="form-control form-control-sm"/> Is mandatory <input type="checkbox" id="chk_dropdown'+i+'" name="chk_dropdown'+i+'"><hr/>');
	    }
	    $('.dropdown_data').html(container);
	});

	// creating elements for image
	$('.image').change(function() {
	    image_count = parseInt($(this).val(), 10);
	    var container = $('<div />');
	    for(var i = 1; i <= image_count; i++) 
	    {
	         container.append('Lable '+i+': <input id="image'+i+'" name="image'+i+'" class="form-control form-control-sm"/> Is mandatory <input type="checkbox" id="chk_image'+i+'" name="chk_image'+i+'" ><hr/>');
	    }
	    $('.image_data').html(container);
	});

	//save button click
	$('.btn-save').click(function(){
		if(scanner_count!='0')     //scanner data
		{
			var scan_visit_type = '2';
			var scan_desc=[];
			var chk_scanner=[];
			for(var i = 1; i <= scanner_count; i++) 
		    {
		    	if($('#scanner'+i).val()=='' || $('#scanner'+i).val()==null)
		    	{
		    		alert("Please enter label for scanner");
		    		return false;
		    	}
		    	if($('#chk_scanner'+i).is(":checked"))
		    	{
		    		chk_scanner.push('1');
		    	}
		    	else
		    	{
		    		chk_scanner.push('0');
		    	}
		        scan_desc.push($('#scanner'+i).val());
		    }
		    data +='&scanner_count='+scanner_count+'&scan_visit_type='+scan_visit_type+'&scan_desc='+scan_desc+'&chk_scanner='+chk_scanner;
		}
		else
		{
			data +='&scanner_count='+scanner_count;
		}
		
		if(desc_count!='0')     //textbox data
		{
			var desc_visit_type = '1';
			var desc_desc=[];
			var chk_desc=[];
			for(var i = 1; i <= desc_count; i++) 
		    {
		    	if($('#desc'+i).val()=='' || $('#desc'+i).val()==null)
		    	{
		    		alert("Please enter label for desc/textbox");
		    		return false;
		    	}
		    	if($('#chk_desc'+i).is(":checked"))
		    	{
		    		chk_desc.push('1');
		    	}
		    	else
		    	{
		    		chk_desc.push('0');
		    	}
		        desc_desc.push($('#desc'+i).val());
		    }
		    data +='&desc_count='+desc_count+'&desc_visit_type='+desc_visit_type+'&desc_desc='+desc_desc+'&chk_desc='+chk_desc;
		}
		else
		{
			data +='&desc_count='+desc_count;
		}
		
		if(date_count!='0')     //date data
		{
			var date_visit_type = '4';
			var date_desc=[];
			var chk_date=[];
			for(var i = 1; i <= date_count; i++) 
		    {
		    	if($('#date'+i).val()=='' || $('#date'+i).val()==null)
		    	{
		    		alert("Please enter label for date");
		    		return false;
		    	}
		    	if($('#chk_date'+i).is(":checked"))
		    	{
		    		chk_date.push('1');
		    	}
		    	else
		    	{
		    		chk_date.push('0');
		    	}
		        date_desc.push($('#date'+i).val());
		    }
		     data +='&date_count='+date_count+'&date_visit_type='+date_visit_type+'&date_desc='+date_desc+'&chk_date='+chk_date;
		}
		else
		{
			data +='&date_count='+date_count;
		}

		if(dropdown_count!='0')     //dropdown data
		{
			var dropdown_visit_type = '5';
			var dropdown_desc=[];
			var chk_dropdown=[];
			for(var i = 1; i <= dropdown_count; i++) 
		    {
		    	if($('#dropdown'+i).val()=='' || $('#dropdown'+i).val()==null)
		    	{
		    		alert("Please enter label for dropdown");
		    		return false;
		    	}
		    	if($('#chk_dropdown'+i).is(":checked"))
		    	{
		    		chk_dropdown.push('1');
		    	}
		    	else
		    	{
		    		chk_dropdown.push('0');
		    	}
		        dropdown_desc.push($('#dropdown'+i).val());
		    }
		    data +='&dropdown_count='+dropdown_count+'&dropdown_visit_type='+dropdown_visit_type+'&dropdown_desc='+dropdown_desc+'&chk_dropdown='+chk_dropdown;
		}
		else
		{
			data +='&dropdown_count='+dropdown_count;
		}

		if(image_count!='0')     //image data
		{
			var image_visit_type = '3';
			var image_desc=[];
			var chk_image=[];
			for(var i = 1; i <= image_count; i++) 
		    {
		    	if($('#image'+i).val()=='' || $('#image'+i).val()==null)
		    	{
		    		alert("Please enter label for image");
		    		return false;
		    	}
		    	if($('#chk_image'+i).is(":checked"))
		    	{
		    		chk_image.push('1');
		    	}
		    	else
		    	{
		    		chk_image.push('0');
		    	}
		         image_desc.push($('#image'+i).val());
		    }
		    data +='&image_count='+image_count+'&image_visit_type='+image_visit_type+'&image_desc='+image_desc+'&chk_image='+chk_image;
		}
		else
		{
			data +='&image_count='+image_count;
		}
		
		$.ajax({
			type : 'post',
			url : 'visits_helper.php',
			data : data+'&task=add_visit',
			success : function(res)
			{
				if(res=='conn_error')
				{
					$('.status').html("<div class='alert alert-danger'><strong>DB Connection error!</strong></div>");
					return false;
				}
				else
				if(res=='success')
				{
					$('.status').html("<div class='alert alert-success'><strong>Success!</strong> Job Data Fields added successfully.</div>");
					$('.jobinfoid').append("<option value='0' selected>-- Select job --</option>");
					$('.btn-save').prop("disabled",true);
					window.location.assign('job-data-fields.php?jobinfoid='+jobinfoid);
					return false;
				}
				else
				{
					$('.status').html("<div class='alert alert-danger'><strong>"+res+"</strong></div>");
					return false;
				}
			}
		});

		data = '';
	});
	
	//update button in model
	$('.btn_update_visit_desc').click(function(){                           //btn_update_visit_desc click
		var visitsid = $(this).val();
		var visit_desc = $('.new_term_'+visitsid).val();
		var new_is_mandatory = $('.new_chk_'+visitsid).is(":checked");
		var task = 'update_visit_desc';
		if(new_is_mandatory==true)
		{
			new_is_mandatory = '1';
		}
		else
		{
			new_is_mandatory = '0';
		}
		var data = 'visitsid='+visitsid+'&jobinfoid='+jobinfoid+'&visit_desc='+visit_desc+'&new_is_mandatory='+new_is_mandatory+'&task='+task;

		$.ajax({
			type : 'post',
			url : 'visits_helper.php',
			data : data,
			success : function(res)
			{
				if(res=='conn_error')
				{
					$('.update_scan_status').html("<div class='alert alert-danger'><strong>DB Connection error!</strong></div>");
					return false;
				}
				else
				if(res=='success')
				{
					$('.update_scan_status').html("<div class='alert alert-success'><strong>Success!</strong> Visit updated successfully.</div>");
					window.location.assign('job-data-fields.php?jobinfoid='+jobinfoid);
				}
				else
				{
					$('.update_scan_status').html("<div class='alert alert-danger'><strong>"+res+"</strong></div>");
					return false;
				}
			}
		});
	});

});
</script>
</body>
</html>
