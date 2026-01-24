<?php

namespace Keystone\Tests\Unit\Blog;

use Keystone\Plugins\Blog\Domain\BlogRepositoryInterface;
use PDO;
use PHPUnit\Framework\TestCase;

final class BlogRepositoryTest extends TestCase
{
    private PDO $db;

    protected function setUp(): void
    {
        $this->db = new PDO('sqlite::memory:');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->createSchema();
        $this->seedData();
    }

    public function test_it_finds_post_by_slug_and_locale(): void
    {
        $repo = new BlogRepositoryInterface($this->db);

        $post = $repo->findBySlugAndLocale('hello-world', 'en_US');

        self::assertNotNull($post);
        self::assertSame('Hello world', $post['title']);
    }

    public function test_it_returns_null_if_locale_not_found(): void
    {
        $repo = new BlogRepositoryInterface($this->db);

        $post = $repo->findBySlugAndLocale('hello-world', 'nl_NL');

        self::assertNull($post);
    }

    private function createSchema(): void
    {
        $this->db->exec(
            'CREATE TABLE blog_posts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                slug TEXT,
                status TEXT
            )'
        );

        $this->db->exec(
            'CREATE TABLE blog_post_translations (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                post_id INTEGER,
                locale TEXT,
                title TEXT,
                content TEXT
            )'
        );
    }

    private function seedData(): void
    {
        $this->db->exec(
            "INSERT INTO blog_posts (id, slug, status)
             VALUES (1, 'hello-world', 'published')"
        );

        $this->db->exec(
            "INSERT INTO blog_post_translations
             (post_id, locale, title, content)
             VALUES (1, 'en_US', 'Hello world', 'Content')"
        );
    }
}

?>