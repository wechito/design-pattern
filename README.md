# 🧱 Proyecto: Patrones de Diseño - Repository + Service Layer + DTO

## 📋 Descripción

Este proyecto implementa una arquitectura limpia utilizando tres patrones clave:

- **Repository Pattern**: Encapsula la lógica de acceso a datos.
- **Service Layer**: Contiene la lógica de negocio.
- **DTO (Data Transfer Object)**: Transporta datos entre capas de forma segura y eficiente.

Esta plantilla sirve como base para estructurar proyectos robustos, escalables y mantenibles.

---

## 📚 Tabla de Contenidos

- [Patrones de Diseño Implementados](#patrones-de-diseño-implementados)
- [Estructura del Código](#estructura-del-código)
- [Archivos Clave](#archivos-clave)
  - [Repository](#repository)
  - [Service Layer](#service-layer)
  - [DTOs](#dtos)
  - [Request Validation](#request-validation)
  - [Controller](#controller)
  - [Inyección de Dependencias](#inyección-de-dependencias)
- [Autor](#autor)

---

## 🧩 Patrones de Diseño Implementados

### 🔁 Repository Pattern
Abstrae la lógica de acceso a datos y permite que el resto de la aplicación no dependa directamente de Eloquent u otros ORMs.

### 🧠 Service Layer
Contiene la lógica de negocio, permitiendo que los controladores se mantengan delgados y deleguen las operaciones complejas.

### 📦 DTO (Data Transfer Object)
Transporta los datos entre capas evitando exponer directamente las entidades del modelo.

---

## 🧱 Estructura del Código

```txt
app/
├── DTOs/
│ ├── request/
│ │ └── TaskRequestDTO.php
│ └── response/
│ └── TaskResponseDTO.php
├── Http/
│ ├── Controllers/
│ │ └── TaskController.php
│ └── Requests/
│ ├── StoreTaskRequest.php
│ └── UpdateTaskRequest.php
├── Repositories/
│ └── TaskRepository.php
├── Services/
│ └── TaskService.php
├── Providers/
│ └── AppServiceProvider.php
└── Traits/
└── ApiResponseTrait.php
```

## 📄 Archivos Clave

### 📁 Repository

Este repositorio encapsula todas las operaciones CRUD de la entidad `Task`.

```php
<?php

namespace App\Repositories;

use App\Models\Task;

class TaskRepository {

    public function findAll() {
        return Task::all();
    }

    public function create(array $data) {
        return Task::create($data);
    }

    public function update(Task $task, array $data) {
        return $task->update($data);
    }

    public function delete(Task $task): void {
        $task->delete();
    }
}
```
✅ Explicación: Este repositorio centraliza la interacción con el modelo Task. Al separar estas operaciones, facilitamos el mantenimiento, el testing y la posibilidad de cambiar la fuente de datos en el futuro.

## 🧠 Service Layer

Aquí se define la lógica de negocio. Transforma las entidades usando DTOs antes de devolverlas.
```php
<?php

namespace App\Services;

use App\DTOs\response\TaskResponseDTO;
use App\Models\Task;
use App\Repositories\TaskRepository;

class TaskService {
    private TaskRepository $taskRepository;

    public function __construct(TaskRepository $taskRepository) {
        $this->taskRepository = $taskRepository;
    }

    public function findAll() {
        $tasks = $this->taskRepository->findAll();

        return $tasks->map(function (Task $task) {
            return TaskResponseDTO::fromModel($task);
        });
    }

    public function create(array $data) {
        $task = $this->taskRepository->create($data);

        return TaskResponseDTO::fromModel($task);
    }

    public function update(Task $task, array $data) {
        $this->taskRepository->update($task, $data);
        return TaskResponseDTO::fromModel($task);
    }

    public function delete(Task $task): void {
        $this->taskRepository->delete($task);
    }
}
```
✅ Explicación: El servicio se encarga de procesar los datos, aplicar reglas de negocio (si existieran) y retornar los datos como DTOs. Esto separa la lógica de negocio de la lógica de acceso a datos.

## 📦 DTOs
Los DTO permiten transportar datos entre capas sin acoplar directamente los modelos.

Request DTO:
```php
<?php

namespace App\DTOs\request;

use App\Models\Task;

class TaskRequestDTO {
    public string $title;
    public string $description;
    public string $status;

    public function __construct(string $title, string $description, string $status) {
        $this->title = $title;
        $this->description = $description;
        $this->status = $status;
    }

    public static function fromModel(Task $task): self {
        return new self($task->title, $task->description, $task->status);
    }
}
```

Response DTO:
```php
<?php

namespace App\DTOs\response;

use App\Models\Task;

class TaskResponseDTO {
    public string $id;
    public string $title;
    public string $description;
    public string $status;

    public function __construct(string $id, string $title, string $description, string $status) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->status = $status;
    }

    public static function fromModel(Task $task): self {
        return new self($task->id, $task->title, $task->description, $task->status);
    }
}
```
✅ Explicación: Los DTOs evitan exponer entidades completas al frontend o a otras capas, protegiendo así la estructura interna del modelo.

## 🧾 Request Validation
Estas clases validan las solicitudes HTTP de forma estructurada.

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest {

    public function rules()
    {
        return [
            'title' => 'required|string',
            'description' => 'required|string',
            'status' => 'required|string'
        ];
    }
}
```

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest {

    public function rules()
    {
        return [
            'title' => 'sometimes|string',
            'description' => 'sometimes|string',
            'status' => 'sometimes|string'
        ];
    }
}
```
✅ Explicación: Laravel valida automáticamente los campos definidos en estas clases antes de llegar al controlador, asegurando que los datos estén completos y correctos.


## 🎮 Controller
El controlador actúa como punto de entrada para las solicitudes HTTP y delega la lógica al servicio.
```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Services\TaskService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    use ApiResponseTrait;
    private TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function index(): JsonResponse {
        return $this->successResponse($this->taskService->findAll(), true, "Task obtenidas correctamente");
    }

    public function store(StoreTaskRequest $request): JsonResponse {
        $data = $this->taskService->create($request->all());
        return $this->successResponse($data, true, "Task creada correctamente", 201);
    }

    public function update(UpdateTaskRequest $request, Task $task): JsonResponse {
        $data = $this->taskService->update($task, $request->all());
        return $this->successResponse($data, true, "Task actualizada correctamente");
    }

    public function destroy(Task $task): JsonResponse {
        $this->taskService->delete($task);
        return $this->successResponse($task, true, "", 204);
    }
}
```
✅ Explicación: Cada método del controlador responde a una acción REST (GET, POST, PUT, DELETE) y devuelve una respuesta estandarizada usando el trait ApiResponseTrait.

## 📦 Inyección de Dependencias
En AppServiceProvider, registramos el repositorio para que Laravel pueda inyectarlo automáticamente cuando se requiera.
```php
<?php

namespace App\Providers;

use App\Repositories\TaskRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TaskRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
```
✅ Explicación: El método bind() le dice al Service Container de Laravel cómo resolver TaskRepository cuando se inyecta, facilitando el desacoplamiento y la testabilidad.

## ✍️ Autor
Edwin Isaac Avila Garcia – edwin.avilag1999@gmail.com