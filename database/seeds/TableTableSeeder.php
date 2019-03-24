<?php

use Illuminate\Database\Seeder;

class TableTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('target')->insert([
            'name' => 'Переписка',
        ]);

        DB::table('target')->insert([
            'name' => 'Дружба',
        ]);

        DB::table('target')->insert([
            'name' => 'Романтические отношения',
        ]);

        DB::table('target')->insert([
            'name' => 'Серьёзные отношения',
        ]);

        DB::table('target')->insert([
            'name' => 'Разовые встречи',
        ]);
    }
}
