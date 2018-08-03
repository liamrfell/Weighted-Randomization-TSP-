<?php
define('__PARSER__', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'dissertation\Class_Parser.php'); 
require_once __PARSER__;

 /**
   * Constructor
   * 
   * Handles initial nearest neighbour tour construction
   * 
   * @package    Randomized Weighting
   * @author     Liam Fell <1218996@chester.ac.uk>
   */
class Constructor extends Parser {	
	private $greedy_locations = array();
	private $cost; 
	private $ignore_list = array('0' => 0);
	private $number_of_cities;
	private $iteration_count = 0;
	private $last_run_flag = FALSE;
	private $tmp_cost = 9999999999999999; 
	
	/**
       * 
       * Pulblic interface
       *
       */
	public function generate_greedy($parsed_data){
		$this->greedy_constructor($parsed_data);		
		return array( 0 => $this->greedy_locations, 1 => floatval($this->cost));
	}
	
	/**
       * 
       * Bulk of nearest neighbour operation
       *
	   * @param array $parsed_data parsed data from which the nearest neighbour solution is built 
	   * @param bool $first_run this function is called recursivley, we detect recursive calls here
	   * @param integer $pointer position of the array we are constructing which we point to
       */
	private function greedy_constructor($parsed_data, $first_run = TRUE, $pointer = 0){		
		$current_matrix_position; 
		$this->number_of_cities = count($parsed_data);
		
		if($first_run == TRUE){ 
			reset($parsed_data);
			list($location, $matrix) = each($parsed_data); 
			$current_matrix_position = 0;
		}
		else{
			$count = 0;		
			$matrix;
			$location;
			foreach($parsed_data as $location_key => $matrix_value){
				if($count != $pointer){
					$count ++;
					continue;
				}
				else{			
					$current_matrix_position = $count;
					$matrix = $matrix_value;
					$location = $location_key;
					break;
				}
			}	
		}
		
		$next_matrix_position = $this->greedy_logic($matrix);	
		
		if($this->iteration_count == $this->number_of_cities){ //last iteration
			array_push($this->greedy_locations,array(
										"City" => $location,  
										"Array Position" => $current_matrix_position, 
										"Next City" => $this->get_city_name_by_index_position($parsed_data,$next_matrix_position), 
										"Next City Position" => $next_matrix_position, 
										"cost" => $matrix[0]
			));
			$this->cost = $this->cost + $matrix[0]; 
		}
		else{ //not the last iteration
		
		
		
			array_push($this->greedy_locations,array(
										"City" => $location,  
										"Array Position" => $current_matrix_position, 
										"Next City" => $this->get_city_name_by_index_position($parsed_data,$next_matrix_position), 
										"Next City Position" => $next_matrix_position, 
										"cost" => $this->tmp_cost
			));
			$this->cost = $this->cost + $this->tmp_cost;
		}
		
		array_push($this->ignore_list,$next_matrix_position); 
		if($this->iteration_count == $this->number_of_cities){ // last iteration
			return;
		}
		else{
			$this->greedy_constructor($parsed_data, FALSE, $next_matrix_position); // recurse
		}
	}
	
	/**
       * 
       * nearest neighbour logic
       *
	   * @param array $matrix parsed matrix data
	   * @param bool $last_run logic is different for the last recurse
	   * @return bool OR array 
       */
	private function greedy_logic($matrix, $last_run = FALSE){	
		$next_matrix_position;	
		$cost_to_first_city;
		if(count($this->ignore_list) == 1){ // first run
			for($m=0; $m<count($matrix); $m++){
				if($matrix[$m] == 0){
					continue;
				}
				elseif($matrix[$m] > 0 && $matrix[$m] < $this->tmp_cost){
					$this->tmp_cost = $matrix[$m];
					$next_matrix_position = $m;
				}
			}
		}
		else{ // first run + n
			$count = 0;
			$this->tmp_cost = 999999999;
			
			for($m=0; $m<count($matrix); $m++){ 	

				if($matrix[$m] == 0){ // skip the first
					$cost_to_first_city = $matrix[$m];
					$count++;
					continue;
				}
				elseif($this->in_ignore_array($count) == TRUE){ // ignore cities within the ignore list
					$count++;
					continue;
				}
				elseif($matrix[$m] > 0 && $matrix[$m] > $this->tmp_cost){ // path isn't shorter so ignore it
					$count++;
					continue;
				}
				elseif($matrix[$m] > 0 && $matrix[$m] < $this->tmp_cost){ // accept the shortest path 
					$this->tmp_cost = $matrix[$m];
					$next_matrix_position = $m;
					$count++;
				}
			}	
		}
		
		$this->iteration_count++;
		
		if(isset($next_matrix_position)){ // return the next city by it's matrix position
			return $next_matrix_position;
		}
		else{ //last iteration 
			return 0;
		} 
	}
	
	/**
       * 
       * checks if given value is in the ignore list, i.e it already exists in the partially constructed solution
       *
	   * @param integer $ignore_val node in which we ignore
	   * @return bool 
       */
	private function in_ignore_array($ignore_val){
		$ignore_array = $this->ignore_list;
		$flag = FALSE;
		
		foreach($ignore_array as $key => $value){
			if($value == $ignore_val){
				$flag = TRUE;
			}
			
		}
		return $flag;
	}
	
	/**
       * 
       * returns given city name from it's indexed position within the parsed data array
       *
	   * @param array $parsed_data array of parsed data
	   * @param array $pos postion which we require city name 
	   * @return string 
       */
	private function get_city_name_by_index_position($parsed_data,$pos){
		$locaion_name;
		$count = 0;
		foreach($parsed_data as $location => $matrix){
			if($count == $pos){
				$location_name = $location;
				break;
			}
			else{
				$count ++;
				continue;
			}
		}
		return $location_name;
	}
}