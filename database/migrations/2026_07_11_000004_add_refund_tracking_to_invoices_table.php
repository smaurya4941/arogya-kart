<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Track the asynchronous outcome of Razorpay refunds. A refund is created with
     * speed=normal and settles minutes-to-days later, so the invoice can't be
     * declared "refunded" the instant we call the API. We store the gateway refund
     * id and settlement time, and reconcile the final state from the
     * refund.processed / refund.failed webhooks (see BillingService::reconcileRefund).
     */
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('refund_id')->nullable()->after('transaction_id');
            $table->timestamp('refunded_at')->nullable()->after('paid_at');
            $table->index('refund_id');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex(['refund_id']);
            $table->dropColumn(['refund_id', 'refunded_at']);
        });
    }
};
