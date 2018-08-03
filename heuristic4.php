<?php

require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'dissertation\Abstract_Class_Heuristic.php';

/* 
- Heuristic4 reverse between two points. Find two random points in the tour and reverse city positions between the two points. 
*/
class Heuristic4 extends Heuristic{
	
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
		
		$diff = 1;
		do { //make sure we have more than two cities to reverse
			$city_2 = mt_rand ( 3 , count($this->distance_matrix) - 1); 
			$city_1 = mt_rand ( 2, $city_2 - 1); 
			$diff = abs($city_2 - $city_1);
		} while($diff === 1);
		
		//return $this->reverse_swap_between_cities($city_1,$city_2,$greedy_solution,$distance_matrix);
		
		return $this->greedy_solution;
	}
}
