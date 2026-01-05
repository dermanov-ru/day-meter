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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('biometric_enabled')->default(false)->after('password');
            $table->string('webauthn_credential_id')->nullable()->after('biometric_enabled');
            $table->text('webauthn_public_key')->nullable()->after('webauthn_credential_id');
            $table->bigInteger('webauthn_counter')->default(0)->after('webauthn_public_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('biometric_enabled');
            $table->dropColumn('webauthn_credential_id');
            $table->dropColumn('webauthn_public_key');
            $table->dropColumn('webauthn_counter');
        });
    }
};
