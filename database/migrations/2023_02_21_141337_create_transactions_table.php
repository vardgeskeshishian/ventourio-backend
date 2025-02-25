<?php

use App\Enums\TransactionStatus;
use App\Models\Currency;
use App\Models\PaymentWay;
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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(PaymentWay::class)
                ->references('id')->on('payment_ways')
                ->restrictOnDelete();

            $table->foreignIdFor(User::class)
                ->references('id')->on('users')
                ->cascadeOnDelete();

            $table->morphs('instance');
            $table->unsignedTinyInteger('status')->default(TransactionStatus::WAITING->value);
            $table->timestamp('paid_at')->nullable();
            $table->unsignedFloat('amount');

            $table->foreignIdFor(Currency::class)
                ->references('id')->on('currencies')
                ->restrictOnDelete();

            $table->json('extra')->nullable();

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
        Schema::dropIfExists('transactions');
    }
};
