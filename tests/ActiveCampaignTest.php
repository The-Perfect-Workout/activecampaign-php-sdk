<?php

use PHPUnit\Framework\TestCase;
use PerfectWorkout\ActiveCampaign\ActiveCampaign;

class ActiveCampaignTest extends TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function test_making_basic_requests()
    {
        $activeCampaign = new ActiveCampaign('', '123', $http = Mockery::mock('GuzzleHttp\Client'));

        $http->shouldReceive('request')->once()->with('GET', 'organizations', [])->andReturn(
            $response = Mockery::mock('GuzzleHttp\Psr7\Response')
        );

        $response->shouldReceive('getStatusCode')->once()->andReturn(200);
        $response->shouldReceive('getBody')->once()->andReturn('{"data": [{"key": "value"}]}');

        $organizations = $activeCampaign->organizations();

        $this->assertContainsOnlyInstancesOf(\PerfectWorkout\ActiveCampaign\Resources\Organization::class, $organizations);
    }

    public function test_handling_validation_errors()
    {
        $activeCampaign = new ActiveCampaign('', '123', $http = Mockery::mock('GuzzleHttp\Client'));

        $http->shouldReceive('request')->once()->with('GET', 'organizations', [])->andReturn(
            $response = Mockery::mock('GuzzleHttp\Psr7\Response')
        );

        $response->shouldReceive('getStatusCode')->andReturn(422);
        $response->shouldReceive('getBody')->once()->andReturn('{"errors": {"name": ["The name is required."]}}');

        try {
            $activeCampaign->organizations();
        } catch (\PerfectWorkout\ActiveCampaign\Exceptions\ValidationException $e) {
        }

        $this->assertEquals(['name' => ['The name is required.']], $e->errors());
    }

    public function test_handling_404_errors()
    {
        $this->expectException(\PerfectWorkout\ActiveCampaign\Exceptions\NotFoundException::class);

        $activeCampaign = new ActiveCampaign('', '123', $http = Mockery::mock('GuzzleHttp\Client'));

        $http->shouldReceive('request')->once()->with('GET', 'organizations', [])->andReturn(
            $response = Mockery::mock('GuzzleHttp\Psr7\Response')
        );

        $response->shouldReceive('getStatusCode')->andReturn(404);

        $activeCampaign->organizations();
    }

    public function test_handling_failed_action_errors()
    {
        $activeCampaign = new ActiveCampaign('', '123', $http = Mockery::mock('GuzzleHttp\Client'));

        $http->shouldReceive('request')->once()->with('GET', 'organizations', [])->andReturn(
            $response = Mockery::mock('GuzzleHttp\Psr7\Response')
        );

        $response->shouldReceive('getStatusCode')->andReturn(400);
        $response->shouldReceive('getBody')->once()->andReturn('Error!');

        try {
            $activeCampaign->organizations();
        } catch (\PerfectWorkout\ActiveCampaign\Exceptions\FailedActionException $e) {
        }

        $this->assertEquals('Error!', $e->getMessage());
    }
}
