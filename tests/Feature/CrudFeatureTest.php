<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CrudFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $india = Country::create(['name' => 'India']);

        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'country_id' => $india->id,
            'email_verified_at' => now(),
        ]);
    }

    public function test_dashboard_requires_auth()
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_dashboard_displays_with_auth()
    {
        $this->seed();

        $response = $this->actingAs($this->user)->get('/dashboard');

        $response->assertOk()
            ->assertViewHas('countries')
            ->assertViewHas('totalPosts')
            ->assertViewHas('totalUsers')
            ->assertViewHas('totalCountries')
            ->assertViewHas('avgPostsPerUser')
            ->assertViewHas('chartLabels')
            ->assertViewHas('chartData')
            ->assertViewHas('pieLabels')
            ->assertViewHas('pieData')
            ->assertViewHas('topContributors')
            ->assertViewHas('yoyGrowth');
    }

    public function test_country_crud()
    {
        $response = $this->actingAs($this->user)->get('/countries');
        $response->assertOk();

        $response = $this->get('/countries/create');
        $response->assertOk();

        $response = $this->post('/countries', [
            'name' => 'Germany',
        ]);
        $response->assertRedirect('/countries');
        $this->assertDatabaseHas('countries', ['name' => 'Germany']);

        $country = Country::where('name', 'Germany')->first();

        $response = $this->get('/countries/'.$country->id.'/edit');
        $response->assertOk();

        $response = $this->put('/countries/'.$country->id, [
            'name' => 'Germany Updated',
        ]);
        $response->assertRedirect('/countries');
        $this->assertDatabaseHas('countries', ['name' => 'Germany Updated']);

        $response = $this->get('/countries/'.$country->id);
        $response->assertRedirect();

        $response = $this->delete('/countries/'.$country->id);
        $response->assertRedirect('/countries');
        $this->assertDatabaseMissing('countries', ['name' => 'Germany Updated']);
    }

    public function test_user_crud()
    {
        $india = Country::where('name', 'India')->first();

        $response = $this->actingAs($this->user)->get('/users');
        $response->assertOk();

        $response = $this->get('/users/create');
        $response->assertOk();

        $response = $this->post('/users', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'country_id' => $india->id,
        ]);
        $response->assertRedirect('/users');
        $this->assertDatabaseHas('users', ['email' => 'newuser@example.com']);

        $newUser = User::where('email', 'newuser@example.com')->first();

        $response = $this->get('/users/'.$newUser->id);
        $response->assertOk()
            ->assertViewHas('user');

        $response = $this->get('/users/'.$newUser->id.'/edit');
        $response->assertOk();

        $response = $this->put('/users/'.$newUser->id, [
            'name' => 'Updated User',
            'email' => 'updated@example.com',
            'password' => '',
            'password_confirmation' => '',
            'country_id' => $india->id,
        ]);
        $response->assertRedirect('/users');
        $this->assertDatabaseHas('users', ['email' => 'updated@example.com']);

        $response = $this->delete('/users/'.$newUser->id);
        $response->assertRedirect('/users');
        $this->assertDatabaseMissing('users', ['email' => 'updated@example.com']);
    }

    public function test_post_crud()
    {
        $india = Country::where('name', 'India')->first();
        $user = User::where('country_id', $india->id)->first();

        $response = $this->actingAs($this->user)->get('/posts');
        $response->assertOk();

        $response = $this->get('/posts/create');
        $response->assertOk();

        $response = $this->post('/posts', [
            'name' => 'Test Post',
            'user_id' => $user->id,
        ]);
        $response->assertRedirect('/posts');
        $this->assertDatabaseHas('posts', ['name' => 'Test Post']);

        $post = Post::where('name', 'Test Post')->first();

        $response = $this->get('/posts/'.$post->id);
        $response->assertOk()
            ->assertViewHas('post');

        $response = $this->get('/posts/'.$post->id.'/edit');
        $response->assertOk();

        $response = $this->put('/posts/'.$post->id, [
            'name' => 'Updated Post',
            'user_id' => $user->id,
        ]);
        $response->assertRedirect('/posts');
        $this->assertDatabaseHas('posts', ['name' => 'Updated Post']);

        $response = $this->delete('/posts/'.$post->id);
        $response->assertRedirect('/posts');
        $this->assertDatabaseMissing('posts', ['name' => 'Updated Post']);
    }

    public function test_country_details_with_filters()
    {
        $this->seed();

        $country = Country::where('name', 'India')->first();

        $response = $this->actingAs($this->user)
            ->get('/country/'.$country->id.'/posts?search=Harry&date_preset=30d&sort_by=name&sort_dir=asc');

        $response->assertOk();
    }

    public function test_csv_export()
    {
        $this->seed();

        $country = Country::where('name', 'India')->first();

        $response = $this->actingAs($this->user)
            ->get('/country/'.$country->id.'/posts/export/csv');

        $response->assertOk();
        $this->assertStringStartsWith('text/csv', $response->headers->get('Content-Type'));
    }

    public function test_pdf_export()
    {
        $this->seed();

        $country = Country::where('name', 'India')->first();

        $response = $this->actingAs($this->user)
            ->get('/country/'.$country->id.'/posts/export/pdf');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_excel_export()
    {
        $this->seed();

        $country = Country::where('name', 'India')->first();

        $response = $this->actingAs($this->user)
            ->get('/country/'.$country->id.'/posts/export/excel?format=xlsx');

        $response->assertOk();
        $this->assertStringStartsWith(
            'application/vnd.openxmlformats',
            $response->headers->get('Content-Type'),
        );
    }
}
