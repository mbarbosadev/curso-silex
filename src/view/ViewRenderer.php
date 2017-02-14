<?php

namespace SON\view;

class ViewRenderer {
	
	private $pathTemplates;
	private $templateName;
	
	public function __construct($pathTemplates) {
		$this->pathTemplates = $pathTemplates;
	}
	
	public function render($name, array $data = []) {

		$this->templateName = $name;
		
		extract($data);
		
		ob_start();
		include $this->pathTemplates."/{$this->templateName}.phtml";
		$saida = ob_get_clean();
		
		return $saida;
		
	}
	
}