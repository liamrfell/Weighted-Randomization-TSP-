<?php

 /**
   * Parser
   * 
   * Handles parsing of raw test instaces (Euclidean 2D Edge Weighting). We input EUC_2D.txt and output to input_data.txt. 
   * 
   * @package    Randomized Weighting
   * @author     Liam Fell <1218996@chester.ac.uk>
   */
class Parser{
	
	private $raw_data_array = array();
	
	/**
       * 
       * Pulblic interface. Opens and reads file to be parsed (EUC_2D.txt in root)
       *
       */
	public function do_parse(){
		$contents = file_get_contents("EUC_2D.txt");
		$contents_array = preg_split('/\r\n|\r|\n/',$contents); // ***SHOULD handle any environment***
	
		$tmp_array = array();
		
		foreach($contents_array as $contents_line){
			$tmp_array[] = preg_split('/\s+/', $contents_line, -1, PREG_SPLIT_NO_EMPTY); //split on spaces
		}
		
		$number_of_cities = count($tmp_array);
		$this->raw_data_array = $tmp_array;
		$count = 0;
		$cost_matrix = array();
		for($i=0; $i<$number_of_cities; $i++){
			$tmp_array[$count][0];
			$tmp_array[$count][1];
			$tmp_array[$count][2];
			$cost_matrix[$tmp_array[$count][0]] = $this->generate_individual_city_matrix($count,$tmp_array[$count][0],$tmp_array[$count][1],$tmp_array[$count][2]);
			$count++;
		}	

		$this->write_to_input_array($cost_matrix);
	}
	
	/**
       * 
       * Carries out the 2d euclidean distance calculation
	   * @param integer $index_number index number, i.e line number
       * @param string $city city by reference
       * @param float $coordinate_x1 x coordinate
       * @param float $coordinate_y1 y coordinate
       * @return array 
       *
       */
	private function generate_individual_city_matrix($index_number,$city,$coordinate_x1,$coordinate_y1){
		$tmp_array = array();

		for($i=0; $i<count($this->raw_data_array); $i++){
			$coordinate_x2 = $this->raw_data_array[$i][1];
			$coordinate_y2 = $this->raw_data_array[$i][2];
			$tmp_array[$i] = round(sqrt(pow($coordinate_x1 - $coordinate_x2,2) + pow($coordinate_y1 - $coordinate_y2,2)),2,PHP_ROUND_HALF_DOWN);

		}
		return array_values($tmp_array);
	}
	
	/**
       * 
       * Writes parsed data to input_data.txt (root directory)
	   * @param array $parsed_data array containing all parsed data in JSON format
       *
       */
	private function write_to_input_array($parsed_data){
		$json_data = json_encode($parsed_data);
		$file = fopen("input_data.txt","w");
		fwrite($file,$json_data);
		fclose($file);
	}
}