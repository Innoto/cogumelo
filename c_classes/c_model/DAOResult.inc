<?php


abstract class DAOResult {
	abstract function fetch();
	abstract function fetchAll();
	abstract function count();
	abstract function free();

	abstract function VOGenerator();
}