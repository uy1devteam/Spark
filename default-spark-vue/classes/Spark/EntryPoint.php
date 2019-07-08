<?php
namespace Spark;class EntryPoint {
private $route;
private $routes;
public $html;
	public $css;
	public $js;
	private $wrapper;
	private $loaded;
	private $config;

	public function __construct(string $route, \Routes $routes, \Config $config) {
		$this->route = $route;
		$this->routes = $routes;
		$this->config = $config;
		$this->checkUrl();
		$this->loaded = [
			"css"=>[],
			"js"=>[]
		];

	}

	private function checkUrl() {
		if ($this->route !== strtolower($this->route)) {
			http_response_code(301);
			header('location: ' . strtolower($this->route));
		}
	}
	
	private function makePath($file, $type) : string {
		return "modules/".$file."/".$file.".".$type;
	} 
	
	private function makeCurrentPath($file, $type, $path=null) : string {
		return __DIR__."/../../public/".$this->makePath($file, $type, $path);
	}
	
	private function makePublicPath($file, $type) : string {
		return "./".$this->makePath($file, $type);
	}
	
	private function makeCurrentDepPath($file, $type="") : string {
		return __DIR__."/../../public/Assets/libaries/".$file.( endsWith($file, $type ) ? "": $type) ;
	}
	
	private function makePublicDepPath($file, $type="") : string {
		return "./Assets/libaries/".$file.( endsWith($file, $type ) ? "": $type) ;
	}
	
	
	private function isValidFile($file, $type, $path=null) : bool {
		$file = $path ?? $this->makeCurrentPath($file, $type);
		return (file_exists($file) and !(function_exists('filesize') ? filesize($file)==0 : trim( file_get_contents($file) )=='' ));
	}
	
	private function getHtml($file) : string {
		$html = "";
		if( $this->isValidFile( $file, "html")  ) {
			ob_start();
			include $this->makeCurrentPath($file, "html");
			$html =  ob_get_clean();
			
			$html = $this->config->WRAPPER != "custom" ?
				"<".$this->config->ELEMENT." class = \"".$file."\" >".$html."</".$this->config->ELEMENT.">" :
				$html =  "<app-".$file.">".$html."</app-".$file.">";	
		}
		
		return $html;
	}
	private function getCss($file, $type="css", $localpath=null, $path=null ) : string  {
		
		if( $this->isValidFile($file, $type, $localpath) and !in_array( trim($file), $this->loaded["css"]) ) {
			$this->loaded["css"][] = trim($file);
			return "<link href=\"".( $path ?? $this->makePublicPath($file, $type))."\" rel=\"stylesheet\" />"; 
		}
		
		return "";
	}
	
	private function getJs($file, $type="js", $localpath=null, $path=null) : string  {
		
		if( $this->isValidFile($file, $type, $localpath) and !in_array( trim($file), $this->loaded["js"]) ) {
			$this->loaded["js"][] = trim($file);
			return "<script src=\"".( $path ?? $this->makePublicPath($file, $type))."\" ></script>";
		}
		
		return "";
	}
	
	private function loadModule( string $module ) {
		$this->html .= $this->getHtml($module);
		$this->css .= $this->getCss($module);
		$this->js .= $this->getJs($module);
	}

	
	private function loadDependencies(array $dependencies ) {
		if(!$dependencies) { return;}
		$cssDeps = $dependencies["css"] ?? [];
		$jsDeps = $dependencies["js"] ?? [];
		$moduleDeps = $dependencies["modules"] ?? [];
		
		foreach( $cssDeps as $dep) {
			if( !in_array( trim($dep), $this->loaded["css"]) and !strpos($dep, "http") ) {
				$this->css .= $this->getCss( $dep,"", $this->makeCurrentDepPath($dep, "css"), $this->makePublicDepPath($dep, "css"));
				$this->loaded["css"][] = trim($dep);
			}
		}
		
		foreach( $jsDeps as $dep) {
			if( !in_array( trim($dep), $this->loaded["js"]) ) {
				$this->js .= $this->getJs( $dep,"", $this->makeCurrentDepPath($dep, "js"), $this->makePublicDepPath($dep, "js"));
				$this->loaded["js"][] = trim($dep);
			}
		}
		
		foreach( $moduleDeps as $dep) {
			$this->loadModule($dep);
		}
		
	}

	public function run() {

		$routes = $this->routes->getRoutes() ;	
		$route = $routes[ $this->route] ?? $routes['404'];
		$modules = $route['modules'] ?? $routes['500']['modules'];
		$dependencies = $route['dependencies'] ?? [];
		
		
		$this->loadDependencies( $dependencies );		
		foreach($modules as $module) {
			include $this->makeCurrentPath($module, "config.php");
			if( $dependencies ) { $this->loadDependencies( $dependencies ); }
			$this->loadModule( $module );

		}
				
	}

}
