<?php 

	/* /////////////////////////////////////////////////////////////////////////
		        		         Pagination Class
	///////////////////////////////////////////////////////////////////////// */
	
   
	class Pagination {
		
		public $current_page;
		public $per_page;
		public $total_count;
		
		public function __construct($page=1,$per_page=20,$total_count=0){
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
		
		public static function current_page($page=1){
			$params = Url::uris();
			if(isset($_GET['page'])){
				$page = $_GET['page'];
			}elseif($params && in_array("page",$params)){
				$page = $params[array_search("page",$params)+1];
			}
			return $page;
		}
		
	}
	
	/* Implementing Pagination 
	
	$page = Pagination::current_page(); 
	$per_page = X; 
	$total_count = Class::count_all(); 
	$pagination = new Pagination($page,$per_page,$total_count);
	$class = Class::find_by_pagination($per_page,$pagination->offset()); 
	foreach($class as $cla): ?> 
	
	<?php if($pagination->total_pages() > 1){
                        
		if($pagination->has_previous_page()){
			echo '<a href="/talent/page/'.$pagination->previous_page().'">&laquo; Previous </a>';
		}
		
		for($i=1; $i<=$pagination->total_pages(); $i++){
			echo ($i == $page) ? '<span class="selected"> '.$i.' </span>' : '<a href="/talent/page/'.$i.'"> '.$i.' </a>';
		}
		
		if($pagination->has_next_page()){
			echo '<a href="/talent/page/'.$pagination->next_page().'"> Next &raquo;</a>';
		}
		
	} ?>
	
	*/


?>