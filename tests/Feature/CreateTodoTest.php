<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateTodoTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_todo(): void
    {
        $payload = [
            'difficulty_id' => 1,
            'content' => 'Write docs',
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
    }
}

