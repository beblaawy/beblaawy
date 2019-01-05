<?php

namespace Zarboxa\Core\Views;

class View {

	/*
	* The Html content in the view
	*/
	public $content = '';

	/*
	* Data sent to the view to be extracted
	*/
	public $data = [];

	public static function make(String $file, array $data = []){
		if (!$file) {
			throw new Exception("The file name is missed", 1);
		}
		$path = VIEW_PATH . $file . '.php';

		if (!file_exists($path)) {
			throw new Exception("This view : " . $file . " is not exist", 1);
		}

		$content = file_get_contents( $path );

		$content = str_replace("{{", "<?php echo", $content);
		$content = str_replace("}}", "?>", $content);

		$view = new self;
		$view->content = $content;
		$view->data    = $data;

		return $view;
	}

	/*
	* This function is used to evaluate the view content
	*/
	public static function display(View $view){
		/* Extract the Associative array passed to the function */
		extract($view->data);
		eval("?>{$view->content}");
	}

	/*
	* Add data array to this view
	*/
	public function with(array $data){
		$this->data = $data;

		return $this;
	}

}