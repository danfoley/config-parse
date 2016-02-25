<?
/* class to parse a test config file
 *
 * Boolean like string values (on/off, yes/no, true/false) will return true/false
 * Numberic string values will return real numeric values
 *
 * Ingore error on input.
 *
 * useage: $cp = new config_parse();
 *         $config_parse->parse_file($input_file);
 *
 * $input_file - the file to be parsed. If a full path is not specified, will assume the file is in the same path as the script calling this class.
 * returns an array of config values or false if a fatal error is encountered.
 *
 * coded by Dan Foley: dan@micamedia.com
 */

class config_parse {

	var $config_array;
	var $config_file;
	var $comment_marker = "#";
	var $bool_true_array = array("on", "yes", "true");
	var $bool_false_array = array("off", "no", "false");

	function config_parse() {

		$this->config_array = array();
		$this->config_file = "";

	}

	// parse the config file into the config array
	public function parse_file($input_file) {
		
		if ($input_file=="") {
			return false;
		}
	
		// locate the config file
		if (file_exists($input_file)) {
			$this->config_file = $input_file;
		} elseif (file_exists(dirname(__FILE__) . $input_file)) {
			$this->config_file = dirname(__FILE__) . $input_file;
		} else {
			return false;
		}

		$this->do_parse();
		return $this->config_array;

	}

	// read the lines of the file
	private function do_parse() {
		$handle = fopen($this->config_file, "r");
		if ($handle) {
			while (($line = fgets($handle)) !== false) {
				$this->process_config_line($line);
			}
			fclose($handle);
		} else {
			return false;
		} 
	}

	// remove blank lines and comments
	private function process_config_line($line) {
		$line = trim($line);

		// blank
		if ($line=="") {
			return;

		// comment
		} elseif (substr($line,0,1)==$this->comment_marker) {
			return;

		// actual value
		} else {
			$this->process_name_value($line);
		}
	}

	// process a line with an actual name/value pair in it.
	private function process_name_value($line) {

		$line_split = explode("=",$line);
		$name = trim($line_split[0]);
		$value = trim($line_split[1]);

		$this->config_array[$name]=$this->process_value($value);
	}

	private function process_value($value) {

		if (in_array($value,$this->bool_true_array)) return true;
		if (in_array($value,$this->bool_false_array)) return false;
		if (is_numeric($value)) return $value+0;
		return $value;

	}
}
?>