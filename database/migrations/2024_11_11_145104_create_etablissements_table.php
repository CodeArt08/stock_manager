<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('etablissements', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('idZap');
            $table->foreign('idZap')->references('id')->on('zaps')->onUpdate('cascade'); 
            $table->string('code', 100);
            $table->string('nomEtab', 100);
            $table->string('telephone', 100);
            $table->timestamps();

            Schema::enableForeignKeyConstraints();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('etablissements', function (Blueprint $table){
            $table->dropForeign('etablissements_idZap_foreign');
        });
    }
};
