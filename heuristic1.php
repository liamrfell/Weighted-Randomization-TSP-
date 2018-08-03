<?php

require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'dissertation\Abstract_Class_Heuristic.php';

/* 
- Heuristic1 local swap. Swap random city position with another city within 2 moves from the randlomly selected point
*/
class Heuristic1 extends Heuristic{
	
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
			
		$first = mt_rand ( 1 , count($this->distance_matrix) - 1); //this one in the end		
		do {   
			$last = mt_rand ( $first , $first + 2); //consider the swap within the range of +/-2
		} while(in_array($last, array($first))); //ignore values which are the same as $first
				
		$last = $this->sanitized_swap_number($last,$this->greedy_solution); //get the next number (+1 or +2) and sanitize, i.e roll back over to 0 after we go past max array size
	
		return $this->two_opt_swap($first,$last,$this->distance_matrix,$this->greedy_solution);		
	}
}

