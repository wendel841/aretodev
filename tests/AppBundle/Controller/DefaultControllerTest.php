<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\Post;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    /**
     * @var EntityManager
     */
    private $_em;

    protected function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $this->_em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $this->_em->beginTransaction();
    }

    public function tearDown()
    {
        $this->_em->rollback();
    }

    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Symfony3', $crawler->filter('.container')->text());
    }

    public function testCreatePost()
    {
        $slug = 'test-post';

        $post = new Post();
        $post->setTitle('Test post');
        $post->setSlug($slug);
        $post->setText('Blog post text');

        $this->assertEquals($slug, $post->getSlug());

        $posts = $this->_em->getRepository(Post::class)->findBy([
            'slug' => $post->getSlug(),
        ]);

        $this->assertTrue(count($posts) == 0);

        $this->_em->persist($post);
        $this->_em->flush();

        $posts = $this->_em->getRepository(Post::class)->findBy([
            'slug' => $post->getSlug(),
        ]);

        $this->assertTrue(count($posts) > 0);
    }
}
