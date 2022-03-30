<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Denomination;

class DenominationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Denomination::create([
            'type' => 'BILLETE',
            'value' => 200,
            'image' => 'https://dummyimage.com/200x150/5c5756/fff'
        ]);
        Denomination::create([
            'type' => 'BILLETE',
            'value' => 100,
            'image' => 'https://dummyimage.com/200x150/5c5756/fff'
        ]);
        Denomination::create([
            'type' => 'BILLETE',
            'value' => 50,
            'image' => 'https://dummyimage.com/200x150/5c5756/fff'
        ]);
        Denomination::create([
            'type' => 'BILLETE',
            'value' => 20,
            'image' => 'https://dummyimage.com/200x150/5c5756/fff'
        ]);
        Denomination::create([
            'type' => 'BILLETE',
            'value' => 10,
            'image' => 'https://dummyimage.com/200x150/5c5756/fff'
        ]);
        Denomination::create([
            'type' => 'MONEDA',
            'value' => 5,
            'image' => 'https://dummyimage.com/200x150/5c5756/fff'
        ]);
        Denomination::create([
            'type' => 'MONEDA',
            'value' => 2,
            'image' => 'https://dummyimage.com/200x150/5c5756/fff'
        ]);
        Denomination::create([
            'type' => 'MONEDA',
            'value' => 1,
            'image' => 'https://dummyimage.com/200x150/5c5756/fff'
        ]);
        Denomination::create([
            'type' => 'MONEDA',
            'value' => 0.5,
            'image' => 'https://dummyimage.com/200x150/5c5756/fff'
        ]);
        Denomination::create([
            'type' => 'MONEDA',
            'value' => 0.2,
            'image' => 'https://dummyimage.com/200x150/5c5756/fff'
        ]);
        Denomination::create([
            'type' => 'MONEDA',
            'value' => 0.1,
            'image' => 'https://dummyimage.com/200x150/5c5756/fff'
        ]);
        Denomination::create([
            'type' => 'OTRO',
            'value' => 0,
            'image' => 'https://dummyimage.com/200x150/5c5756/fff'
        ]);
    }
}
