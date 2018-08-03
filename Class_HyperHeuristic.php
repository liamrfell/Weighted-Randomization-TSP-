<?php
define('__CONSTRUCTOR__', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'dissertation\Class_Constructor.php'); 
define('__BASEPATH__', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'dissertation'); 
require_once __CONSTRUCTOR__;

define("NUMBER_OF_HEURISTICS", 7);
for($t=0; $t<=NUMBER_OF_HEURISTICS; $t++ ){
	require_once __BASEPATH__ . DIRECTORY_SEPARATOR . 'heuristic' . $t . '.php'; //including all scripts here
}

 /**
   * HyperHeuristic 
   * 
   * Handles improvement using the Randomized Weighting framework
   * 
   * @package    Randomized Weighting
   * @author     Liam Fell <1218996@chester.ac.uk>
   */
class HyperHeuristic extends Constructor{	
	private $number_of_heuristics = NUMBER_OF_HEURISTICS; //i.e the number of low level heuristics (STARTING FROM 0)
	
	//run time  metrics
	private $return_time = 0;
	private $safe_buffer_time = 5;
	
	//matrix data
	private $matrix_data = array();
	
	//costs
	private $in_run_cost; 
	
	//solutions
	private $in_run_solution_array = array();
	
	//weightings
	private $weighting_array;
	
	/**
       * 
       * Pulblic function go, provides interface
	   *
	   * @param array $parsed_data_in parsed data can be passed directly, the function uses parsed data in input_data.txt as default
       *
       */
	public function go($parsed_data_in = FALSE){ 
		if($parsed_data_in === FALSE){ //if we aren't passing in parsed data directly, get data from the input_data.txt file
			$parsed_data_in = json_decode(file_get_contents('./input_data.txt', FILE_USE_INCLUDE_PATH),TRUE);
		}
			
		$this->generate_return_unix_time();
		$this->matrix_data = $parsed_data_in;
		$this->generate($parsed_data_in);
	}
	
	/**
       * 
       * Generates a safe run time for the hyperheuristic to run in 
       *
       */
	private function generate_return_unix_time(){		
		$max_execution_time = ini_get('max_execution_time');
		
		if($max_execution_time == 0 || NULL){
			$max_execution_time = 30;
		}
		$this->return_time = time() + $max_execution_time;
		$this->return_time = $this->return_time - $this->safe_buffer_time;
	}
	
	/**
       * 
       * Normalizes weighting values for all heuristics
       *
       */
	private function normalize_weightings(){
		$weightings = $this->weighting_array;
		$normalized_weightings = array();
		
		$min = min($weightings);
		$max = max($weightings);
		$lower = 0.01;
		$upper = 1;
		
		foreach ($weightings as $i => $v) {
			$normalized_weightings[$i] = ((($upper - $lower) * ($v - $min)) / ($max - $min)) + $lower;
		}
		
		$this->weighting_array = $normalized_weightings;	
	}
	
	/**
       * 
       * Generates the greedy and hyperheuristic solutions, individual testing of heuristics is performed here
       * @param array $matrix_data an array of a parsed problem set
	   *
       */
	protected function generate($matrix_data){		
		$greedy_solution = $this->generate_greedy($matrix_data); 
		$hyper_solution = $this->hyper_heuristic_framework($greedy_solution,$matrix_data); 
		$this->front_end($hyper_solution,$greedy_solution);
		
		/*** START OF TESTING ***/
		/*** Heuristic 0 (swap first and last) COMPLETE ***/
		//$h0 = new Heuristic0;
		//$h0->generate_heuristic_solution($greedy_solution[0],$matrix_data);
		//exit;
		/*** End of Heuristic 0 ***/
		
		/*** Heuristic 1 (local 2 opt) COMPLETE ***/
		//$h1 = new Heuristic1;
		//$h1->generate_heuristic_solution($greedy_solution[0],$matrix_data);
		//exit;
		/*** End of Heuristic 1 ***/
		
		/*** Heuristic 2 (wide 2 opt) COMPLETE ***/
		//$h2 = new Heuristic2;
		//$h2->generate_heuristic_solution($greedy_solution[0],$matrix_data);
		//exit;
		/*** End of Heuristic 2 ***/
		
		/*** Heuristic 3 (reverse proceeding cities from random point forward) COMPLETE ***/
		//$h3 = new Heuristic3;
		//$h3->generate_heuristic_solution($greedy_solution[0],$matrix_data);
		//exit;
		/*** End of Heuristic 3 ***/
		
		/*** Heuristic 4 (flip between 2 random points) COMPLETE ***/
		//$h4 = new Heuristic4;
		//$h4->generate_heuristic_solution($greedy_solution[0],$matrix_data);
		//exit;
		/*** End of Heuristic 4 ***/
		
		/*** Heuristic 5 (flip all pre-ceeding cities) COMPLETE ***/
		//$h5 = new Heuristic5;
		//$h5->generate_heuristic_solution($greedy_solution[0],$matrix_data);
		//exit;
		/*** End of Heuristic 5 ***/
		
		/*** Heuristic 6 (full 2-opt run) COMPLETE ***/
		//$h6 = new Heuristic6;
		//$h6->generate_heuristic_solution($greedy_solution[0],$matrix_data);
		//exit;
		/*** End of Heuristic 6 ***/
		
		/*** Heuristic 7 (3 opt swap on biggest city to city distance) INCOMPLETE ***/
		//$h7 = new Heuristic7;
		//$h7->generate_heuristic_solution($greedy_solution[0],$matrix_data);
		//exit;
		/*** End of Heuristic 7 ***/
		/*** END OF TESTING ***/
	 }
	
	/**
       * 
       * The incremental Randomized Weighting hyperheuristic imporvement algorithm. 
	   * @param array $greedy_solution a complete solution produced by the nearest neighbour algorithm 
       * @param array $matrix_data an array of a parsed problem set
	   *
       */	
	private function hyper_heuristic_framework($greedy_solution,$matrix_data){
		$heuristic_class_array = $this->instantiate_heuristics(); 
		$this->generate_weighting_array(); 
		$this->in_run_solution_array = $greedy_solution[0]; 
		$this->in_run_cost = $greedy_solution[1];
		$impovement_count = 0;
		$impovement = FALSE;
		while(time() < $this->return_time){
			if($impovement_count == 10000 && $impovement === FALSE){
				break;
			}
			$maximum_weighting_sum = max($this->weighting_array);
			$rand = mt_rand() / mt_getrandmax();
			$this->array_keys_shuffle();
			foreach($this->weighting_array as $weighint_key => $weighting_value){
				if($rand < $weighting_value ){
					$heuristic_solution = $heuristic_class_array[substr($weighint_key, -1)]->generate_heuristic_solution($this->in_run_solution_array,$this->matrix_data);
					$sum_cost = $this->sum_cost($heuristic_solution);
									
					if($sum_cost < $this->in_run_cost){
						$this->error_check($heuristic_solution);
						$this->weighting_array[$weighint_key] = $weighting_value + 2;
						$this->in_run_solution_array = $heuristic_solution;
						$this->in_run_cost = $sum_cost;
						$impovement = TRUE;
						@$this->normalize_weightings();
					} 
				} 
			}
			$impovement_count++;
		}
			
		$this->write_weighting_to_storage();
		
		return array($this->in_run_solution_array,array($this->in_run_cost));
	}
	/**
       * 
       * Writes writing data to offline storage ("weighting.txt") 
	   *
       */	
	private function write_weighting_to_storage(){
		$json_derulo = json_encode($this->weighting_array);
		$file = fopen("weighting.txt", "w"); //handles file creation and writing
		fwrite($file, $json_derulo);
		fclose($file);
	}
	 
	/**
       * 
       * Shuffles the weighting values array whilst preserving array keys
	   *
       */	
	private function array_keys_shuffle(){
		$keys = array_keys($this->weighting_array); 
		shuffle($keys); 
		$randomized_arr = array(); 
		foreach ($keys as $key) { 
			$randomized_arr[$key] = $this->weighting_array[$key]; 
		}		
		$this->weighting_array = $randomized_arr;
	}
	
	/**
       * 
       * Instantiates individual heuristics and casts the objects to array
	   * @return array
       */	
	private function instantiate_heuristics(){
		$heuristic_class_array = array();
		for($t=0; $t<=$this->number_of_heuristics; $t++ ){ 
			$heuristic = 'Heuristic' . $t;
			$$heuristic = new $heuristic;
			array_push($heuristic_class_array,$$heuristic);
		} 
		return $heuristic_class_array;
	 }
	 
	/**
       * 
       * Generates the weighting array, the offline storage file (weighting.txt) is created if it does not exist
	   *
       */	
	private function generate_weighting_array(){
		
		if(file_exists("weighting.txt") && is_writable("weighting.txt") && !empty(trim(file_get_contents("weighting.txt")))){
			$json_derulo = json_decode(file_get_contents('./weighting.txt', FILE_USE_INCLUDE_PATH),TRUE);
			$this->weighting_array = $json_derulo;
		}
		else{
			$tmp_heuritic_name_ara = array();
			for($i=0; $i<=$this->number_of_heuristics; $i++){
				$tmp_heuritic_name_ara[] = 'Heutistic' . $i;
			}
			$this->weighting_array = array_fill_keys($tmp_heuritic_name_ara, 3);
			@$this->normalize_weightings();//normalize the weighting array
		}
	 }
	
	/**
      * 
      * Generates the weighting array, the offline storage file (weighting.txt) is created if it does not exist
	  * @param array $heuristic_solution array containing solution
	  * @return array
	  *
      */	
	private function sum_cost($heuristic_solution){
	$cost = 0;
	foreach($heuristic_solution as $key => $descript_arr){
		foreach($descript_arr as $descript_key => $descript_val){
			if($descript_key == 'cost' || $descript_key == 'Cost'){
				$cost = $cost + $descript_val;
			}
		}
	}
	return $cost;
	}
	
	/**
      * 
      * Provides screen output. Terminates execution
	  *
      */
	private function error_check($heuristic_solution){
		$city = array();

		for($i=0;$i<count($heuristic_solution);$i++){
			$city[] = $heuristic_solution[$i]["City"];
		}
		if(count(array_unique($city))<count($city)){
			echo "Duplicate city found. Unidentified (occasional) error in Nearest Neighbour heuristic with small number of problem sets. Use a differnet problem instance";
			exit;
		}
	}
	
	/**
      * 
      * Provides screen output
	  *
      */	
	private function front_end($hyper,$greedy){
	 	echo "<pre>";
		ksort($this->weighting_array);
		var_dump($this->weighting_array);
		echo "</pre>";
		echo "Hyper Cost: ". $hyper[1][0];
		echo "<br>";
		echo "Greedy Cost: " . $greedy[1];
		echo "<br>";
		echo "<br>";
		echo "<strong>Hyper</strong>";
		echo "<pre>";
		var_dump($hyper[0]);
		/* echo "<br>";
		echo "<strong>Greedy</strong>";
		echo "<pre>";
		var_dump($greedy);  */
		
		//test all cities are there
		/* for($i=0;$i<count($hyper[0]);$i++){
			echo $hyper[0][$i]["City"];
			echo "<br>";
		} */
	}
 }