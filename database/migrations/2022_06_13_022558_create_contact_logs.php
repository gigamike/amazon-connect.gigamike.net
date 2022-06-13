<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_logs', function (Blueprint $table) {
            $table->id();
            $table->string('contact_id')->unique();
            $table->string('contact_initiation_method')->nullable();
            $table->string('customer_endpoint_address')->nullable();
            $table->string('system_endpoint_address')->nullable();
            $table->foreignId('customer_id')->nullable();
            $table->timestamps();
            $table->index('contact_id');
            $table->index('customer_endpoint_address');
            $table->index('system_endpoint_address');
            $table->index('customer_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contact_logs');
    }
}
