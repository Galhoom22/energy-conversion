<?php

use App\Models\Organization;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

function validRegisterMeterPayload(Organization $organization, array $overrides = []): array
{
    return array_replace_recursive([
        'organizationId' => $organization->id,
        'requesterId' => 'user_123',
        'timestamp' => now()->toISOString(),
        'data' => [
            'serial_number' => 'MTR-00001',
            'type' => 'electric',
            'location' => 'Cairo',
        ],
    ], $overrides);
}

function actingAsWriterFor(Organization $organization): User
{
    $user = User::factory()->create([
        'organization_id' => $organization->id,
    ]);

    Sanctum::actingAs($user, ['energy_conversion.write']);

    return $user;
}

test('it registers a meter with valid data', function () {
    $organization = Organization::factory()->create();
    actingAsWriterFor($organization);

    $response = $this->postJson(
        route('api.v1.energy_conversion.register-meter'),
        validRegisterMeterPayload($organization),
    );

    $response->assertSuccessful()
        ->assertJsonPath('status', 'SUCCESS')
        ->assertJsonPath('details.serialNumber', 'MTR-00001');

    $this->assertDatabaseHas('meters', [
        'serial_number' => 'MTR-00001',
        'organization_id' => $organization->id,
    ]);
});

test('it rejects a request without the write scope', function () {
    $organization = Organization::factory()->create();
    Sanctum::actingAs(User::factory()->create([
        'organization_id' => $organization->id,
    ]), ['some-other-scope']);

    $response = $this->postJson(
        route('api.v1.energy_conversion.register-meter'),
        validRegisterMeterPayload($organization, [
            'data' => ['serial_number' => 'MTR-00002'],
        ]),
    );

    $response->assertForbidden();

    $this->assertDatabaseEmpty('meters');
});

test('it rejects registration for another organization', function () {
    $userOrganization = Organization::factory()->create();
    $otherOrganization = Organization::factory()->create();
    actingAsWriterFor($userOrganization);

    $response = $this->postJson(
        route('api.v1.energy_conversion.register-meter'),
        validRegisterMeterPayload($otherOrganization, [
            'data' => ['serial_number' => 'MTR-00003'],
        ]),
    );

    $response->assertForbidden();

    $this->assertDatabaseEmpty('meters');
});

test('it returns validation errors for missing fields', function () {
    $organization = Organization::factory()->create();
    actingAsWriterFor($organization);

    $response = $this->postJson(route('api.v1.energy_conversion.register-meter'), [
        'data' => [],
    ]);

    $response->assertStatus(400)
        ->assertJsonValidationErrors([
            'organizationId',
            'requesterId',
            'data.serial_number',
            'data.type',
        ]);

    $this->assertDatabaseEmpty('meters');
});

test('it returns validation errors for an unknown organization', function () {
    $organization = Organization::factory()->create();
    actingAsWriterFor($organization);

    $response = $this->postJson(
        route('api.v1.energy_conversion.register-meter'),
        validRegisterMeterPayload($organization, [
            'organizationId' => 999,
            'data' => ['serial_number' => 'MTR-00004'],
        ]),
    );

    $response->assertStatus(400)
        ->assertJsonValidationErrors(['organizationId']);

    $this->assertDatabaseEmpty('meters');
});

test('it returns the original result for a repeated idempotency key', function () {
    $organization = Organization::factory()->create();
    actingAsWriterFor($organization);

    $payload = validRegisterMeterPayload($organization, [
        'data' => ['serial_number' => 'MTR-00005'],
    ]);

    $headers = ['Idempotency-Key' => 'key-123'];

    $first = $this->postJson(route('api.v1.energy_conversion.register-meter'), $payload, $headers);
    $second = $this->postJson(route('api.v1.energy_conversion.register-meter'), $payload, $headers);

    $first->assertSuccessful();
    $second->assertSuccessful();

    expect($second->json())->toEqual($first->json());

    $this->assertDatabaseCount('meters', 1);
});

test('it writes an audit record when a meter is registered', function () {
    $organization = Organization::factory()->create();
    $user = actingAsWriterFor($organization);

    $this->postJson(
        route('api.v1.energy_conversion.register-meter'),
        validRegisterMeterPayload($organization, [
            'data' => ['serial_number' => 'MTR-00006'],
        ]),
    )->assertSuccessful();

    $this->assertDatabaseHas('audit_records', [
        'organization_id' => $organization->id,
        'action' => 'meter.registered',
        'actor_id' => $user->id,
    ]);
});
