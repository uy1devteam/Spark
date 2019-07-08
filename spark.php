<?php

include "utilities.php";

$COMMANDS = [ 
	"build"=>["", []],
	"create"=>[
		"module"=>["", []],
		"project"=>["", []]
	],
	"delete"=>[
		"module"=>["", []],
		"project"=>["", []]
	],
	"export"=>[
		"module"=>["", []]
	],
	"import"=>[
		"module"=>["", []]
	], 
	"new"=>[
		"project"=>["", []]
	]
];		

// set up args


// check if args are enabled
if( !isset($argc) ) {
	echo "enable argv and argc for php to be able to pass arguement to the script \n"; return ;
}

if($argc==1) { echo "you have not specified any command\n"; return; }

unset( $argv[0]); // remove script name
$argc--;

$argv = array_values($argv);
for( $i=0; $i<$argc; $i++ ) {
	if( !(startsWith($argv[$i], "-")  or ( $i>0 and startsWith($argv[$i-1], "-") ) ) ) {
		$args[] = $argv[$i];
	}
}

parse($args); 
