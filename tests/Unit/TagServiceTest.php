<?php

namespace Tests\Unit;

use App\Models\Board;
use App\Models\Tag;
use App\Models\User;
use App\Service\TagService;
use Tests\TestCase;

class TagServiceTest extends TestCase
{
    /** @var TagService */
    private $tag_service;

    /** @var Tag */
    private $tag;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tag_service = app()->make(TagService::class);
        $this->tag = app()->make(Tag::class);
    }

    /**
     * @test
     * @group getBoardCountPerTag
     */
    public function getBoardCountPerTag()
    {
        $user = factory(User::class)->create();

        $board1 = factory(Board::class)->make();
        $user->boards()->save($board1);
        $board2 = factory(Board::class)->make();
        $user->boards()->save($board2);
        $board3 = factory(Board::class)->make();
        $user->boards()->save($board3);

        $tag1 = factory(Tag::class)->create(['name' => 'tag1']);
        $tag2 = factory(Tag::class)->create(['name' => 'tag2']);

        $board1->tags()->attach($tag1);

        $board2->tags()->attach($tag2);
        $board3->tags()->attach($tag2);

        $tags = $this->tag_service->getBoardCountPerTag();

        $this->assertEquals(1, $tags->shift()->board_count);
        $this->assertEquals(2, $tags->shift()->board_count);
    }

    /**
     * @test
     * @group retrieveBoardPaginationByTagName
     */
    public function retrieveBoardPaginationByTagName()
    {
        $user = factory(User::class)->create();

        $board1 = factory(Board::class)->make();
        $user->boards()->save($board1);
        $board2 = factory(Board::class)->make();
        $user->boards()->save($board2);
        $board3 = factory(Board::class)->make();
        $user->boards()->save($board3);

        $tag1 = factory(Tag::class)->create(['name' => 'tag1']);
        $tag2 = factory(Tag::class)->create(['name' => 'tag2']);

        $board1->tags()->attach($tag1);
        $board2->tags()->attach($tag2);
        $board3->tags()->attach($tag2);

        $board1 = $this->tag_service->retrieveBoardPaginationByTagName('tag1');
        $board2 = $this->tag_service->retrieveBoardPaginationByTagName('tag2');

        $this->assertCount(1, $board1);
        $this->assertCount(2, $board2);
    }

    /**
     * @test
     * @group createIfNotExists
     */
    public function createIfNotExists()
    {
        $tag1 = 'foo';
        $tag2 = 'bar';
        $tag3 = 'baz';

        $this->tag_service->createIfNotExists([$tag1, $tag2]);
        $this->tag_service->createIfNotExists([$tag1, $tag3]);

        $this->assertCount(3, $this->tag->whereIn('name', [$tag1, $tag2, $tag3])->get());
    }
}