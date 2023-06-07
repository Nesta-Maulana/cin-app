<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('settings')->delete();
        
        \DB::table('settings')->insert(array (
            0 => 
            array (
                'address' => 'Sukabumi, Jawa Barat',
                'app_name' => 'Chore',
                'app_version' => NULL,
                'created_at' => '2023-05-11 14:46:48',
                'id' => 1,
                'logo' => '',
                'name' => 'Shofyan Ariantho',
                'updated_at' => '2023-06-08 06:21:44',
            ),
        ));
        
        
    }
}