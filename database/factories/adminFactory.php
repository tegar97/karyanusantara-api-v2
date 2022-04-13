<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class adminFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'email' => 'karyanuntara@karyanuntara.co.id',
            'password' => "karyanuntara123",
            'name' => 'Karyanusantara'
        ];
    }
}
