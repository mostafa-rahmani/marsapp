<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table){
            $table->increments('id');
            $table->string('landing_title')->nullable();
            $table->string('landing_description')->nullable();
            $table->string('app_download_url')->nullable();
            $table->boolean('admin_register_on')->nullable()->default(1);
            $table->timestamps();
        });
        $data = [
            'landing_title' => 'اپلیکیشن پرتقال برای تمام طراحان',
            'landing_description' => 'طراح هستید یا نقاش و شاید هنرمند، اپ پرتقال رو نصب کنید و ایده ها و طرح هاتون رو با هم به اشتراک بزارید و بازخورد دوستاتون رو هم داشته باشید.',
            'app_download_url' => 'cafebazaar.ir',
            'admin_register_on' => 1
        ];
        \App\Setting::create($data);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
