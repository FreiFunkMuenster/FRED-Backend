<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterNetworksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('networks', function(Blueprint $table){
            $table->decimal("calculated_longitude", 10, 6)->default(0);
            $table->decimal("calculated_latitude", 10, 6)->default(0);
            $table->integer('datapoints')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('networks', function(Blueprint $table){
            $table->dropColumn("calculated_longitude");
            $table->dropColumn("calculated_latitude");
            $table->dropColumn('datapoints');
        });

    }
}
