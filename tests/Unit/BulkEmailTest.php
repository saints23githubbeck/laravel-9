<?php

namespace Tests\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use App\Jobs\SendEmailJob;

use Tests\TestCase;


class BulkEmailTest extends TestCase
{
    use RefreshDatabase; // Optional: Use this trait if you want to refresh the database before each test.

    public function testSendEmailJobDispatchedCorrectly()
    {
        // Mock the Queue facade
        Queue::fake();

        $emailData = [
            'toEmailAddress' => ['arthurshadrack45@gmail.com'],
            'messageSubject' => 'Test Subject',
            'messageBody' => 'Test Body',
        ];

        // Send a POST request to your endpoint with valid data
        $response = $this->json('POST', '/api/send', $emailData);

        Queue::assertPushed(SendEmailJob::class, function ($job) use ($emailData) {
            // Convert the 'toEmailAddresses' property of the job to an array for comparison
            $jobTo = is_array($job->toEmailAddresses) ? $job->toEmailAddresses : [$job->toEmailAddresses];

            return in_array($emailData['toEmailAddress'][0], $jobTo) &&
                $job->messageSubject === $emailData['messageSubject'] &&
                $job->messageBody === $emailData['messageBody'];
        });

        // Assert that the response is as expected
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Emails sent successfully']);
    }

    public function testSendEmailJobNotDispatchedOnValidationError()
    {
        Queue::fake(); // Mock the Queue facade

        // Send a POST request to your endpoint with invalid data
        $response = $this->postJson('/api/send', [
            'toEmailAddress' => ['arthurshadrack45@gmail.com'], // Invalid email address
            'messageSubject' => 'Test Subject',
            'messageBody' => 'Test Body',
        ]);

        // Assert that the job is not dispatched
        Queue::assertNotPushed(SendEmailJob::class);

        // Assert that the response indicates a validation error
        $response->assertStatus(422); // Use 422 for validation errors
        $response->assertJsonValidationErrors(['toEmailAddress']);
    }


}


