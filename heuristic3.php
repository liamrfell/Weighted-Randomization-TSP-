<?php

require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'dissertation\Abstract_Class_Heuristic.php';

/* 
- Heuristic3 reverse proceeding. Find a random point in the tour and flip all proceeding cities.
*/
class Heuristic3 extends Heuristic{
	
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
			
		$city = mt_rand ( 1 , count($this->distance_matrix) - 1); 
		
		$city_array = array();
			
		/* $solution = $this->proceeding_reverse_swap($city,$this->greedy_solution,$this->distance_matrix); 
			
		for($i=0;$i<count($solution);$i++){
			$city_array[] = $solution[$i]["City"];
		}
		
		if(array_unique($city_array)<$city_array){
			echo $city;
			echo "<br>";
			echo "<pre>";
			foreach($city_array as $city){
				echo "<br>";
				echo $city;
			}
			exit;
		} */
			
		return $this->proceeding_reverse_swap($city,$this->greedy_solution,$this->distance_matrix);
		
	}
}

