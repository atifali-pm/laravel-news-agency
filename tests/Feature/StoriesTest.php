<?php

namespace Tests\Feature;

use App\Story;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StoriesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function a_guest_can_only_view_the_story()
    {
        $story = factory(Story::class)->create();

        $this->get('/stories/create')->assertRedirect('/login');
        $this->post('/stories', $story->toArray())->assertRedirect('/login');
        $this->patch($story->path(), $this->data())->assertRedirect('/login');
        $this->delete($story->path())->assertRedirect('/login');

    }

    /** @test */
    public function a_visitor_can_browse_the_list_of_stories()
    {
        $this->withoutExceptionHandling();
        $story = factory(Story::class)->create();
        $this->assertInstanceOf(Story::class, $story);

        $this->get('/stories')
             ->assertOk();
    }

    /** @test */
    public function a_visitor_can_view_one_post()
    {
        $this->withoutExceptionHandling();
        $story = factory(Story::class)->create();

        $this->get($story->path())
             ->assertOk()
             ->assertSee($story->title)
             ->assertSee($story->description)
             ->assertSee($story->author_id);
    }

    /** @test */
    public function a_user_can_add_a_story()
    {
        $attributes = $this->data();

        $user = factory(User::class)->create();

        $this->be($user)->get('/stories/create')->assertOk();

        $this->be($user)
             ->post('/stories', $attributes)->assertRedirect('/stories');


        $this->assertCount(1, Story::all());
        $this->assertDatabaseHas('stories', ['title' => $attributes['title']]);
    }

    /** @test */
    public function a_story_can_be_updated()
    {

        $story = factory(Story::class)->create();

        $updatedAttribues = array_merge($this->data(), [
            'title'       => 'New title',
            'description' => 'New description',
        ]);

        $this->be(factory(User::class)->create())
             ->patch($story->path(), $updatedAttribues)->assertOk();
        $this->assertDatabaseHas('stories', $updatedAttribues);

    }

    /** @test */
    public function a_story_can_be_deleted()
    {
        $story = factory(Story::class)->create();

        $this->be(factory(User::class)->create())
             ->delete($story->path())->assertRedirect('/stories');
        $this->assertCount(0, Story::all());
    }


    /** @test */
    public function a_title_is_required()
    {
        $attributes = array_merge($this->data(), ['title' => '']);

        $this->be(factory(User::class)->create())
             ->post('/stories', $attributes)
             ->assertSessionHasErrors('title');

        $this->assertCount(0, Story::all());
    }

    /** @test */
    public function a_description_is_required()
    {
        $attributes = array_merge($this->data(), ['description' => '']);

        $this->be(factory(User::class)->create())
             ->post('/stories', $attributes)
             ->assertSessionHasErrors('description');

        $this->assertDatabaseMissing('stories', $attributes);
    }

    public function data()
    {
        return [
            'title'       => $this->faker->text,
            'description' => $this->faker->paragraph,
            'published'   => true,
        ];
    }
}
