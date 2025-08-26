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
        Schema::create('return_refund_requests', function (Blueprint $table) {
           $table->id();

            // Link to order & product
            $table->foreignId('order_id')->nullable()->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->cascadeOnDelete();
            
            // Link to user
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('seller_id')->nullable()->constrained('users')->cascadeOnDelete();

            // Type of request
            $table->enum('request_type', ['return', 'refund', 'replacement'])->nullable();

            // Reason for request
            $table->string('reason')->nullable();
            $table->text('description')->nullable();

            // Status tracking
            $table->enum('status', [
                'pending', 
                'approved', 
                'rejected', 
                'processing', 
                'completed'
            ])->default('pending')->nullable();

            // Refund details (if applicable)
            $table->decimal('refund_amount', 10, 2)->nullable();
            $table->enum('refund_method', ['original_payment', 'store_credit', 'manual'])->nullable();
            $table->timestamp('refund_processed_at')->nullable();

            // Return shipping details
            $table->string('return_tracking_number')->nullable();
            $table->string('return_shipping_carrier')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_refund_requests');
    }
};
