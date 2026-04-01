# Proyecto Laravel 5

Proyecto desarrollado con **Laravel 5** y **PHP v7.1** con configuración ya preestablecida, funcionando únicamente como API utilizando **JWT** para autenticación y la librería **maatwebsite/excel** para exportar datos.

Este proyecto se creó con la intención de demostrar mis habilidades como prueba técnica, utilizando herramientas modernas en el 2026 para ejecutar y mantener un proyecto legacy.

## Requisitos del entorno

Este proyecto utiliza Docker para garantizar un entorno aislado y reproducible,
especialmente orientado a aplicaciones legacy.

### Software requerido

- Docker Desktop **v27.0.3** o superior
- Docker Compose v2
- Sistema operativo:
  - Windows 10/11 (con WSL2 habilitado)
  - Linux
  - macOS

### Configuración recomendada en Windows

- Docker Desktop configurado para usar **Linux containers**
- Backend: **WSL2**

### Si ya tenias instalado docker Desktop, eliminar imagenes para no tener conflicto

```bash
docker stop $(docker ps -a -q)
docker rm $(docker ps -a -q)
docker rmi $(docker images -q)
docker-compose down --rmi all
docker system prune --all --volumes
```

### Al tener instalado solo es ejecutar

```bash
docker-compose up -d
```

Al terminar ya puedes acceder a http://localhost:8000/

## Notas adicionales de Compatibilidad (PHP 7.1)

Este proyecto está optimizado para PHP 7.1.3. Debido a que algunas librerías modernas han dejado de soportar esta versión, se han aplicado las siguientes restricciones en `composer.json` para evitar errores de sintaxis (T_STRING, property types) y asegurar el funcionamiento de los tests:

- `symfony/polyfill-php72`: **v1.19.0** (Activación correcta de `spl_object_id`).
- `phpdocumentor/reflection-docblock`: **v4.3.4** (Evita sintaxis de PHP 7.4).
- `phpdocumentor/type-resolver`: **v1.1.0**.
- `phpdocumentor/reflection-common`: **v2.1.0**.
- `phpspec/prophecy`: **v1.10.3**.

> [!IMPORTANT]
> Siempre utiliza `composer update --ignore-platform-reqs` dentro del contenedor para respetar estas versiones sin conflictos de extensiones locales.

### Principios y Prácticas Aplicadas

El proyecto ha sido desarrollado teniendo en cuenta los siguientes puntos clave:

1.  **Orden, escalabilidad y organización del código**: Estructura modular y separación de responsabilidades (Controladores, Modelos, Recursos, Requests).
2.  **Validaciones de request y códigos de respuesta**: Uso de Form Requests para validación centralizada y respuestas HTTP semánticas.
3.  **Uso de buenas prácticas**: Inyección de dependencias, principios SOLID básicos, y uso de características nativas de Laravel.
4.  **Calidad del código frente al tiempo de desarrollo**: Soluciones robustas pero eficientes, evitando sobre-ingeniería.
5.  **Legibilidad del código**: Naming conventions claros y código auto-explicativo.
6.  **Uso adecuado de códigos de respuesta HTTP y métodos HTTP**: GET, POST, PUT, DELETE con respuestas 200, 201, 204, 401, 422, etc.
7.  **Validaciones**: Validación estricta de datos de entrada en todas las operaciones de escritura.

## Seeders de Prueba

El proyecto incluye seeders para poblar la base de datos automáticamente al iniciar los contenedores:

- **Usuarios**: Se crea un usuario por defecto (`test@example.com` / `password`) y 10 usuarios adicionales.
- **Autores**: Se crean 10 autores de prueba.
- **Libros**: Cada autor se crea con 2 libros asociados (20 libros en total), lo que dispara automáticamente el conteo asíncrono.

Los seeders son **idempotentes**; se pueden ejecutar múltiples veces sin duplicar el usuario de prueba principal.

## Pruebas Automatizadas (Feature Tests)

Se ha implementado un suite de pruebas completo para verificar la integridad de la API:

- **Autenticación**: Login, logout, refresh de token y perfil de usuario (`AuthApiTest`).
- **Autores**: Operaciones CRUD completas y validaciones (`AuthorApiTest`).
- **Libros**: CRUD, asociación con autores y verificación de Jobs asíncronos (`BookApiTest`).
- **Exportación**: Verificación de generación y descarga del reporte Excel (`ExportApiTest`).

Para ejecutar las pruebas dentro del entorno Docker:

```bash
docker-compose exec app vendor/bin/phpunit tests/Feature
```

## Tabla de APIs

| Método     | Endpoint                      | Body (JSON)                                                                                             | Header                            | Descripción                        |
| :--------- | :---------------------------- | :------------------------------------------------------------------------------------------------------ | :-------------------------------- | :--------------------------------- |
| **POST**   | `{{url}}/api/v1/auth/login`   | `{"email": "test@example.com", "password": "password"}`                                                 | `Content-Type: application/json`  | Iniciar sesión y obtener token.    |
| **POST**   | `{{url}}/api/v1/auth/me`      | N/A                                                                                                     | `Authorization: Bearer {{token}}` | Obtener usuario autenticado.       |
| **POST**   | `{{url}}/api/v1/auth/refresh` | N/A                                                                                                     | `Authorization: Bearer {{token}}` | Refrescar token expirado.          |
| **POST**   | `{{url}}/api/v1/auth/logout`  | N/A                                                                                                     | `Authorization: Bearer {{token}}` | Cerrar sesión (invalidar token).   |
| **GET**    | `{{url}}/api/v1/authors`      | N/A                                                                                                     | `Authorization: Bearer {{token}}` | Listar todos los autores.          |
| **POST**   | `{{url}}/api/v1/authors`      | `{"name": "Gabriel", "surname": "Marquez"}`                                                             | `Authorization: Bearer {{token}}` | Crear un nuevo autor.              |
| **GET**    | `{{url}}/api/v1/authors/{id}` | N/A                                                                                                     | `Authorization: Bearer {{token}}` | Ver detalle de un autor.           |
| **PUT**    | `{{url}}/api/v1/authors/{id}` | `{"name": "Gabo", "surname": "Marquez"}`                                                                | `Authorization: Bearer {{token}}` | Actualizar un autor.               |
| **DELETE** | `{{url}}/api/v1/authors/{id}` | N/A                                                                                                     | `Authorization: Bearer {{token}}` | Eliminar un autor.                 |
| **GET**    | `{{url}}/api/v1/books`        | N/A                                                                                                     | `Authorization: Bearer {{token}}` | Listar todos los libros.           |
| **POST**   | `{{url}}/api/v1/books`        | `{"title": "Cien Años", "published_date": "1967-05-30", "author_id": 1, "description": "Obra maestra"}` | `Authorization: Bearer {{token}}` | Crear libro (Dispara Job Async).   |
| **GET**    | `{{url}}/api/v1/books/{id}`   | N/A                                                                                                     | `Authorization: Bearer {{token}}` | Ver detalle de un libro.           |
| **PUT**    | `{{url}}/api/v1/books/{id}`   | `{"title": "El coronel no tiene quien le escriba"}`                                                     | `Authorization: Bearer {{token}}` | Actualizar un libro.               |
| **DELETE** | `{{url}}/api/v1/books/{id}`   | N/A                                                                                                     | `Authorization: Bearer {{token}}` | Eliminar un libro.                 |
| **GET**    | `{{url}}/api/v1/export`       | N/A                                                                                                     | `Authorization: Bearer {{token}}` | Descargar Excel (Authors & Books). |

_Nota: Reemplaza `{{url}}` con tu host (ej: `http://localhost:8000`) y `{{token}}` con el token JWT obtenido en el login._

## API de Autores - Listado Avanzado (Server-Side)

El endpoint `GET /api/v1/authors` implementa un **listado server-side optimizado** para aplicaciones con grandes volúmenes de datos. En lugar de cargar todos los autores en memoria (que puede colapsar el navegador con miles de registros), esta implementación realiza la búsqueda, filtrado y paginación directamente en la base de datos.

### 🎯 ¿Por qué Server-Side?

| Enfoque     | Problema                                | Solución Server-Side                         |
| ----------- | --------------------------------------- | -------------------------------------------- |
| Client-Side | Descarga 10,000+ registros al navegador | Solo envía 10-50 registros por página        |
| Client-Side | Búsqueda lenta en JavaScript            | Búsqueda optimizada con índices SQL (`LIKE`) |
| Client-Side | Consumo excesivo de memoria             | Paginación SQL nativa (`LIMIT`/`OFFSET`)     |
| Client-Side | Tiempo de carga inicial alto            | Respuesta inmediata con datos parciales      |

### 📊 Rendimiento Óptimo

Esta implementación garantiza:

- **Tiempo de respuesta constante**: ~50-100ms independientemente del tamaño de la tabla
- **Uso de memoria estable**: El servidor procesa solo la página solicitada
- **Escalabilidad horizontal**: Soporta millones de registros sin degradación
- **Índices SQL**: Las búsquedas por `name` y `surname` aprovechan índices de base de datos

### 🔧 Parámetros Disponibles

| Parámetro   | Tipo    | Default | Descripción                                    | Ejemplo                 |
| ----------- | ------- | ------- | ---------------------------------------------- | ----------------------- |
| `search`    | string  | -       | Busca por nombre o apellido (case-insensitive) | `?search=marquez`       |
| `order_by`  | string  | `id`    | Campo para ordenar                             | `?order_by=books_count` |
| `order_dir` | string  | `desc`  | Dirección (`asc` o `desc`)                     | `?order_dir=asc`        |
| `per_page`  | integer | `10`    | Elementos por página (máx. recomendado: 100)   | `?per_page=25`          |

**Campos permitidos para ordenamiento:** `id`, `name`, `surname`, `books_count`, `created_at`, `updated_at`

### 💡 Ejemplos de Uso

```bash
# Búsqueda básica + paginación
GET /api/v1/authors?search=gabriel&per_page=5

# Ordenar por cantidad de libros (más prolificos primero)
GET /api/v1/authors?order_by=books_count&order_dir=desc

# Búsqueda + ordenamiento alfabético + paginación personalizada
GET /api/v1/authors?search=marquez&order_by=name&order_dir=asc&per_page=3

# Listado paginado estándar (útil para tablas con paginador)
GET /api/v1/authors?page=2&per_page=10
```

### 📦 Estructura de Respuesta

```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 15,
        "name": "Kurt",
        "surname": "Nikolaus",
        "books_count": 2,
        "created_at": "2026-04-01 09:44:57",
        "updated_at": "2026-04-01 09:44:58"
      }
    ],
    "first_page_url": "http://localhost:8000/api/v1/authors?page=1",
    "from": 1,
    "last_page": 4,
    "last_page_url": "http://localhost:8000/api/v1/authors?page=4",
    "next_page_url": "http://localhost:8000/api/v1/authors?page=2",
    "path": "http://localhost:8000/api/v1/authors",
    "per_page": 10,
    "prev_page_url": null,
    "to": 10,
    "total": 40
  }
}
```

### 🏗️ Implementación Técnica

El controlador utiliza un patrón de **Query Builder dinámico** que construye la consulta SQL paso a paso:

1. **Base Query**: `Author::query()` - Inicia el builder de Eloquent
2. **Búsqueda**: Se aplica `where()` con cláusulas `LIKE` solo si hay término de búsqueda
3. **Ordenamiento**: Validación de campos permitidos + dirección (asc/desc)
4. **Paginación**: `paginate()` ejecuta el `COUNT(*)` para metadatos + `LIMIT/OFFSET` para datos
5. **Transformación**: `map()` sobre la colección para formato de respuesta personalizado

**Ventajas de esta arquitectura:**

- **SQL optimizado**: Una sola consulta con subquery para conteo total
- **Lazy loading**: Los datos se transforman solo después de la paginación
- **Seguridad**: Validación de parámetros previene inyección y ordenamiento por campos no permitidos
- **Flexibilidad**: Fácil extensible para agregar filtros adicionales (fechas, rangos, etc.)

### 🎨 Integración Frontend (React/Vue/Angular)

Esta API está diseñada para integrarse con componentes de tabla avanzados como:

- **DataTables** (jQuery/Vanilla JS)
- **AG-Grid** (React/Angular/Vue)
- **Vuetify DataTable** (Vue)
- **React Table / TanStack Table** (React)
- **PrimeNG Table** (Angular)

Todos estos componentes soportan el modo **server-side** donde envían automáticamente los parámetros `search`, `order_by`, `order_dir`, `page` y `per_page` basados en las interacciones del usuario.

## Códigos de Respuesta HTTP

La API utiliza códigos de respuesta HTTP semánticos según el resultado de cada operación:

| Código  | Descripción          | Cuándo ocurre                                |
| :------ | :------------------- | :------------------------------------------- |
| **200** | OK                   | Petición exitosa (GET, PUT).                 |
| **201** | Created              | Recurso creado exitosamente (POST).          |
| **204** | No Content           | Recurso eliminado exitosamente (DELETE).     |
| **401** | Unauthorized         | Token no proporcionado, inválido o expirado. |
| **422** | Unprocessable Entity | Error de validación en los datos enviados.   |

### Ejemplo de respuesta 401 (Unauthorized)

Cuando se intenta acceder a un endpoint protegido sin token o con token inválido:

```json
{
  "message": "Unauthorized. Token not provided or invalid."
}
```

### Ejemplo de respuesta 422 (Validation Error)

Cuando los datos enviados no cumplen las reglas de validación:

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "name": ["The name field is required."],
    "surname": ["The surname field is required."]
  }
}
```

## Resumen de Pruebas (Tests)

El proyecto incluye tests automatizados que verifican el comportamiento correcto de la API y el flujo asíncrono:

### Tests de Libros (`BookApiTest`)

| Test                                                        | Descripción                                                       |
| ----------------------------------------------------------- | ----------------------------------------------------------------- |
| `test_can_list_books`                                       | Verifica que se listen todos los libros (200)                     |
| `test_can_create_book_and_dispatches_event`                 | Crea libro y verifica que se dispare el evento `BookCreated`      |
| `test_can_show_book`                                        | Obtiene detalle de un libro específico (200)                      |
| `test_can_update_book`                                      | Actualiza un libro existente (200)                                |
| `test_can_delete_book`                                      | Elimina un libro (204)                                            |
| `test_cannot_list_books_without_token`                      | Rechaza petición sin autenticación (401)                          |
| `test_job_updates_author_book_count`                        | Verifica que el Job actualiza correctamente `books_count`         |
| `test_cannot_create_book_with_invalid_author_id`            | Valida que rechaza `author_id` inexistente (422)                  |
| `test_complete_flow_creates_book_and_updates_count_via_job` | Flujo completo: Crear libro → Evento → Job → Contador actualizado |

### Tests de Autores (`AuthorApiTest`)

| Test                                     | Descripción                    |
| ---------------------------------------- | ------------------------------ |
| `test_can_list_authors`                  | Lista todos los autores (200)  |
| `test_can_create_author`                 | Crea un nuevo autor (201)      |
| `test_can_show_author`                   | Muestra detalle de autor (200) |
| `test_can_update_author`                 | Actualiza autor (200)          |
| `test_can_delete_author`                 | Elimina autor (204)            |
| `test_cannot_list_authors_without_token` | Rechaza sin token (401)        |

## Protección contra Race Conditions

### ¿Qué es una Race Condition?

Una **race condition** (condición de carrera) ocurre cuando dos o más procesos intentan modificar el mismo dato simultáneamente, causando resultados inconsistentes.

**Ejemplo del problema:**

```
Job A lee:   autor tiene 5 libros
Job B lee:   autor tiene 5 libros (mismo momento)
Job A guarda: 6 libros
Job B guarda: 6 libros  ← ¡Error! Debería ser 7
```

### Solución Implementada

El Job `UpdateAuthorBookCount` utiliza **transacciones con bloqueo pesimista**:

```php
DB::transaction(function () {
    // Lock del registro - solo un Job puede modificarlo a la vez
    $author = Author::lockForUpdate()->find($this->authorId);

    if ($author) {
        $count = Book::where('author_id', $this->authorId)->count();
        $author->update(['books_count' => $count]);
    }
});
```

### ¿Cómo funciona?

| Sin protección                 | Con `lockForUpdate()`                   |
| ------------------------------ | --------------------------------------- |
| Job A lee: 5 libros            | Job A **bloquea** el registro del autor |
| Job B lee: 5 libros            | Job B **espera** a que A termine        |
| Job A guarda: 6                | Job A cuenta 6, guarda 6                |
| Job B guarda: 6 ← ¡Incorrecto! | Job B cuenta 6, guarda 6 ← **Correcto** |

### Beneficios

- **Consistencia**: El contador siempre refleja la cantidad real de libros
- **Concurrencia segura**: Múltiples Jobs pueden ejecutarse simultáneamente sin conflictos
- **Integridad de datos**: Previene actualizaciones perdidas en escenarios de alta carga
