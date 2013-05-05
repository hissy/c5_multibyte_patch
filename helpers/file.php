<?php
defined('C5_EXECUTE') or die("Access Denied.");
class FileHelper extends Concrete5_Helper_File {

	/** 
	 * Takes a path to a file and sends it to the browser, streaming it, and closing the HTTP connection afterwards. Basically a force download method
	 * @param stings $file
	 */
	public function forceDownload($file) {
		session_write_close();
		ob_clean();
		header('Content-type: application/octet-stream');
		$filename = basename($file);
		$asciiname = mb_convert_encoding($filename, "US-ASCII", "UTF-8");
		if ( $filename == $asciiname ) {
			header("Content-Disposition: attachment; filename=\"$filename\"");
		} else {
			$filename = strtolower(APP_CHARSET) . "'" . strtolower(LANGUAGE) . "'" . rawurlencode($filename);
			header("Content-Disposition: attachment; filename*=$filename");
		}
		header('Content-Length: ' . filesize($file));
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);
		header("Content-Transfer-Encoding: binary");
		header("Content-Encoding: plainbinary");
	
		$buffer = '';
		$chunk = 1024*1024;
		$handle = fopen($file, 'rb');
		if ($handle === false) {
			return false;
		}
		while (!feof($handle)) {
			$buffer = fread($handle, $chunk);
			print $buffer;
		}
		
		fclose($handle);
		exit;		
	}
	
	/** 
	 * Cleans up a filename and returns the cleaned up version
	 * @param string $file
	 * @return string @file
	 */
	public function sanitize($file) {
		$file = mb_convert_encoding($file,"UTF-8","auto");
		$file = preg_replace("/[\/:*?\"<>|\s]/u", "_", $file);
		return trim($file);
	}

}