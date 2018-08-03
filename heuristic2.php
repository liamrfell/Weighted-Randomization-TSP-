<?php

require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'dissertation\Abstract_Class_Heuristic.php';

/* 
- Heuristic2 wide swap. Swap random city position with another city within 5 moves of the randomly selected point.
*/
class Heuristic2 extends Heuristic{
	
	private $greedy_solution = array();
	private $distance_matrix = array();
	
	/**
       * 
       * generates the solution
	   * @return array
       *
       */
	public function generate_heuristic_solution($greedy_solution,$distance_matrix){
		
		if(count($distance_matrix) < 12){ 
			return $greedy_solution;
		}
		
		$this->greedy_solution = $greedy_solution;
		$this->distance_matrix = $distance_matrix;		
		
		$first = mt_rand ( 1 , count($this->distance_matrix) - 1); 
		do {   
			$last = mt_rand ( $first , $first + 5); //consider the swap within the range of +/-2
		} while(in_array($last, array($first))); //ignore values which are the same as $first
		
		$last = $this->sanitized_swap_number($last,$this->greedy_solution); //get the next number (+1 or +2) and sanitize, i.e roll back over to 0 after we go past max array size
			
		return $this->two_opt_swap($first,$last,$this->distance_matrix,$this->greedy_solution);		
	}
}

