<?php 
session_start();
if($_SESSION['user']=='')
{
	header('Location: login.php');
	exit;
}
else
{
	date_default_timezone_set('Asia/Calcutta');
	include 'php/sessioncheck.php';

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

<?php
if ($_SESSION['superadmin'] == 1)
{
?>
<script type="text/javascript">
$('#lazy').jstree({
    'core' : {
        'data' : {
            "url" : "asset-tree-helper.php?operation=get_node",
            "data" : function (node) {
                return { "id" : node.id };
            }
	}
	,'check_callback' : true,
        'themes' : {
              'responsive' : false
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
    'plugins' : ["search","state","contextmenu","wholerow"],
    contextmenu: {items: customMenu}
}).on('create_node.jstree', function (e, data) {
              
          $.get('asset-tree-helper.php?operation=create_node', { 'id' : data.node.parent, 'position' : data.position, 'text' : data.node.text })
            .done(function (d) {
              data.instance.set_id(data.node, d.id);
            })
            .fail(function () {
              data.instance.refresh();
            });
        }).on('rename_node.jstree', function (e, data) {
          $.get('asset-tree-helper.php?operation=rename_node', { 'id' : data.node.id, 'text' : data.text })
            .fail(function () {
              data.instance.refresh();
            });
        }).on('delete_node.jstree', function (e, data) {
          $.get('asset-tree-helper.php?operation=delete_node', { 'id' : data.node.id })
            .fail(function () {
              data.instance.refresh();
            });
	});

function customMenu(node) {
    var tree = $("#lazy").jstree(true);	
    // The default set of all items
    var items = {
        createItem: { // The "create" menu item
		"separator_before": false,    
		label: "Create",
		    action: function (obj) {
			var $node = tree.create_node(node);
			tree.edit($node);
		    }
        },
        renameItem: { // The "rename" menu item
		"separator_before": true,    
		label: "Rename",
		    action: function (obj) {
			tree.edit(node);
		    }
        },
        deleteItem: { // The "delete" menu item
		"separator_before": true,    
		label: "Delete",
		    action: function (obj) {
			if(confirm('Are you sure to remove this node?')){
            			tree.delete_node(node);
            		}
		    }
        }
    };

    return items;
}

/*var to = false;
$('#lazy_q').keyup(function () {
    if(to) { clearTimeout(to); }
        to = setTimeout(function () {
            var v = $('#lazy_q').val();
            $('#lazy').jstree(true).search(v);
            }, 250);
});*/

</script>
<?php
}
else
{
?>
<script type="text/javascript">
$('#lazy').jstree({
    'core' : {
        'data' : {
            "url" : "asset-tree-helper.php?operation=get_node",
            "data" : function (node) {
                return { "id" : node.id };
            }
	}
	,'check_callback' : true,
        'themes' : {
              'responsive' : false
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
    'plugins' : ["search","state","wholerow"],
});
</script>
<?php
}
?>
</body>
</html>
