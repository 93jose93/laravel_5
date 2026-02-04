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

## Notas de Compatibilidad (PHP 7.1)

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

| Método | Endpoint | Body (JSON) | Header | Descripción |
| :--- | :--- | :--- | :--- | :--- |
| **POST** | `{{url}}/api/v1/auth/login` | `{"email": "test@example.com", "password": "password"}` | `Content-Type: application/json` | Iniciar sesión y obtener token. |
| **POST** | `{{url}}/api/v1/auth/me` | N/A | `Authorization: Bearer {{token}}` | Obtener usuario autenticado. |
| **POST** | `{{url}}/api/v1/auth/refresh` | N/A | `Authorization: Bearer {{token}}` | Refrescar token expirado. |
| **POST** | `{{url}}/api/v1/auth/logout` | N/A | `Authorization: Bearer {{token}}` | Cerrar sesión (invalidar token). |
| **GET** | `{{url}}/api/v1/authors` | N/A | `Authorization: Bearer {{token}}` | Listar todos los autores. |
| **POST** | `{{url}}/api/v1/authors` | `{"name": "Gabriel", "surname": "Marquez"}` | `Authorization: Bearer {{token}}` | Crear un nuevo autor. |
| **GET** | `{{url}}/api/v1/authors/{id}` | N/A | `Authorization: Bearer {{token}}` | Ver detalle de un autor. |
| **PUT** | `{{url}}/api/v1/authors/{id}` | `{"name": "Gabo", "surname": "Marquez"}` | `Authorization: Bearer {{token}}` | Actualizar un autor. |
| **DELETE** | `{{url}}/api/v1/authors/{id}` | N/A | `Authorization: Bearer {{token}}` | Eliminar un autor. |
| **GET** | `{{url}}/api/v1/books` | N/A | `Authorization: Bearer {{token}}` | Listar todos los libros. |
| **POST** | `{{url}}/api/v1/books` | `{"title": "Cien Años", "published_date": "1967-05-30", "author_id": 1, "description": "Obra maestra"}` | `Authorization: Bearer {{token}}` | Crear libro (Dispara Job Async). |
| **GET** | `{{url}}/api/v1/books/{id}` | N/A | `Authorization: Bearer {{token}}` | Ver detalle de un libro. |
| **PUT** | `{{url}}/api/v1/books/{id}` | `{"title": "El coronel no tiene quien le escriba"}` | `Authorization: Bearer {{token}}` | Actualizar un libro. |
| **DELETE** | `{{url}}/api/v1/books/{id}` | N/A | `Authorization: Bearer {{token}}` | Eliminar un libro. |
| **GET** | `{{url}}/api/v1/export` | N/A | `Authorization: Bearer {{token}}` | Descargar Excel (Authors & Books). |

*Nota: Reemplaza `{{url}}` con tu host (ej: `http://localhost:8000`) y `{{token}}` con el token JWT obtenido en el login.*
