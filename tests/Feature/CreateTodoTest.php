<?php

namespace Tests\Feature;

use App\Infrastructure\Database\Models\Difficulty;
use App\Infrastructure\Database\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateTodoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // 難易度の初期データを作成
        Difficulty::create(['id' => 1, 'name' => '小', 'points' => 1, 'color' => '#4CAF50']);
        Difficulty::create(['id' => 2, 'name' => '中', 'points' => 2, 'color' => '#FF9800']);
        Difficulty::create(['id' => 3, 'name' => '大', 'points' => 3, 'color' => '#F44336']);

        // タグの初期データを作成
        Tag::create(['id' => 1, 'name' => '個人開発']);
        Tag::create(['id' => 2, 'name' => '勉強']);
    }

    public function test_can_create_todo(): void
    {
        $payload = [
            'difficulty_id' => 1,
            'content' => 'Write docs',
            'tag_ids' => [1, 2],
        ];

        $response = $this->postJson('/api/todos', $payload);

        $response->assertCreated();
        $response->assertJsonStructure([
            'id', 'difficulty_id', 'content', 'completed_at', 'created_at', 'updated_at', 'tag_ids'
        ]);

        $this->assertDatabaseHas('todos', [
            'difficulty_id' => 1,
            'content' => 'Write docs',
        ]);

        // タグの関連付けを確認
        $this->assertDatabaseHas('todo_tag', [
            'todo_id' => 1,
            'tag_id' => 1,
        ]);
        $this->assertDatabaseHas('todo_tag', [
            'todo_id' => 1,
            'tag_id' => 2,
        ]);
    }

    public function test_cannot_create_todo_without_tag(): void
    {
        $payload = [
            'difficulty_id' => 1,
            'content' => 'Write docs',
        ];

        $response = $this->postJson('/api/todos', $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['tag_ids']);
    }

    public function test_cannot_create_todo_without_difficulty(): void
    {
        $payload = [
            'content' => 'Write docs',
            'tag_ids' => [1],
        ];

        $response = $this->postJson('/api/todos', $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['difficulty_id']);
    }

    public function test_cannot_create_todo_with_invalid_difficulty(): void
    {
        $payload = [
            'difficulty_id' => 999,
            'content' => 'Write docs',
            'tag_ids' => [1],
        ];

        $response = $this->postJson('/api/todos', $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['difficulty_id']);
    }

    public function test_cannot_create_todo_with_invalid_tag(): void
    {
        $payload = [
            'difficulty_id' => 1,
            'content' => 'Write docs',
            'tag_ids' => [999],
        ];

        $response = $this->postJson('/api/todos', $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['tag_ids.0']);
    }
}

