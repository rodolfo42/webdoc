<?php
namespace Model;

use Model\Model;

class Documento extends Model {
	
	public $table = "docs";
	
	public $createdTimestampField = 'created';
	public $timestampField = null;
}