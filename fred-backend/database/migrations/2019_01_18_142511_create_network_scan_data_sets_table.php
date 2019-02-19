<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNetworkScanDataSetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('network_scan_data_sets', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->integer('scan_id')->unsigned();
            $table->foreign('scan_id')->references('id')->on('scans')->onDelete('cascade');

            $table->integer('network_id')->unsigned();
            $table->foreign('network_id')->references('id')->on('networks')->onDelete('cascade');

            $table->string('ssid');
            $table->string('capabilities')->nullable();
            $table->string('frequency')->nullable();
            $table->string('level')->nullable();
            $table->string('distance')->nullable();
            $table->string('distance_sd')->nullable();
            $table->string('passpoint')->nullable();
            $table->string('channel_bandwidth')->nullable();
            $table->string('center_frequency_0')->nullable();
            $table->string('center_frequency_1')->nullable();
            $table->string('mc_responder')->nullable();
            $table->string('channel_mode')->nullable();
            $table->string('bss_load_element')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('network_scan_data_sets');
    }
}
