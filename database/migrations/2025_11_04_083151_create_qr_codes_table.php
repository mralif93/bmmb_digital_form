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
        Schema::create('qr_codes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // QR code name/title
            $table->string('type')->default('url'); // branch, url, text, phone, email, sms, wifi, vcard
            $table->text('content'); // What the QR code encodes (URL, text, etc.)
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('set null'); // Optional branch relation
            $table->string('qr_code_image')->nullable(); // QR code image filename
            $table->string('status')->default('active'); // active, inactive
            $table->integer('size')->default(300); // QR code size
            $table->string('format')->default('png'); // png, svg, jpg
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('last_regenerated_at')->nullable(); // Last regeneration timestamp
            $table->timestamp('expires_at')->nullable(); // Expiration timestamp (when QR code expires)
            $table->string('validation_token')->nullable(); // Token for URL validation (invalidates old QR codes)
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('name');
            $table->index('type');
            $table->index('status');
            $table->index('branch_id');
            $table->index('expires_at');
            $table->index('validation_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qr_codes');
    }
};

