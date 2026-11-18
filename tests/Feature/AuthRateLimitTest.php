<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class AuthRateLimitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        RateLimiter::clear('auth-forms:'.$this->app['request']->ip());
    }

    public function test_registration_submissions_are_rate_limited(): void
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post(route('register'), [])->assertStatus(302);
        }

        $this->post(route('register'), [])->assertStatus(429);
    }

    public function test_password_reset_requests_are_rate_limited(): void
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post(route('password.email'), [])->assertStatus(302);
        }

        $this->post(route('password.email'), [])->assertStatus(429);
    }

    public function test_password_reset_submissions_are_rate_limited(): void
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post(route('password.store'), [])->assertStatus(302);
        }

        $this->post(route('password.store'), [])->assertStatus(429);
    }
}
