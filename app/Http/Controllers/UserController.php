<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
     public function getUser(){
         return "this returnes the code step by step";
     }

     public function aboutUser2(){
         return "this returnes the code step by step this is me";
     }
     public function getUserName($name){
          return "this returnes the code step by step this is {$name}";
     }
    //  now if wewant to opent any new page with controler then we did it 
     public function view($name){
        return view('view',['name'=>$name]);
     }
}
