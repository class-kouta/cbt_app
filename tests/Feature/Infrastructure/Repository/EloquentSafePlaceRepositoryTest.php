<?php

namespace Tests\Feature\Infrastructure\Repository;

use App\Domain\Entity\SafePlace;
use App\Infrastructure\Database\Models\SafePlace as SafePlaceModel;
use App\Infrastructure\Repository\EloquentSafePlaceRepository;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class EloquentSafePlaceRepositoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('safe_places');
        Schema::create('safe_places', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_id')->unique();
            $table->text('safe_image')->nullable();
            $table->text('safe_something')->nullable();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('safe_places');

        parent::tearDown();
    }

    public function test_save_for_member_updates_existing_single_record_when_entity_has_no_id(): void
    {
        $memberId = 123;
        $repository = new EloquentSafePlaceRepository;

        $created = $repository->saveForMember(
            SafePlace::createNew('first image', 'first something'),
            $memberId
        );

        $updated = $repository->saveForMember(
            SafePlace::createNew('updated image', 'updated something'),
            $memberId
        );

        $this->assertSame($created->getId(), $updated->getId());
        $this->assertSame(1, SafePlaceModel::where('member_id', $memberId)->count());
        $this->assertDatabaseHas('safe_places', [
            'id' => $created->getId(),
            'member_id' => $memberId,
            'safe_image' => 'updated image',
            'safe_something' => 'updated something',
        ]);
    }
}
