<?php

namespace Tests\Feature\Infrastructure\Repository;

use App\Domain\Entity\HappySchemaActionPlan;
use App\Infrastructure\Database\Models\HappySchemaActionPlan as HappySchemaActionPlanModel;
use App\Infrastructure\Repository\EloquentHappySchemaActionPlanRepository;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class EloquentHappySchemaActionPlanRepositoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('happy_schema_action_plans');
        Schema::create('happy_schema_action_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_id')->unique();
            $table->text('happy_schema')->nullable();
            $table->text('action_plan')->nullable();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('happy_schema_action_plans');

        parent::tearDown();
    }

    public function test_save_for_member_updates_existing_single_record_when_entity_has_no_id(): void
    {
        $memberId = 123;
        $repository = new EloquentHappySchemaActionPlanRepository;

        $created = $repository->saveForMember(
            HappySchemaActionPlan::createNew('first schema', 'first plan'),
            $memberId
        );

        $updated = $repository->saveForMember(
            HappySchemaActionPlan::createNew('updated schema', 'updated plan'),
            $memberId
        );

        $this->assertSame($created->getId(), $updated->getId());
        $this->assertSame(1, HappySchemaActionPlanModel::where('member_id', $memberId)->count());
        $this->assertDatabaseHas('happy_schema_action_plans', [
            'id' => $created->getId(),
            'member_id' => $memberId,
            'happy_schema' => 'updated schema',
            'action_plan' => 'updated plan',
        ]);
    }

    public function test_find_methods_are_scoped_to_member(): void
    {
        $repository = new EloquentHappySchemaActionPlanRepository;

        $own = $repository->saveForMember(
            HappySchemaActionPlan::createNew('own schema', 'own plan'),
            123
        );

        $other = $repository->saveForMember(
            HappySchemaActionPlan::createNew('other schema', 'other plan'),
            456
        );

        $this->assertSame($own->getId(), $repository->findFirstForMember(123)?->getId());
        $this->assertNull($repository->findByIdForMember($other->getId(), 123));

        $plans = $repository->findAllForMemberOrderedByLatest(123);

        $this->assertCount(1, $plans);
        $this->assertSame($own->getId(), $plans[0]->getId());
    }
}
