<?php

function startsWith($haystack, $needle) {
    return substr_compare($haystack, $needle, 0, strlen($needle)) === 0;
}
function endsWith($haystack, $needle) {
    return substr_compare($haystack, $needle, -strlen($needle)) === 0;
}

function recurse_copy($src,$dst) { 
    $dir = opendir($src); 
    @mkdir($dst); 
    while(false !== ( $file = readdir($dir)) ) { 
        if (( $file != '.' ) && ( $file != '..' )) { 
            if ( is_dir($src . '/' . $file) ) { 
                recurse_copy($src . '/' . $file,$dst . '/' . $file); 
            } 
            else { 
                copy($src . '/' . $file,$dst . '/' . $file); 
            } 
        } 
    } 
    closedir($dir); 
}



function isBasicCommand( string $command , array $COMMANDS) {
	return !is_array(array_values($COMMANDS[$command])[0] ?? true); 
}

function validateArgs( array $args, array $commands ) {
	
	$args = array_values($args);
	$arg = strtolower( trim( $args[0] ?? '') );
	if( !($arg and isset($commands[$arg])) ) { return false; }
	$args[0] = $arg;
	return $args;
}

function parse( array $args ) {
	global $COMMANDS;
	
	
	$stack = ["", ""];
	$function = "";
	$stack[1] = $args[0];
	$commands = $COMMANDS;
	
	while( ($args = validateArgs($args,  $commands ) )  and  !isBasicCommand($stack[1],  $commands)  ) {
				
		$commands = $commands[$stack[1]] ?? $commands;
		unset($args[0]);
		$args = array_values($args);
		$stack[0] = $stack[1];
		$stack[1] = $args[0] ?? false;
		$function .= ucfirst($stack[0]);
} 
	
	if( $args !== false ) {
		$options = $commands[$args[0]];
		$function.=ucfirst($args[0]);
		unset($args[0]);
		$args = array_values($args);
		$function( $args, getopt($options[0], $options[1]) );
	}
	else  {
		echo "invalid  or missing command\n";
		return;
	}

}


// CLI commands

function Build (array $args=[], array $options=[] ) {
		echo "Building project\n";
}

function DeleteProject (array $args=[], array $options=[] ) {
		echo "Deleting project\n";
}

function DeleteModule (array $args=[], array $options=[] ) {
		echo "Deleting Module\n";
}

function ExportModule (array $args=[], array $options=[] ) {}

function ImportModule (array $args=[], array $options=[] ) {}

function CreateProject (array $args=[], array $options=[] ) {
	if( $name = $args[0] ?? false ) {
		$dir = getcwd()."/$name";
		mkdir($dir);
		recurse_copy(__DIR__."/default-spark-vue", $dir);
	}
	else {
		echo "specify project name";
	}

}

function CreateModule (array $args=[], array $options=[] ) {}
