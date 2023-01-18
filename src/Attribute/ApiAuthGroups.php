<?php
namespace App\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
class ApiAuthGroups
{
     public function __construct(public $groups)
     {
        # code...
     }
}
