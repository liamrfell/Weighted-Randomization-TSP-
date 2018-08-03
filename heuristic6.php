<?php

require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'dissertation\Abstract_Class_Heuristic.php';

/* 
- Heuristic6 single 2-opt run. Perform a sinlge 2-opt run from point 0 in the tour to the final point in the tour
*/
class Heuristic6 extends Heuristic{
	
	private $greedy_solution = array();
	private $distance_matrix = array();
	
	public function generate_heuristic_solution($greedy_solution,$distance_matrix){
		$this->greedy_solution = $greedy_solution;
		$this->distance_matrix = $distance_matrix;

		$cost = $this->sum($this->greedy_solution);
		
		$first = 1;
		$last = count($greedy_solution) -3;
		
		for($i=1; $i<=$last; $i++){
			$solution = $this->two_opt_swap($i,$i+1,$this->distance_matrix,$this->greedy_solution);
			$in_run_cost = $this->sum($solution);
			if($in_run_cost < $cost){
				$this->greedy_solution = $solution;
			}
		}
	
		return $this->greedy_solution;		
	}
}

