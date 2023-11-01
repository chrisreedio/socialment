<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up()
	{
		if (Schema::hasTable('users')) {
			Schema::table('users', function (Blueprint $table) {
				$table->string('password')->nullable()->change();
			});
		}
	}
};
