<?php
define('__PARSER__', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'dissertation\Class_PARSER.php'); 
require_once __PARSER__;

/**
   * Parser Controller
   * 
   * The script which is run in order to parse
   *
   * URL: http://DOMAIN/dissertation/parse.php
   *
   * @package    Randomized Weighting
   * @author     Liam Fell <1218996@chester.ac.uk>
   */

$parse = new Parser();
$parse->do_parse();

