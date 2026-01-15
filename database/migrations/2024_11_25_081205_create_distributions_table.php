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
        Schema::create('distributions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('idStock');
            $table->foreign('idStock')->references('id')->on('stocks')->onUpdate('cascade');
            $table->bigInteger('idZap');
            $table->foreign('idZap')->references('id')->on('zaps')->onUpdate('cascade'); 
            $table->bigInteger('idEtab');
            $table->foreign('idEtab')->references('id')->on('etablissements')->onUpdate('cascade');  
            $table->string('nbrCarton', 100);
            $table->string('nbrPiece', 100);
            $table->date('dateDist');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('distributions', function (Blueprint $table){
            $table->dropForeign('distributions_idStock_foreign');
            $table->dropForeign('distributions_idZap_foreign');
            $table->dropForeign('distributions_idEtab_foreign');
        });
    }
};
