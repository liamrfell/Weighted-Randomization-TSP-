<?php

require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'dissertation\Abstract_Class_Heuristic.php';

/* 
- Heuristic7 3 opt swap on biggest city to city code. We attempt three different swaps around the most costful move and return the most efficient of the three potential tours.
*/
class Heuristic7 extends Heuristic{
	
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

		$position_of_biggest_cost = $this->get_position_of_biggest_cost(array_map(array($this,"get_array_of_costs"),$greedy_solution));				
		$position_before_biggest_cost = $this->get_position_back($position_of_biggest_cost);
		$position_after_biggest_cost = $this->get_position_forward($position_of_biggest_cost);
	
		$swap_1 = $this->two_opt_swap($position_of_biggest_cost,$position_before_biggest_cost,$this->distance_matrix,$this->greedy_solution);
		$swap_2 = $this->two_opt_swap($position_of_biggest_cost,$position_after_biggest_cost,$this->distance_matrix,$swap_1);
		$three_opt_solution_1 = $this->two_opt_swap($position_of_biggest_cost,$this->get_position_forward($position_after_biggest_cost),$this->distance_matrix,$swap_2);
		$three_opt_solution_2 = $this->two_opt_swap($position_before_biggest_cost,$this->get_position_forward($position_after_biggest_cost),$this->distance_matrix,$swap_2);
		$three_opt_solution_3 = $this->two_opt_swap($position_of_biggest_cost,$position_before_biggest_cost,$this->distance_matrix,$three_opt_solution_2);
		
		$best_solution = $this->get_best_solution($three_opt_solution_1,$three_opt_solution_2,$three_opt_solution_3);
	
		return $$best_solution;
	}	

	/**
       * 
       * selects 3-opt solution with lowest overall cost
	   * @return integer
       *
       */
	private function get_best_solution($three_opt_solution_1,$three_opt_solution_2,$three_opt_solution_3){
		$solution_array = array();
		
		$solution_array['three_opt_solution_1'] = $this->sum($three_opt_solution_1);
		$solution_array['three_opt_solution_2'] = $this->sum($three_opt_solution_2);
		$solution_array['three_opt_solution_3'] = $this->sum($three_opt_solution_3);
		
		$key = array_keys($solution_array, min($solution_array));	//get key of best solution	
		return $key[0];
		
	}
	
	/**
       * 
       * selects position after costful move
	   * @return integer
       *
       */
	private function get_position_forward($position){
		$position = $position + 1;
		
		if($position + 1 > count($this->greedy_solution)){
			return 0;
		}
		
		return $position;
		
	}
	
	/**
       * 
       * selects position before costful move
	   * @return integer
       *
       */
	private function get_position_back($position){
		if($position == 0){
			return count($this->greedy_solution) -1;
		}
		
		return $position - 1;
	}
	
	/**
       * 
       * gets cost
	   * @return integer
       *
       */
	private function get_array_of_costs($solution_arr){
		return $solution_arr["cost"];
	}
	
	/**
       * 
       * gets array key of biggest costing move
	   * @return integer
       *
       */
	private function get_position_of_biggest_cost($costs_array){
		$key = array_keys($costs_array, max($costs_array));
		return $key[0];
	}
}

