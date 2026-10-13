# api_site

A simple blog REST API built with Laravel.

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

The API is then available at `http://localhost:8000/api`.

## Endpoints

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/test` | Test route, returns sample data |
| GET | `/api/blogs` | Get all blogs |
| GET | `/api/blog/{id}` | Get one blog by id (404 if not found) |
| POST | `/api/addBlog` | Add a blog. Body: `title` (required, max 255), `content` (required) |
| PUT | `/api/updateBlog` | Update a blog. Body: `id`, `title`, `content` (404 if not found) |
| DELETE | `/api/deleteBlog/{id}` | Delete a blog by id |
| GET | `/api/user` | Current user (requires Sanctum token) |

### Example

```bash
curl -X POST http://localhost:8000/api/addBlog \
  -H "Content-Type: application/json" \
  -d '{"title": "My first blog", "content": "Hello world"}'
```

Validation errors return `422` with a JSON body listing the invalid fields.
