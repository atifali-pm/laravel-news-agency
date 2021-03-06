<?php

namespace Tests\Unit;

use App\Comment;
use App\Story;
use App\User;
use Illuminate\Support\Str;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StoryTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function a_story_has_an_author(){
        $this->withoutExceptionHandling();
        $story = factory(Story::class)->create();

        $this->assertInstanceOf(User::class, $story->author);
    }

    /** @test */
    public function a_story_has_a_path()
    {
        $story = factory(Story::class)->create();

        $this->assertEquals('/stories/' . $story->id, $story->path());

    }

    /** @test */
    public function a_story_can_add_comments()
    {
        $story = factory(Story::class)->create();

        $data = [
            'detail' => $this->faker->paragraph,
            'writer_id' => factory(User::class)->create()->id,
        ];

        $response = $story->addComment($data);

        $this->assertInstanceOf(Comment::class, $response);
        $this->assertCount(1, Comment::all());

        $this->assertDatabaseHas('comments', $data);
    }
}
