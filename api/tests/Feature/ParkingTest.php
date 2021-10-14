<?php
namespace Tests\Feature\SysAdmin;

use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class ParkingTest extends TestCase
{

    public function testCreateComment(){
        $post = Post::first();

        $response = $this->postJson("api/posts/{$post->id}/comments", []);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        //not uuid comment id
        $response = $this->postJson("api/posts/{$post->id}/comments", [
            'user_name' => Str::random(10),
            'message' => Str::random(15),
            'to_comment_id' => Str::random(15),
        ]);
        $response->assertStatus(    Response::HTTP_UNPROCESSABLE_ENTITY);


        //not existing comment id
        $response = $this->postJson("api/posts/{$post->id}/comments", [
            'user_name' => Str::random(10),
            'message' => Str::random(15),
            'to_comment_id' => Uuid::uuid4()->toString(),
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);


        //1 parent comment
        $response = $this->postJson("api/posts/{$post->id}/comments", [
            'user_name' => Str::random(10),
            'message' => Str::random(15),
        ]);
        $commentData = $this->decode($response)->data;

        //2 reply to parent comment
        $response = $this->postJson("api/posts/{$post->id}/comments", [
            'user_name' => Str::random(10),
            'message' => Str::random(15),
            'to_comment_id' =>$commentData->id,
        ]);
        $secondCommentData = $this->decode($response)->data;
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'message'
        ]);

        //2 reply again to parent comment
        $response = $this->postJson("api/posts/{$post->id}/comments", [
            'user_name' => Str::random(10),
            'message' => Str::random(15),
            'to_comment_id' =>$commentData->id,
        ]);
        $secondCommentData = $this->decode($response)->data;
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'message'
        ]);

        //3
        $response = $this->postJson("api/posts/{$post->id}/comments", [
            'user_name' => Str::random(10),
            'message' => Str::random(15),
            'to_comment_id' => $secondCommentData->id,
        ]);
        $thirdCommentData = $this->decode($response)->data;
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'message'
        ]);

        //4 - cannot process allowed 3 layers only
        $response = $this->postJson("api/posts/{$post->id}/comments", [
            'user_name' => Str::random(10),
            'message' => Str::random(15),
            'to_comment_id' => $thirdCommentData->id,
        ]);
        $fourthCommentData = $this->decode($response)->data;
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        $response->assertJsonStructure([
            'message'
        ]);
    }

    public function testViewPost(){
        $post = Post::first();
        $response = $this->getJson("api/posts/{$post->id}", []);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'message'
        ]);
    }

    //test parking

    public function testParkVehicle(){
        //register to parking transaction
    }

    public function testUnparkVehicle(){
        //record rates
    }

}
