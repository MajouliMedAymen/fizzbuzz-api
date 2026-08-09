# FizzBuzz API

REST API exposing a parameterisable fizz-buzz generator, plus a 
statistics endpoint reporting the most frequently requested parameter set.

**Stack:** PHP 8.3 · Symfony 7.1 · Doctrine ORM 3 · PostgreSQL 16 · Docker

---

## Run it with Docker

```bash
docker compose up --build -d
curl 'http://localhost:8080/api/v1/fizzbuzz?int1=3&int2=5&limit=15&str1=fizz&str2=buzz'
```

The app container waits for PostgreSQL, applies the migrations, then serves on
`http://localhost:8080`. Stop it with `docker compose down`.

---

## Endpoints

| Method | Path | Description |
| ------ | ---- | ---------- |
| `GET` | `/` | Homepage: what the API is, its status, and example calls for every endpoint below |
| `GET` | `/api/v1/fizzbuzz` | Generates the sequence. Params: `int1`, `int2`, `limit`, `str1`, `str2` |
| `GET` | `/api/v1/statistics` | Most frequent request and its hit count. No parameter |
| `GET` | `/health/live` | Liveness |
| `GET` | `/health/ready` | Readiness (checks PostgreSQL) |

### Examples

```bash
curl 'http://localhost:8080/'
```

```json
{
  "name": "FizzBuzz API",
  "description": "Parameterisable fizz-buzz generator with request statistics.",
  "status": "ok",
  "database": "ok",
  "endpoints": {
    "fizzbuzz": {
      "method": "GET",
      "path": "/api/v1/fizzbuzz",
      "description": "Generates the fizz-buzz sequence for the given rules.",
      "example": "/api/v1/fizzbuzz?int1=3&int2=5&str1=fizz&str2=buzz&limit=15"
    },
    "statistics": {
      "method": "GET",
      "path": "/api/v1/statistics",
      "description": "Returns the most frequently requested parameter set and its hit count."
    },
    "health": { "live": "/health/live", "ready": "/health/ready" }
  }
}
```

```bash
curl 'http://localhost:8080/api/v1/fizzbuzz?int1=3&int2=5&limit=15&str1=fizz&str2=buzz'
```

```json
{
  "parameters": { "int1": 3, "int2": 5, "limit": 15, "str1": "fizz", "str2": "buzz" },
  "count": 15,
  "result": ["1","2","fizz","4","buzz","fizz","7","8","fizz","buzz","11","fizz","13","14","fizzbuzz"]
}
```

```bash
curl 'http://localhost:8080/api/v1/statistics'
```

```json
{
  "parameters": { "int1": 3, "int2": 5, "limit": 15, "str1": "fizz", "str2": "buzz" },
  "hits": 42,
  "lastHitAt": "2026-08-05T10:12:31+00:00"
}
```

Invalid input returns `422`:

```bash
curl 'http://localhost:8080/api/v1/fizzbuzz?int1=0&int2=5&limit=15&str1=fizz&str2=buzz'
```

```json
{
  "type": "about:blank",
  "title": "Validation failed",
  "status": 422,
  "detail": "The request parameters are invalid.",
  "violations": [{ "field": "int1", "message": "int1 must be a strictly positive integer." }]
}
```
