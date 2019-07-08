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

function validateArgs(  array $args, array $commands ) {
	
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
		$dir = getcwd();
		$function( $dir, $args, getopt($options[0], $options[1]) );
	}
	else  {
		echo "invalid  or missing command\n";
		return;
	}

}


// CLI commands

function Build (string $dir, array $args=[], array $options=[] ) {
		echo "Building project\n";
}


function ExportModule (string $dir, array $args=[], array $options=[] ) {}

function ImportModule (string $dir, array $args=[], array $options=[] ) {}

function CreateProject (string $dir, array $args=[], array $options=[] ) {
	if( $name = $args[0] ?? false ) {
		$dir .= "/$name";
		mkdir($dir);
		recurse_copy(__DIR__."/default-spark-vue", $dir);
		echo " Project $name created\n";
	}
	else {
		echo "specify project name";
	}

}

function CreateModule (string $dir, array $args=[], array $options=[] ) {
	if( $name = $args[0] ?? false ) {
		unset($args[0]);
		$dir = file_exists("init.json") ? $dir : 
				(file_exists("../init.json") ? $dir."/.." :
				(file_exists("../../init.json") ? $dir."/../.." :
				(file_exists("../../../init.json") ? $dir."/../../.." : false)));
		
		if(!$dir or count($args)>1) {
			echo !$dir ? " This is not a project folder, type command in project folder \n" : 
				 "too much arguements : ".implode(" , ", $args)." \n";
			return;
		}

		$dir .= "/public/modules/$name";
		mkdir($dir);
		recurse_copy(__DIR__."/default-module", $dir);
		foreach(["css", "js", "config.php", "html"] as $ext) {
			rename("$dir/default-module.$ext", "$dir/$name.$ext");
		}
		echo "module $name created\n";
	}
	else {
		echo "specify module name";
	}
}
