# Blog Feature

## Current structure

The blog is a plugin-backed feature with:
- Dedicated `blog_posts` table
- Admin CRUD screen
- Public listing at `/blog/`
- Detail pages at `/blog/{slug}/`

## Data shape

Each post: `title`, `slug`, `excerpt`, `content`, `thumbnail_path`, `status` (draft/published), `published_at`, `seo_title`, `seo_description`.

## Key files

- Controller: `app/Http/Controllers/Admin/BlogController.php`
- Service: `app/Services/PostService.php`
- Model: `app/Models/BlogPostModel.php`
- Admin views: `resources/views/admin/blog/`
- Public list: `resources/views/pages/blog.php`
- Public detail: `resources/views/blog/show.php`

## Adding fields

1. Add migration for new columns.
2. Update `BlogPostModel`.
3. Update `PostService`.
4. Update admin form views.
5. Update public views.

## Rules

- Blog posts use their own table (repeatable content, not fixed pages).
- Do not store blog posts in page JSON.
- Do not mix blog CRUD with page editing logic.
