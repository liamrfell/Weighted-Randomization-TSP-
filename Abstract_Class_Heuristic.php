<?php
/**
   * Heuristic 
   * 
   * Provides abstract and global functions for individual heuristics
   * 
   * @package    Randomized Weighting
   * @author     Liam Fell <1218996@chester.ac.uk>
   */
Abstract Class Heuristic{

	/**
       * 
       * abstract function enforcement
	   * @param array $improvement_solution current solution 
	   * @param array $distance_matrix array of distances
	   *
	   * @param array $parsed_data_in parsed data can be passed directly, the function uses parsed data in input_data.txt as default
       *
       */
	abstract public function generate_heuristic_solution($greedy_solution,$distance_matrix); // interaction with the hyper-heuristic
		
	/**
       * 
       * provides cost calculation between two nodes
	   * @param string $index_pos_name of node
	   * @param integer $index_pos_2_num position of node we travel to 
	   * @param array $matrix_data array of distances
	   * @return array
       *
       */
	protected function two_opt_move_cost($index_pos_name,$index_pos_2_num,$matrix_data){
		return $matrix_data[$index_pos_name][$index_pos_2_num];
	}
	
	/**
       * 
       * performs the 2 opt swap on i and k positions
	   * @param integer $i first array position
	   * @param integer $k second array position 
	   * @param array $matrix_data array of distances
	   * @current_solution current in run solution
	   * @return array
       *
       */	
	protected function two_opt_swap($i,$k,$matrix_data,$current_solution){
		
		if($i > $k){
			list($k, $i) = array($i, $k); //swap the variables over, we only handle the swap when $k is bigger than $i
		}
			
		$tmp_solution = $current_solution;
		$first_arr = $current_solution[$i];
		$last_arr = $current_solution[$k];
			
		//do first swap
		$tmp_solution[$i]["City"] = $last_arr["City"];
		$tmp_solution[$i]["Array Position"] = $last_arr["Array Position"];
		$tmp_solution[$i]["cost"] = $this->two_opt_move_cost($last_arr["City"],$tmp_solution[$i]["Next City Position"],$matrix_data);

		//do second swap
		if($k != count($current_solution) -1 && $i == 0){ //we are swapping on the first city
			$tmp_solution[$k]["City"] = $first_arr["City"];
			$tmp_solution[$k]["Array Position"] = $first_arr["Array Position"];
			$tmp_solution[$k]["Next City"] = $tmp_solution[$k+1]["City"];
			$tmp_solution[$k]["Next City Position"] = $tmp_solution[$k+1]["Array Position"];
			$tmp_solution[$k]["cost"] = $this->two_opt_move_cost($first_arr["City"],$tmp_solution[$k+1]["Array Position"],$matrix_data); 		
		
			$tmp_solution[count($tmp_solution)-1]["Next City"] = $tmp_solution['0']["City"];
			$tmp_solution[count($tmp_solution)-1]["Next City Position"] = $tmp_solution['0']["Array Position"];
			$tmp_solution[count($tmp_solution)-1]["cost"] = $this->two_opt_move_cost($tmp_solution[count($tmp_solution)-1]["City"],$tmp_solution['0']["Array Position"],$matrix_data); 	
		}
		elseif($k == count($current_solution) -1 && $i > 0){ //we are swaping the last city with another with one other than 0
			$tmp_solution[$k]["City"] = $first_arr["City"];
			$tmp_solution[$k]["Array Position"] = $first_arr["Array Position"];
			$tmp_solution[$k]["Next City"] = $tmp_solution['0']["City"];
			$tmp_solution[$k]["Next City Position"] = $tmp_solution['0']["Array Position"];
			$tmp_solution[$k]["cost"] = $this->two_opt_move_cost($first_arr["City"],$tmp_solution['0']["Array Position"],$matrix_data); 				
		} 
		elseif($k == count($current_solution) -1 && $i == 0){ //we are performing an extremety swap 
			$tmp_solution[$k]["City"] = $first_arr["City"];
			$tmp_solution[$k]["Array Position"] = $first_arr["Array Position"];
			$tmp_solution[$k]["Next City"] = $tmp_solution[$i]["City"];
			$tmp_solution[$k]["Next City Position"] = $tmp_solution[$i]["Array Position"];
			$tmp_solution[$k]["cost"] = $this->two_opt_move_cost($first_arr["City"],$tmp_solution[$k]["Next City Position"],$matrix_data); 		
		}
		else{ 
			$tmp_solution[$k]["City"] = $first_arr["City"];
			$tmp_solution[$k]["Array Position"] = $first_arr["Array Position"];
			$tmp_solution[$k]["cost"] = $this->two_opt_move_cost($first_arr["City"],$tmp_solution[$k]["Next City Position"],$matrix_data);
		}
		
		//do first swap predecessor
		if($i > 0){
			$predecessor = $i - 1;
			$tmp_solution[$predecessor]["Next City"] = $tmp_solution[$i]["City"];
			$tmp_solution[$predecessor]["Next City Position"] = $tmp_solution[$i]["Array Position"];
			$tmp_solution[$predecessor]["cost"] = $this->two_opt_move_cost($tmp_solution[$predecessor]["City"],$tmp_solution[$predecessor]["Next City Position"],$matrix_data);
		}
		
		//do second swap predecessor
		$predecessor = $k - 1;
		$tmp_solution[$predecessor]["Next City"] = $tmp_solution[$k]["City"];
		$tmp_solution[$predecessor]["Next City Position"] = $tmp_solution[$k]["Array Position"];
		$tmp_solution[$predecessor]["cost"] = $this->two_opt_move_cost($tmp_solution[$predecessor]["City"],$tmp_solution[$predecessor]["Next City Position"],$matrix_data);
		
		return $tmp_solution;
	}
	
	/**
       * 
       * reads in values to solution array when performing reverse swap 
	   * @param array $finalised_solution 
	   * @param array $new_order_after_intersection
	   * @param array $matrix_data array of distances
	   * @return array
       *
       */	
	protected function read_in_new_values_from_reverse_swap_array($finalised_solution,$new_order_after_intersection,$distance_matrix){
		$finalised_solution_count = count($finalised_solution) -1;
		$new_order_count = count($new_order_after_intersection);
		$solution = $finalised_solution;
		
		//manage the intersection point
		$intersection_point = end($finalised_solution);
		$intersection_point["Next City"] = $new_order_after_intersection[0]["City"];
		$intersection_point["Next City Position"] = $new_order_after_intersection[0]["Array Position"];
		$intersection_point["cost"] = $this->two_opt_move_cost($intersection_point["City"],$new_order_after_intersection[0]["Array Position"],$distance_matrix);
		$solution[$finalised_solution_count] = $intersection_point;
		reset($finalised_solution);
		
		//manage swap
		$last_itteration = count($distance_matrix);
		$count = 0;
		for($i=$finalised_solution_count + 1; $i<count($distance_matrix); $i++){
	
			if($count+1 >= $new_order_count){
				break;
			}
			
			$solution[$i]["City"] = $new_order_after_intersection[$count]["City"];
			$solution[$i]["Array Position"] = $new_order_after_intersection[$count]["Array Position"];
			$solution[$i]["Next City"] = $new_order_after_intersection[$count+1]["City"];
			$solution[$i]["Next City Position"] = $new_order_after_intersection[$count+1]["Array Position"];
			$solution[$i]["cost"] = $this->two_opt_move_cost($new_order_after_intersection[$count]["City"],$new_order_after_intersection[$count+1]["Array Position"],$distance_matrix);
			$count++;
		}
		
		$solution[$i]["City"] = $new_order_after_intersection[$count]["City"];
		$solution[$i]["Array Position"] = $new_order_after_intersection[$count]["Array Position"];
		$solution[$i]["Next City"] = $solution[0]["City"];
		$solution[$i]["Next City Position"] = $solution[0]["Array Position"];
		$solution[$i]["cost"] = $this->two_opt_move_cost($new_order_after_intersection[$count]["City"],$solution[0]["Array Position"],$distance_matrix);
		
		return $solution;

	}
	
	/**
       * 
       * performs reverse swap between city 1 and city 2
	   * @param array $city_1 
	   * @param array $city_2
	   * @param greedy_solution
	   * @param array $matrix_data array of distances
	   * @return array
       *
       */
	protected function reverse_swap_between_cities($city_1,$city_2,$greedy_solution,$distance_matrix){
		$tmp_solution = $greedy_solution;
		$diff = abs($city_2 - $city_1);
		// do the flip on the sliced array
		$tmp_sliced_array_level_1 = array();
		$tmp_sliced_array_level_1 = array_slice($greedy_solution,$city_1,$diff);
		$tmp_sliced_array_level_1 = array_reverse($tmp_sliced_array_level_1);
			
		//rebuild the temporary array
		$tmp_solution[$city_1-1]["Next City"] = $tmp_sliced_array_level_1[0]["City"];
		$tmp_solution[$city_1-1]["Next City Position"] = $tmp_sliced_array_level_1[0]["Array Position"];
		$tmp_solution[$city_1-1]["cost"] = $this->two_opt_move_cost($tmp_solution[$city_1-1]["City"],$tmp_sliced_array_level_1[0]["Array Position"],$distance_matrix);
		$count = 0;
		for($i=$city_1;$i<$city_2-1;$i++){
			$tmp_solution[$i]["City"] = $tmp_sliced_array_level_1[$count]["City"];
			$tmp_solution[$i]["Array Position"] = $tmp_sliced_array_level_1[$count]["Array Position"];
			$tmp_solution[$i]["Next City"] = $tmp_sliced_array_level_1[$count+1]["City"];
			$tmp_solution[$i]["Next City Position"] = $tmp_sliced_array_level_1[$count+1]["Array Position"];
			$tmp_solution[$i]["cost"] = $this->two_opt_move_cost($tmp_sliced_array_level_1[$count]["City"],$tmp_sliced_array_level_1[$count+1]["Array Position"],$distance_matrix);
			$count++;
		}
		end($tmp_sliced_array_level_1);       
		$key = key($tmp_sliced_array_level_1); 
		$tmp_solution[$city_2-1]["City"] = $tmp_sliced_array_level_1[$key]["City"];
		$tmp_solution[$city_2-1]["Array Position"] = $tmp_sliced_array_level_1[$key]["Array Position"];
		$tmp_solution[$city_2-1]["cost"] = $this->two_opt_move_cost($tmp_sliced_array_level_1[$key]["City"],$tmp_solution[$city_2]["Array Position"],$distance_matrix); 
		
		return $tmp_solution;
	}
	
	/**
       * 
       * performs reverse swap on all cities preeceeding a given city
	   * @param array $city_position 
	   * @param greedy_solution
	   * @param array $matrix_data array of distances
	   * @return array
       *
       */
	protected function reverse_preceeding($city_position,$greedy_solution,$distance_matrix){
		$tmp_solution = $greedy_solution;
		
		$tmp_sliced_array_level_1 = array();
		$tmp_sliced_array_level_1 = array_slice($greedy_solution,0,$city_position );
			
		$tmp_sliced_array_level_1 = array_reverse($tmp_sliced_array_level_1);

		$count = 0;
		for($i=0; $i<$city_position-1; $i++){
			if($count == $city_position){
				break;
			}
			$tmp_solution[$i]["City"] = $tmp_sliced_array_level_1[$i]["City"];
			$tmp_solution[$i]["Array Position"] = $tmp_sliced_array_level_1[$i]["Array Position"];
			$tmp_solution[$i]["Next City"] = $tmp_sliced_array_level_1[$i+1]["City"];
			$tmp_solution[$i]["Next City Position"] = $tmp_sliced_array_level_1[$i+1]["Array Position"];
			$tmp_solution[$i]["cost"] = $this->two_opt_move_cost($tmp_sliced_array_level_1[$i]["City"],$tmp_sliced_array_level_1[$i+1]["Array Position"],$distance_matrix);
			$count++;
		}
		//intersection
		$tmp_solution[$count - 1]["Next City"] = $tmp_sliced_array_level_1[$count]["City"];
		$tmp_solution[$count - 1]["Next City Position"] = $tmp_sliced_array_level_1[$count]["Array Position"];
		$tmp_solution[$count - 1]["cost"] = $this->two_opt_move_cost($tmp_solution[$count - 1]["City"],$tmp_sliced_array_level_1[$count]["Array Position"],$distance_matrix);
		$tmp_solution[$count]["City"] = $tmp_sliced_array_level_1[$count]["City"];
		$tmp_solution[$count]["Array Position"] = $tmp_sliced_array_level_1[$count]["Array Position"];
		$tmp_solution[$count]["cost"] = $this->two_opt_move_cost($tmp_sliced_array_level_1[$count]["City"],$tmp_solution[$count]["Next City Position"],$distance_matrix);
		//last
		end($tmp_solution);
		$key = key($tmp_solution);
		$tmp_solution[$key]["Next City"] = $tmp_solution[0]["City"];
		$tmp_solution[$key]["Next City Position"] = $tmp_solution[0]["Array Position"];
		$tmp_solution[$key]["cost"] = $this->two_opt_move_cost($tmp_solution[$key]["City"],$tmp_solution[$key]["Next City Position"],$distance_matrix);
		
		return $tmp_solution;
	}
	
	/**
       * 
       * performs reverse swap on all proceeding cities from a given city
	   * @param array $city_position 
	   * @param greedy_solution
	   * @param array $matrix_data array of distances
	   * @return array
       *
       */
	protected function proceeding_reverse_swap($city_position,$greedy_solution,$distance_matrix){
		$tmp_solution = array();
		// do the flip on the sliced array
		$tmp_sliced_array_level_1 = array();
		$tmp_sliced_array_level_1 = array_slice($greedy_solution,$city_position);
		$tmp_sliced_array_level_1 = array_reverse($tmp_sliced_array_level_1);
		
		// delete the items proceeding our intersection point
		$keys = array_keys($greedy_solution); 
		$count = array_search($city_position - 1, $keys); 
		$tmp_solution = array_slice($greedy_solution, 0, $count + 1, true);
		
		return $this->read_in_new_values_from_reverse_swap_array($tmp_solution,$tmp_sliced_array_level_1,$distance_matrix);

	}
	
	/**
       * 
       * sums the tour cost of a given tour
	   * @param $heuristic_solution
	   * @return array
       *
       */
	protected function sum($heuristic_solution){
	$cost = 0;
		foreach($heuristic_solution as $key => $descript_arr){
			foreach($descript_arr as $descript_key => $descript_val){
				if($descript_key == 'cost' || $descript_key == 'Cost'){
					$cost = $cost + $descript_val;
				}
			}
		}
	}

	/**
       * 
       * if a tour has 12 cities we could perform 2 opt on city 12, and 13. 13 doesn't exist in the tour so we roll back to 0
	   * @param $number_to_check if number is greater than number of cities in tour we roll back to 0
	   * @return string  
       *
       */
	protected function sanitized_swap_number($number_to_check,$greedy_solution){
		$size = count($greedy_solution) -1;
		if($number_to_check > $size){
			return abs($size - $number_to_check) -1;
		}
		return $number_to_check;
	}
	
	
}