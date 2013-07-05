
<?php

Cogumelo::Load('tableCol');

class genericTableCol extends tableCol {

	// $col: VO object
	// $table: table parameters
	function processCol($col, $table) {
		return $col->getter( $this->getId()  );  // here you can modify the returned data
	}

}