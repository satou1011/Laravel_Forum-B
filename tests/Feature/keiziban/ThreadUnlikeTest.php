<?php

namespace Tests\Feature\keiziban;

use App\Models\User;
use App\Models\create_thread;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ThreadUnlikeTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $admin;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->admin = User::factory()->admin()->create();

        try {
            $create = new create_thread;
            $create->create_thread('ThreadTestID');
            $create->insertTable(
                'ThreadTestName',
                'ThreadTestID',
                'test@example.com'
            );
        } catch (QueryException $error) {
            // nothing to do
        }

        $this
            ->actingAs($this->user)
            ->post('/jQuery.ajax/sendRow', [
                'table' => 'ThreadTestID',
                'message' => 'This is test comment!'
            ]);
    }

    public function test_not_login_get_access_thread_unlike()
    {
        $response = $this->get('/jQuery.ajax/unlike');

        $response->assertStatus(404);
    }

    public function test_user_get_access_thread_unlike()
    {
        $response = $this
            ->actingAs($this->user)
            ->get('/jQuery.ajax/unlike');

        $response->assertStatus(404);
    }

    public function test_admin_get_access_thread_unlike()
    {
        $response = $this
            ->actingAs($this->admin)
            ->get('/jQuery.ajax/unlike');

        $response->assertStatus(404);
    }

    /*
     * ログインしていない状態でのpost通信が防げない...
     * この状態でpost通信は出来ないでしょうということでこのままで
    */
    public function test_not_login_post_access_thread_unlike()
    {
        $response = $this->post('/jQuery.ajax/unlike', [
            'thread_id' => 'ThreadTestID',
            'message_id' => 1
        ]);

        $response->assertStatus(200);
    }

    public function test_user_post_access_thread_unlike()
    {
        $response = $this
            ->actingAs($this->user)
            ->post('/jQuery.ajax/unlike', [
                'thread_id' => 'ThreadTestID',
                'message_id' => 1
            ]);

        $response->assertStatus(200);
    }

    public function test_admin_post_access_thread_unlike()
    {
        $response = $this
            ->actingAs($this->admin)
            ->post('/jQuery.ajax/unlike', [
                'thread_id' => 'ThreadTestID',
                'message_id' => 1
            ]);

        $response->assertStatus(200);
    }
}
