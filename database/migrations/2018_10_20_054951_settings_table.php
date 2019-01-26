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
            $table->string('button_text')->nullable();
            $table->string('second_button_text')->nullable();
            $table->boolean('admin_register_on')->nullable()->default(1);
            $table->text('about_first_text')->nullable();
            $table->string('about_first_img')->nullable();
            $table->text('about_second_text')->nullable();
            $table->string('about_second_img')->nullable();

            $table->string('web_developer_img')->nullable();
            $table->string('web_developer_url')->nullable();

            $table->string('android_developer_img')->nullable();
            $table->string('android_developer_url')->nullable();

            $table->timestamps();
        });
        $data = [
            'landing_title' => 'اپلیکیشن پرتقال برای تمام طراحان',
            'landing_description' => 'طراح هستید یا نقاش و شاید هنرمند، اپ پرتقال رو نصب کنید و ایده ها
             و طرح هاتون رو با هم به اشتراک بزارید
             و بازخورد دوستاتون رو هم داشته باشید.',
            'app_download_url' => 'cafebazaar.ir',
            'admin_register_on' => 1,
            'button_text'   => 'دانلود مستقیم',
            'about_first_text' => 'این یک دیتای ساختگی است. تنها برای تست',
            'about_second_text' => 'این یک دیتای ساختگی است. تنها برای تست',
            'about_first_img' => 'https://getbootstrap.com/docs/4.1/assets/img/bootstrap-stack.png',
            'about_second_img' => 'https://getbootstrap.com/docs/4.1/assets/img/bootstrap-stack.png',

            'web_developer_img' => 'https://unsplash.com/photos/oTweoxMKdkA',
            'web_developer_url' => 'https://unsplash.com/photos/oTweoxMKdkA',

            'android_developer_img' => 'https://unsplash.com/photos/kBlqlwbuxHU',
            'android_developer_url' => 'https://unsplash.com/photos/kBlqlwbuxHU'

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
