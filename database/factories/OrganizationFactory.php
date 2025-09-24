<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Laravel\Cashier\Subscription;
use Laravel\Cashier\SubscriptionItem;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organization>
 */
class OrganizationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'user_id' => User::factory(),
            'ssh_public_key' => 'FAKE_PUBLIC_KEY',
            'ssh_private_key' => 'FAKE_PRIVATE_KEY',
        ];
    }

    /**
     * Indicate that the organization has an active subscription.
     */
    public function withSubscription(array $overrides = []): static
    {
        return $this->afterCreating(function ($organization) use ($overrides) {
            $subscription = Subscription::factory()
                ->for($organization, 'owner')
                ->state($overrides)
                ->create();

            SubscriptionItem::factory()
                ->for($subscription)
                ->state([
                    'stripe_price' => config('services.stripe.price_id'),
                    'quantity' => 1,
                ])
                ->create();
        });
    }
}
