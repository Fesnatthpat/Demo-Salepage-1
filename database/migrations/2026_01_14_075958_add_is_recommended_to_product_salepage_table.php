<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_salepage', function (Blueprint $table) {
            $table->boolean('is_recommended')->default(false)->after('pd_sp_active')->comment('สินค้าแนะนำ');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_salepage', function (Blueprint $table) {
            $table->dropColumn('is_recommended');
        });
    }
};