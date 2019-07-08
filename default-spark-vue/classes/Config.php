<?php



class Config  {
	
	public $ERROR_MESSAGE;
	public $WRAPPER;
	public $ELEMENT;

	public function __construct( array $customConfigs ) {
		
		$this->ERROR_MESSAGE =  "Current page has no module or modules could not be loaded";
		$this->WRAPPER =  "custom";
		$this->ELEMENT =  "div";
		

		// custom configs
		foreach( $customConfigs as $key=>$value ) {
			$key = strtoupper($key);
			$this->$key = trim($value);
		}

	}

	public function wrap(): string {
		
		if( $config->WRAPPER != "custom" ) {
			return "<".$config->ELEMENT." class = \"".$module."\" >".$html."</".$config->ELEMENT.">";
		}
		
		else {
			return "<".$config->WRAPPER.">".$html."<".$config->WRAPPER.">";
		}

		return $this->routes;
	}

}
