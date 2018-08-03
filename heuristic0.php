<?php

require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'dissertation\Abstract_Class_Heuristic.php';

/* 
- Heuristic0 extremety swap. Swaps the starting city, with the ending city 
*/

class Heuristic0 extends Heuristic{
	
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
		
		$first = $this->select_first();
		$last = $this->select_last($this->greedy_solution);
	
		return $this->two_opt_swap($first,$last,$this->distance_matrix,$this->greedy_solution);
	}
	
	public function select_last($greedy_solution){
		return count($this->greedy_solution) - 1;
	}	
	
	public function select_first(){
		return 0;
	}
}

