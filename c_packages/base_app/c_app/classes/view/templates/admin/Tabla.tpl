<html>
<head>
	<title>Test tablas</title>
</head>


	<script src="Media/VendorScript/jQuery/jQuery.js"></script>
	<script src="Media/VendorScript/jQuery/Class-0.0.2.js"></script>
	<script src="Media/Script/lib/table/table.js"></script>
	<script >

		jQuery(document).ready(function() {
			var tabla = new table({
					url: '/action/tabla',
					rowsno: 20
				});	
		});
	</script>
<body>
	<div id="tabla">
		
	</div>
</body>
</html>