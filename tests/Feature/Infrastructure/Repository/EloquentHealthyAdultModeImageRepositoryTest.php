<?php

namespace Tests\Feature\Infrastructure\Repository;

use App\Domain\Entity\HealthyAdultModeImage;
use App\Infrastructure\Database\Models\HealthyAdultModeImage as HealthyAdultModeImageModel;
use App\Infrastructure\Repository\EloquentHealthyAdultModeImageRepository;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class EloquentHealthyAdultModeImageRepositoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('healthy_adult_mode_images');
        Schema::create('healthy_adult_mode_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_id')->unique();
            $table->text('content')->nullable();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('healthy_adult_mode_images');

        parent::tearDown();
    }

    public function test_save_for_member_updates_existing_single_record_when_entity_has_no_id(): void
    {
        $memberId = 123;
        $repository = new EloquentHealthyAdultModeImageRepository;

        $created = $repository->saveForMember(
            HealthyAdultModeImage::createNew('first content'),
            $memberId
        );

        $updated = $repository->saveForMember(
            HealthyAdultModeImage::createNew('updated content'),
            $memberId
        );

        $this->assertSame($created->getId(), $updated->getId());
        $this->assertSame(1, HealthyAdultModeImageModel::where('member_id', $memberId)->count());
        $this->assertDatabaseHas('healthy_adult_mode_images', [
            'id' => $created->getId(),
            'member_id' => $memberId,
            'content' => 'updated content',
        ]);
    }

    public function test_find_methods_are_scoped_to_member(): void
    {
        $memberId = 123;
        $otherMemberId = 456;
        $repository = new EloquentHealthyAdultModeImageRepository;

        $ownModel = HealthyAdultModeImageModel::create([
            'member_id' => $memberId,
            'content' => 'own content',
        ]);
        $otherModel = HealthyAdultModeImageModel::create([
            'member_id' => $otherMemberId,
            'content' => 'other content',
        ]);

        $this->assertSame('own content', $repository->findFirstForMember($memberId)?->getContent());
        $this->assertSame('own content', $repository->findByIdForMember($ownModel->id, $memberId)?->getContent());
        $this->assertNull($repository->findByIdForMember($otherModel->id, $memberId));
    }
}
