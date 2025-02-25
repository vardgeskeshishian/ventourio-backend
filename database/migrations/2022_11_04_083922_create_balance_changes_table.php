<?php

use App\Models\BalanceChange;
use App\Models\User;
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
        Schema::create('balance_changes', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('model');

            $table->foreignIdFor(User::class)
                ->references('id')->on('users')
                ->cascadeOnDelete();

            $table->unsignedFloat('amount')->default(0);
            $table->boolean('type');
            $table->string('remark')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('balance_changes');
    }
};
