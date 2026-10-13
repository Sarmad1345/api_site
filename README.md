# api_site

A blog REST API built with Laravel. It uses the same database schema as the
`blogging_site` project: blogs with an email, title, description, cover image,
category, tags and soft deletes.

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan storage:link
php artisan serve
```

The API is then available at `http://localhost:8000/api`.

`php artisan storage:link` is needed so uploaded blog images are reachable at
the `image.url` returned by the API.

### Sharing the database with blogging_site

`api_site` and `blogging_site` can point at the same database (`blog_site`).
The blog migrations here use the same file names as in `blogging_site`, so
`php artisan migrate` skips the ones that already ran there.

## Endpoints

### Blogs

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/blogs` | Paginated list of blogs (10 per page, `?page=2`) with category, tags and image |
| GET | `/api/blog/{id}` | One blog by id (404 if not found) |
| POST | `/api/addBlog` | Add a blog (multipart form, see fields below) |
| PUT | `/api/updateBlog` | Update a blog (see fields below, 404 if not found) |
| DELETE | `/api/deleteBlog/{id}` | Move a blog to the trash (soft delete) |
| GET | `/api/searchBlogs?search=term` | Search title and description (paginated) |
| GET | `/api/trashedBlogs` | Blogs in the trash |
| PATCH | `/api/restoreBlog/{id}` | Restore a blog from the trash |
| DELETE | `/api/forceDeleteBlog/{id}` | Permanently delete a blog |

### Categories and tags

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/categories` | All categories, sorted by name |
| GET | `/api/tags` | All tags, sorted by name |

### Other

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/test` | Test route, returns sample data |
| GET | `/api/user` | Current user (requires Sanctum token) |

## Blog fields

| Field | Add | Update | Rules |
|---|---|---|---|
| `id` | – | required | id of the blog to update |
| `email` | required | required | valid email, unique per blog |
| `title` | required | required | max 255 characters |
| `description` | required | required | max 5000 characters |
| `category_id` | required | required | must exist in `categories` |
| `file` | required | optional | image, max 2 MB |
| `tags[]` | optional | optional | tag ids; on update, sending `tags` replaces the blog's tags |

Validation errors return `422` with a JSON body listing the invalid fields.

### Examples

Add a blog:

```bash
curl -X POST http://localhost:8000/api/addBlog \
  -H "Accept: application/json" \
  -F "email=sarmad@example.com" \
  -F "title=My first blog" \
  -F "description=Hello world" \
  -F "category_id=1" \
  -F "tags[]=1" \
  -F "file=@cover.jpg"
```

Update a blog with a new image. PHP does not read files sent with a real `PUT`
request, so send a `POST` with `_method=PUT`:

```bash
curl -X POST http://localhost:8000/api/updateBlog \
  -H "Accept: application/json" \
  -F "_method=PUT" \
  -F "id=1" \
  -F "email=sarmad@example.com" \
  -F "title=Updated title" \
  -F "description=Updated text" \
  -F "category_id=1" \
  -F "file=@new-cover.jpg"
```

## Tests

```bash
php artisan test
```

Tests use an in-memory SQLite database, so they never touch `blog_site`.
