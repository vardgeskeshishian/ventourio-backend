<?php

use App\Models\BaseCertificate;
use App\Models\Currency;
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
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(BaseCertificate::class)
                ->nullable()
                ->references('id')->on('base_certificates')
                ->nullOnDelete();

            $table->foreignIdFor(User::class, 'bought_by_user_id')
                ->references('id')->on('users')
                ->cascadeOnDelete();

            $table->foreignIdFor(Currency::class)
                ->references('id')->on('currencies')
                ->restrictOnDelete();

            $table->string('code')->nullable()->unique();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('used_at')->nullable();

            $table->foreignIdFor(User::class, 'used_by_user_id')
                ->nullable()
                ->references('id')->on('users')
                ->nullOnDelete();

            $table->boolean('is_seen')->default(0);

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
        Schema::dropIfExists('certificates');
    }
};
