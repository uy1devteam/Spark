<?php
try {
	include __DIR__ . '/../includes/autoload.php';
	include __DIR__ . '/../includes/utilities.php';
	include __DIR__ . '/../includes/config.php';
	
	
	
	$route = ltrim(strtok($_SERVER['REQUEST_URI'], '?'), '/');
	$route = trim($route);
	
	if( ( strlen($route) > strlen("css") ) and ( endsWith($route, ".css") or endsWith($route, ".js")) ) {
		http_response_code(404);
		die();
	}
	
	$entryPoint = new \Spark\EntryPoint($route, new \Routes(), new \Config( $config ?? []));
	$entryPoint->run();
		
?>

<!doctype html>
<html>

<head>
	<link rel="stylesheet" href="styles.css" />
	<?php echo $entryPoint->css; ?>
</head>

<body>
	<?php echo $entryPoint->html; ?>

	<?php 
			echo '<script> $data = '.json_encode($_GET )."</script>";
			echo $entryPoint->js;
	?>
	<script src="index.js"></script>

</body>

</html>








<?php	
	
}

catch ( \Exception $e) {
	echo "error";
	//header(301);
	//header('location: 500');
}
