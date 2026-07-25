<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up():void{if(!Schema::hasTable('holidays'))Schema::create('holidays',function(Blueprint $t){$t->id();$t->date('holiday_date')->unique();$t->string('name');$t->boolean('is_active')->default(true);$t->text('notes')->nullable();$t->timestamps();});} public function down():void{Schema::dropIfExists('holidays');}};
