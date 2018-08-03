<?php

require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'dissertation\Abstract_Class_Heuristic.php';

/* 
- Heuristic5 reverse preceeding cities from a random point. Find a random city in the tour, flip all preceeding cities from this random point. 
*/
class Heuristic5 extends Heuristic{
	
	private $greedy_solution = array();
	private $distance_matrix = array();
	
		/**
       * 
       * generates the solution
	   * @return array
       *
       */
	public function generate_heuristic_solution($greedy_solution,$distance_matrix){
		$this->greedy_solution = $greedy_solution;
		$this->distance_matrix = $distance_matrix;
		$city = mt_rand( 3, count($distance_matrix) -2 );		
		
	/* 	$city = 30;
		
		$solution = $this->reverse_preceeding($city,$greedy_solution,$distance_matrix);
		
		echo "<pre>";
		var_dump();
		exit;
		
		for($i=0;$i<count($this->greedy_solution);$i++){
			echo $solution[$i]["City"];
			echo "<br>";
		}
		 */
		
		return $this->reverse_preceeding($city,$greedy_solution,$distance_matrix);	
	}
}
