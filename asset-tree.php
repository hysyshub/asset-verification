<?php 
session_start();
if($_SESSION['user']=='')
{
    header('Location: login.php');
}
else
{
date_default_timezone_set('Asia/Calcutta');

?>
<html>
<head>
<meta charset="UTF-8">
<title>Asset Tree</title>

<style>

body 
{
font-family: arial,verdana;
font-size: 12px;
}

#footer
{
text-align: center;
padding: 10px;
}

#tieuptable
{
border-collapse:collapse;
font-family: arial,verdana;
}

#tieuptable, #tieuptable th, #tieuptable td
{
border: 1px solid #000;
}

#tieuptable
{
width:100%;
}

#tieuptable th
{
height:25px;
background-color: #ddd;
color: #000;
font-weight: bold;
font-size: 12px;
padding-left: 2px;
padding-right: 2px;
} 

#tieuptable td
{
height:20px;
text-align: left;
vertical-align: middle;
font-size: 12px;
padding-left: 2px;
padding-right: 2px;
}

.demo { overflow:auto; border:1px solid silver; min-height:100px; width:1000px; }

</style>

<link rel="stylesheet" href="jstree/dist/themes/default/style.min.css" />


</head>
<body>
<?php

include 'header.php';

?>
<!-- Page Content  -->
        <div id="content" style="overflow: auto;">

            <nav class="navbar navbar-expand-lg navbar-light bg-light" style="width:100%">
                <div class="container-fluid">

                    <button type="button" id="sidebarCollapse" class="btn btn-info" style='background:#030dcf;'>
                        <i class="fas fa-align-left"></i>
                        
                    </button>
                    <button class="btn btn-dark d-inline-block d-lg-none ml-auto" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="fas fa-align-justify"></i>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="nav navbar-nav ml-auto">

			</ul>
                    </div>
                </div>
            </nav>  
        <div  class="col-md-12">
            <div  class="col-md-6">
        	<h3>Asset Tree</h3>            
        	<div id="lazy" class="demo"></div>
            </div>
        </div>
    </div>
        
<?php include 'footer.php'; }?>
<script src="jstree/dist/jstree.min.js"></script>
<script type="text/javascript">
$('#lazy').jstree({
    'core' : {
        'data' : {
            "url" : "asset-tree-helper.php",
            "data" : function (node) {
                return { "id" : node.id };
            }
        }
    },
        search: {
            case_insensitive: true,
            ajax: {
                url: "asset-tree-helper.php",
                "data": function (n) {
                    return { id: n.attr ? n.attr("id") : 0 };

                }

            }
        },      
    'plugins' : ["search"]
});

/*var to = false;
$('#lazy_q').keyup(function () {
    if(to) { clearTimeout(to); }
        to = setTimeout(function () {
            var v = $('#lazy_q').val();
            $('#lazy').jstree(true).search(v);
            }, 250);
});*/

</script>
</body>
</html>
