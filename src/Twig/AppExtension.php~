<?php
// src/Twig/AppExtension.php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigSimpleFilter;

class AppExtension extends AbstractExtension
{
	public function objectFilter($stdClassObject) {
	    // Just typecast it to an array
	    $response = (array)$stdClassObject;

   	 return $response;
	}
	
	public function getFilters()
	{
    	return array(
        	new TwigFilter('cast_to_array', array($this, 'objectFilter')),
   	 );
	}
}