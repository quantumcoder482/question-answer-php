<?php

class Pagination {

	public $current_page;
	public $per_page;  
	public $total_count;
	 

	public function __construct($page=1, $per_page=20, $total_count=0){ 	
	$this->current_page = (int)$page;
	$this->per_page = (int)$per_page;
	$this->total_count = (int)$total_count;
	}

	  

	public function offset() {
	return ($this->current_page - 1) * $this->per_page;  
	}

	  

	public function total_pages() {  
	return ceil($this->total_count/$this->per_page);
	}
		
	  

	public function previous_page() {
	return $this->current_page - 1;
	}
	  
	  

	public function next_page() {
	return $this->current_page + 1;
	}

		

	public function has_previous_page() {
	return $this->previous_page() >= 1 ? true : false;
	}

		

	public function has_next_page() {
	return $this->next_page() <= $this->total_pages() ? true : false;
	}

}


?>