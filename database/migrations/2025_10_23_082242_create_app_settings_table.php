<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();

            $table->string('site_name')->nullable();
            $table->string('meta_name')->nullable();
            $table->string('meta_tag')->nullable();
            $table->text('description')->nullable();

            // JSON fields
            $table->json('social_links')->nullable(); // { "facebook": "...", "twitter": "..." }
            $table->json('home_banner')->nullable(); // [{ "id":1,"image":"path","title":"...","subtitle":"..." }, ...]

            // Media
            $table->string('logo')->nullable();
            $table->string('splash_screen_image')->nullable();

            // Splash screen text
            $table->string('splash_screen_title')->nullable();
            $table->text('splash_screen_description')->nullable();

            $table->string('copyright_message')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};
