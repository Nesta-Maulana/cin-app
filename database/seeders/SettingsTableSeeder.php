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

        \DB::table('settings')->insert(
            array(
                array(
                    'id' => 1,
                    'name' => 'App Setting & Copyright',
                    'created_at' => '2023-05-11 14:46:48',
                    'updated_at' => '2024-06-04 09:07:32',
                    'category' => 'system',
                    'data' => json_encode(
                        array(
                            'app_name' => env('APP_NAME').' Panel',
                            'app_version' => '1.0.0',
                            'name' => 'Prana Vorge Technologies',
                            'address' => "Jalan Lawang Gintung, Kota Bogor Selatan",
                            'logo' => 'setting/tDpqYxYcbV0M538mnapxhktBb1hEXAMvr1LnXG46.png',
                            'updated_by' => 1,
                            'updated_at' => '2024-06-04 09:07:31',
                        )
                    ),
                )
            )
        );


    }
}
