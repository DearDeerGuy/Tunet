# Tunet API Documentation

**Base URL:** `http://localhost:8000`

All endpoints accept and return data in **JSON** format.

Some endpoints require **Bearer Token Authentication**.


## User → New Message Verify

- **Method:** `POST`
- **URL:** `{{baseURL}}/api/email/verification-notification`


### Example Response:
```json
{
  "status": "success",
  "data": { ... }
}
```


---


## User → Login

- **Method:** `POST`
- **URL:** `{{baseURL}}/api/login`

### Query Parameters:

- `email` *(example: `jconn@example.net`)* — Электронная почта пользователя (уникальная).
- `password` *(example: `password`)* — Пароль пользователя (минимум 8 символов).

### Headers:

- `Accept`: `application/json`

### Example Response:
```json
{
    "access_token": "4|8CRVX3KhHKU0h4Q48TJdLEwoQHtfXeEZbJKrttSJ8159145d",
    "token_type": "Bearer",
    "user": {
        "id": 7,
        "name": "maks",
        "email": "anonymousxlxhack1@gmail.com",
        "avatar": null,
        "date_of_birth": "2004-01-07",
        "email_verified_at": null,
        "created_at": "2025-08-20T17:57:45.000000Z",
        "updated_at": "2025-08-20T17:57:45.000000Z",
        "admin_lvl": 0,
        "isBanned": 0
    }
}
```


---


## User → Register

- **Method:** `POST`
- **URL:** `{{baseURL}}/api/register`

### Query Parameters:

- `name` *(example: `maks`)* — Имя пользователя или название категории/фильма.
- `password` *(example: `651130maks`)* — Пароль пользователя (минимум 8 символов).
- `password_confirmation` *(example: `651130maks`)* — Подтверждение пароля, должно совпадать с `password`.
- `email` *(example: `anonymousxlxhack@gmail.com1`)* — Электронная почта пользователя (уникальная).
- `date_of_birth` *(example: `2004-01-07 `)* — Дата рождения пользователя в формате `YYYY-MM-DD`.

### Headers:

- `Accept`: `application/json`

### Example Response:
```json
{
    "access_token": "3|cjNAWzipZ6yzSUe5VC32lIe5v6lyNAWkugxK7EM81a894e34",
    "token_type": "Bearer",
    "user": {
        "name": "maks",
        "email": "anonymousxlxhack1@gmail.com",
        "date_of_birth": "2004-01-07",
        "updated_at": "2025-08-20T17:57:45.000000Z",
        "created_at": "2025-08-20T17:57:45.000000Z",
        "id": 7
    }
}
```


---


## User → Verify

- **Method:** `GET`
- **URL:** `{{baseURL}}/api/email/verify/{user_id}/{token}`

### Query Parameters:

- `expires` *(example: `1748963975`)* — Время истечения ссылки (timestamp).
- `signature` *(example: `eff16bbdb9e5af9dbe7d83329e0810fc867c1f3f3250b4f00c8ea03b9dd5ba95`)* — Подпись для проверки подлинности ссылки.

### Authentication:
- Requires Bearer Token

### Example Response:
```json
{
  "status": "success",
  "data": { ... }
}
```


---


## Category → Index

- **Method:** `GET`
- **URL:** `{{baseURL}}/api/category`

### Query Parameters:

- `per_page` *(example: `10`)* — Количество элементов на страницу.

### Authentication:
- Requires Bearer Token

### Example Response:
```json
{
    "current_page": 1,
    "data": [
        {
            "id": 1,
            "slug": "action",
            "name": "Action",
            "created_at": "2025-08-19T18:11:43.000000Z",
            "updated_at": "2025-08-19T18:11:43.000000Z"
        },
        {
            "id": 2,
            "slug": "drama",
            "name": "Drama",
            "created_at": "2025-08-19T18:11:43.000000Z",
            "updated_at": "2025-08-19T18:11:43.000000Z"
        },
        {
            "id": 3,
            "slug": "comedy",
            "name": "Comedy",
            "created_at": "2025-08-19T18:11:43.000000Z",
            "updated_at": "2025-08-19T18:11:43.000000Z"
        },
        {
            "id": 4,
            "slug": "sci-fi",
            "name": "Sci-Fi",
            "created_at": "2025-08-19T18:11:43.000000Z",
            "updated_at": "2025-08-19T18:11:43.000000Z"
        },
        {
            "id": 5,
            "slug": "fantasy",
            "name": "Fantasy",
            "created_at": "2025-08-19T18:11:43.000000Z",
            "updated_at": "2025-08-19T18:11:43.000000Z"
        },
        {
            "id": 6,
            "slug": "thriller",
            "name": "Thriller",
            "created_at": "2025-08-19T18:11:43.000000Z",
            "updated_at": "2025-08-19T18:11:43.000000Z"
        },
        {
            "id": 7,
            "slug": "horror",
            "name": "Horror",
            "created_at": "2025-08-19T18:11:43.000000Z",
            "updated_at": "2025-08-19T18:11:43.000000Z"
        }
    ],
    "first_page_url": "http://localhost:8000/api/category?page=1",
    "from": 1,
    "last_page": 1,
    "last_page_url": "http://localhost:8000/api/category?page=1",
    "links": [
        {
            "url": null,
            "label": "&laquo; Previous",
            "active": false
        },
        {
            "url": "http://localhost:8000/api/category?page=1",
            "label": "1",
            "active": true
        },
        {
            "url": null,
            "label": "Next &raquo;",
            "active": false
        }
    ],
    "next_page_url": null,
    "path": "http://localhost:8000/api/category",
    "per_page": 10,
    "prev_page_url": null,
    "to": 7,
    "total": 7
}
```


---


## Category → Show

- **Method:** `GET`
- **URL:** `{{baseURL}}/api/category/{category_id}`


### Authentication:
- Requires Bearer Token

### Example Response:
```json
{
  "status": "success",
  "data": { ... }
}
```


---


## Reviews → Index

- **Method:** `GET`
- **URL:** `{{baseURL}}/api/reviews`

### Query Parameters:

- `perPage` *(example: `2`)* — Количество элементов на страницу (альтернативное название).
- `film_id` *(example: `1`)* — ID фильма для добавления в избранное или для отзывов.

### Authentication:
- Requires Bearer Token

### Example Response:
```json
{
  "status": "success",
  "data": { ... }
}
```


---


## Reviews → Show

- **Method:** `GET`
- **URL:** `{{baseURL}}/api/reviews/{reviews_id}`


### Authentication:
- Requires Bearer Token

### Example Response:
```json
{
  "status": "success",
  "data": { ... }
}
```


---


## Reviews → Create

- **Method:** `POST`
- **URL:** `{{baseURL}}/api/reviews/`


### Authentication:
- Requires Bearer Token

### Example Response:
```json
{
  "status": "success",
  "data": { ... }
}
```


---


## Reviews → Update

- **Method:** `PATCH`
- **URL:** `{{baseURL}}/api/reviews/{reviews_id}`

### Query Parameters:


### Authentication:
- Requires Bearer Token

### Example Response:
```json
{
  "status": "success",
  "data": { ... }
}
```


---


## Reviews → Delete

- **Method:** `DELETE`
- **URL:** `{{baseURL}}/api/reviews/{reviews_id}`

### Query Parameters:


### Authentication:
- Requires Bearer Token

### Example Response:
```json
{
  "status": "success",
  "data": { ... }
}
```


---


## Admin → Category → Create

- **Method:** `POST`
- **URL:** `{{baseURL}}/api/category`

### Query Parameters:

- `slug` *(example: `Test1`)* — Символьный уникальный идентификатор категории (например, `action`).
- `name` *(example: `test1`)* — Имя пользователя или название категории/фильма.

### Authentication:
- Requires Bearer Token

### Example Response:
```json
{
  "status": "success",
  "data": { ... }
}
```


---


## Admin → Category → Update

- **Method:** `PATCH`
- **URL:** `{{baseURL}}/api/category/{category_id}`

### Query Parameters:

- `slug` *(example: `Test12`)* — Символьный уникальный идентификатор категории (например, `action`).
- `name` *(example: `test12`)* — Имя пользователя или название категории/фильма.

### Authentication:
- Requires Bearer Token

### Example Response:
```json
{
  "status": "success",
  "data": { ... }
}
```


---


## Admin → Category → Delete

- **Method:** `DELETE`
- **URL:** `{{baseURL}}/api/category/{category_id}`

### Query Parameters:


### Authentication:
- Requires Bearer Token

### Example Response:
```json
{
  "status": "success",
  "data": { ... }
}
```


---


## Admin → Film → Create

- **Method:** `POST`
- **URL:** `{{baseURL}}/api/film`


### Authentication:
- Requires Bearer Token

### Request Body:

- `poster` (file, example: `/home/maks/Изображения/Снимки экрана/Снимок экрана от 2025-06-16 12-26-00.png`) — Файл изображения (постер фильма/сериала).
- `type` (text, example: `film`) — Тип объекта: `film` или `serial`.
- `release_date` (text, example: `1999-01-01`) — Дата выхода фильма/сериала (YYYY-MM-DD).
- `description` (text, example: `Test desc`) — Описание фильма/сериала.
- `title` (text, example: `Test title`) — Название фильма/сериала.

### Example Response:
```json
{
  "status": "success",
  "data": { ... }
}
```


---


## Film → Index

- **Method:** `GET`
- **URL:** `{{baseURL}}/api/film`

### Query Parameters:

- `page` *(example: `2`)* — Номер страницы (для пагинации).
- `per_page` *(example: `2`)* — Количество элементов на страницу.

### Authentication:
- Requires Bearer Token

### Example Response:
```json
{
  "status": "success",
  "data": { ... }
}
```


---


## Film → Show

- **Method:** `GET`
- **URL:** `{{baseURL}}/api/film/{film_id}`


### Authentication:
- Requires Bearer Token

### Example Response:
```json
{
  "status": "success",
  "data": { ... }
}
```


---


## Favorite → Create Favorite

- **Method:** `POST`
- **URL:** `{{baseURL}}/api/favorite`

### Query Parameters:

- `film_id` *(example: `None`)* — ID фильма для добавления в избранное или для отзывов.

### Authentication:
- Requires Bearer Token

### Example Response:
```json
{
  "status": "success",
  "data": { ... }
}
```


---


## Favorite → Favorite

- **Method:** `GET`
- **URL:** `{{baseURL}}/api/favorite`


### Authentication:
- Requires Bearer Token

### Example Response:
```json
{
  "status": "success",
  "data": { ... }
}
```


---
