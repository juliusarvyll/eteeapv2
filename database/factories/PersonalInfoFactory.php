<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PersonalInfo;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PersonalInfo>
 */
class PersonalInfoFactory extends Factory
{
    protected $model = PersonalInfo::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'applicant_id' => $this->faker->unique()->randomNumber(),
            'firstName' => $this->faker->firstName,
            'middleName' => $this->faker->lastName,
            'lastName' => $this->faker->lastName,
            'suffix' => $this->faker->optional()->word,
            'birthDate' => $this->faker->date(),
            'placeOfBirth' => $this->faker->city,
            'civilStatus' => $this->faker->randomElement(['single', 'married', 'divorced', 'widowed']),
            'email' => $this->faker->unique()->safeEmail,
            'phoneNumber' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
            'zipCode' => $this->faker->postcode,
            'document' => $this->faker->optional()->word,
            'sex' => $this->faker->randomElement(['male', 'female']),
            'languages' => $this->faker->words(3, true),
            'status' => $this->faker->randomElement(['draft', 'pending', 'approved', 'rejected']),
        ];
    }
}
