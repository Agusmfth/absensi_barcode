<?php
namespace Database\Factories; use Illuminate\Database\Eloquent\Factories\Factory; use Illuminate\Support\Str;
class UserFactory extends Factory{public function definition():array{return['name'=>fake()->name(),'email'=>fake()->unique()->safeEmail(),'username'=>fake()->unique()->userName(),'password'=>'password','role'=>'admin','is_active'=>true,'remember_token'=>Str::random(10)];}}
