<?php

namespace Tests\Feature;

use App\Livewire\Admin\ManageFeaturedProducts;
use App\Models\BlogComment;
use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Livewire\Livewire;
use Tests\TestCase;

class StaticPagesAndRssFeedTest extends TestCase
{
    use DatabaseMigrations;

    public function test_community_about_and_contact_pages_render_successfully(): void
    {
        $this->get(route('community'))
            ->assertOk()
            ->assertSee('JOIN THE MOVEMENT');

        $this->get(route('about'))
            ->assertOk()
            ->assertSee('JJHOUSE');

        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('hello@jjhouse.com');
    }

    public function test_blog_rss_feed_renders_valid_xml(): void
    {
        $author = User::factory()->create([
            'name' => 'Editor',
            'email' => 'editor@jjhouse.com',
        ]);

        BlogPost::query()->create([
            'author_id' => $author->id,
            'title' => 'Streetwear Drop 01',
            'slug' => 'streetwear-drop-01',
            'excerpt' => 'First seasonal drop overview.',
            'content' => '<p>Full story for drop 01.</p>',
            'published_at' => now()->subHour(),
            'is_published' => true,
        ]);

        $response = $this->get(route('blog.feed'));

        $response->assertOk();
        $this->assertStringContainsString('text/xml', (string) $response->headers->get('Content-Type'));

        $xmlContent = $response->getContent();
        $this->assertStringStartsWith('<?xml version="1.0" encoding="UTF-8"?>', ltrim($xmlContent));
        $this->assertStringContainsString('<?xml-stylesheet type="text/xsl" href="/blog/feed.xsl"?>', $xmlContent);

        $parsed = simplexml_load_string($xmlContent);
        $this->assertNotFalse($parsed);
        $this->assertSame('Streetwear Drop 01', (string) $parsed->channel->item[0]->title);

        $xslResponse = $this->get(route('blog.feed.xsl'));
        $xslResponse->assertOk();
        $this->assertStringContainsString('text/xml', (string) $xslResponse->headers->get('Content-Type'));

        $xslDoc = new \DOMDocument();
        $this->assertTrue($xslDoc->loadXML($xslResponse->getContent()));
    }

    public function test_featured_products_placeholder_asset_exists_and_is_used(): void
    {
        $this->assertFileExists(public_path('images/asset.png'));

        $category = Category::query()->create([
            'name' => 'Tops',
            'slug' => 'tops',
        ]);

        Product::query()->create([
            'category_id' => $category->id,
            'name' => 'No Image Tee',
            'slug' => 'no-image-tee',
            'description' => 'Sample product without images',
            'price' => 150000,
            'stock' => 5,
            'is_active' => true,
            'is_featured_home' => true,
        ]);

        Livewire::test(ManageFeaturedProducts::class)
            ->assertOk()
            ->assertSee(asset('images/asset.png'), false)
            ->assertDontSee('storage/home/asset.png', false);
    }

    public function test_storefront_links_connect_blog_community_about_and_contact_routes(): void
    {
        $author = User::factory()->create([
            'name' => 'Editor',
            'email' => 'editor@jjhouse.com',
        ]);

        $post = BlogPost::query()->create([
            'author_id' => $author->id,
            'title' => 'Streetwear Culture Guide',
            'slug' => 'streetwear-culture-guide',
            'excerpt' => 'Guide excerpt.',
            'content' => '<p>Guide body.</p>',
            'published_at' => now()->subHour(),
            'is_published' => true,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('blog.index'), false)
            ->assertSee(route('blog.feed'), false)
            ->assertSee(route('blog.show', $post->slug), false)
            ->assertSee(route('community'), false)
            ->assertSee(route('about'), false)
            ->assertSee(route('contact'), false);

        $this->get(route('blog.index'))
            ->assertOk()
            ->assertSee(route('blog.feed'), false)
            ->assertSee(route('blog.show', $post->slug), false);

        $this->get(route('blog.show', $post->slug))
            ->assertOk()
            ->assertSee(route('blog.index'), false)
            ->assertSee('Streetwear Culture Guide');
    }

    public function test_authenticated_user_can_submit_blog_comment(): void
    {
        $user = User::factory()->create();

        $post = BlogPost::query()->create([
            'author_id' => $user->id,
            'title' => 'Commentable Post',
            'slug' => 'commentable-post',
            'excerpt' => 'Short summary.',
            'content' => '<p>Body.</p>',
            'published_at' => now()->subHour(),
            'is_published' => true,
        ]);

        $this->actingAs($user)
            ->from(route('blog.show', $post->slug))
            ->post(route('blog.comment.store'), [
                'post_id' => $post->id,
                'content' => 'Artikel yang sangat membantu!',
            ])
            ->assertRedirect(route('blog.show', $post->slug))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('blog_comments', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'content' => 'Artikel yang sangat membantu!',
            'is_approved' => false,
        ]);
    }

    public function test_admin_can_approve_blog_comment_with_patch_form_method(): void
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::query()->create([
            'name' => 'admin',
            'display_name' => 'Admin',
        ]));

        $customer = User::factory()->create();
        $post = BlogPost::query()->create([
            'author_id' => $admin->id,
            'title' => 'Moderation Post',
            'slug' => 'moderation-post',
            'excerpt' => 'Summary.',
            'content' => '<p>Body.</p>',
            'published_at' => now()->subHour(),
            'is_published' => true,
        ]);

        $comment = BlogComment::query()->create([
            'post_id' => $post->id,
            'user_id' => $customer->id,
            'content' => 'Pending comment',
            'is_approved' => false,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.blog.comments'))
            ->assertOk()
            ->assertSee('<input type="hidden" name="_method" value="PATCH">', false);

        $this->actingAs($admin)
            ->from(route('admin.blog.comments'))
            ->post(route('admin.blog.comments.approve', $comment), ['_method' => 'PATCH'])
            ->assertRedirect(route('admin.blog.comments'))
            ->assertSessionHas('success');

        $this->assertTrue($comment->fresh()->is_approved);
    }

    public function test_product_detail_shows_gallery_controls_when_additional_images_exist(): void
    {
        $category = Category::query()->create([
            'name' => 'Hoodies',
            'slug' => 'hoodies',
        ]);

        $product = Product::query()->create([
            'category_id' => $category->id,
            'name' => 'Gallery Hoodie',
            'slug' => 'gallery-hoodie',
            'description' => 'Heavyweight cotton hoodie.',
            'price' => 350000,
            'stock' => 10,
            'is_active' => true,
        ]);

        ProductImage::query()->create([
            'product_id' => $product->id,
            'image_path' => '/storage/products/main.jpg',
            'is_primary' => true,
        ]);
        ProductImage::query()->create([
            'product_id' => $product->id,
            'image_path' => '/storage/products/back.jpg',
            'is_primary' => false,
        ]);

        $this->get(route('product.show', $product->slug))
            ->assertOk()
            ->assertSee('/storage/products/main.jpg', false)
            ->assertSee('/storage/products/back.jpg', false)
            ->assertSee('Lihat Foto Lainnya (2)')
            ->assertSee('Foto Berikutnya');

        $singleImageProduct = Product::query()->create([
            'category_id' => $category->id,
            'name' => 'Single Photo Tee',
            'slug' => 'single-photo-tee',
            'description' => 'Single photo tee.',
            'price' => 150000,
            'stock' => 5,
            'is_active' => true,
        ]);

        ProductImage::query()->create([
            'product_id' => $singleImageProduct->id,
            'image_path' => '/storage/products/only.jpg',
            'is_primary' => true,
        ]);

        $this->get(route('product.show', $singleImageProduct->slug))
            ->assertOk()
            ->assertSee('/storage/products/only.jpg', false)
            ->assertDontSee('Lihat Foto Lainnya');
    }
}

