# Laravel Action Pattern Implementation

This document describes the Action pattern implementation for the user management system, following Laravel best practices.

## Overview

The Action pattern is a design pattern that encapsulates business logic into single-purpose classes. This approach provides better separation of concerns, testability, and maintainability.

## Structure

### Base Action Class
- **Location**: `app/Actions/Action.php`
- **Purpose**: Abstract base class that defines the contract for all actions
- **Method**: `execute(...$parameters)` - Abstract method that all actions must implement

### User Actions
All user-related actions are located in `app/Actions/User/`:

1. **CreateUserAction** (`app/Actions/User/CreateUserAction.php`)
   - **Purpose**: Handle user creation logic
   - **Parameters**: `array $data` (user data)
   - **Returns**: `User` model instance
   - **Validation**: Name, email, password validation

2. **UpdateUserAction** (`app/Actions/User/UpdateUserAction.php`)
   - **Purpose**: Handle user update logic
   - **Parameters**: `User $user`, `array $data`
   - **Returns**: Updated `User` model instance
   - **Validation**: Name, email, optional password validation

3. **DeleteUserAction** (`app/Actions/User/DeleteUserAction.php`)
   - **Purpose**: Handle user deletion logic
   - **Parameters**: `User $user`
   - **Returns**: `bool` (success status)
   - **Validation**: Prevents self-deletion

4. **GetUsersAction** (`app/Actions/User/GetUsersAction.php`)
   - **Purpose**: Handle user listing with search and pagination
   - **Parameters**: `array $filters` (optional search filters)
   - **Returns**: `LengthAwarePaginator`

## Benefits

### 1. **Single Responsibility Principle**
Each action class has one specific responsibility:
- `CreateUserAction` - Only handles user creation
- `UpdateUserAction` - Only handles user updates
- `DeleteUserAction` - Only handles user deletion
- `GetUsersAction` - Only handles user listing

### 2. **Testability**
Actions can be easily unit tested in isolation:
```php
public function test_create_user_action()
{
    $action = new CreateUserAction();
    $userData = ['name' => 'John', 'email' => 'john@example.com', 'password' => 'password'];
    
    $user = $action->execute([$userData]);
    
    $this->assertInstanceOf(User::class, $user);
    $this->assertEquals('John', $user->name);
}
```

### 3. **Reusability**
Actions can be reused across different parts of the application:
- Controllers
- Commands
- Jobs
- Other Actions

### 4. **Dependency Injection**
Actions can be easily injected into controllers and other classes:
```php
public function store(Request $request, CreateUserAction $createUserAction)
{
    $user = $createUserAction->execute([$request->all()]);
}
```

### 5. **Error Handling**
Centralized error handling with ValidationException:
```php
try {
    $createUserAction->execute([$request->all()]);
} catch (ValidationException $e) {
    return back()->withErrors($e->errors())->withInput();
}
```

## Controller Refactoring

The `UserController` has been refactored to use actions:

### Before (Traditional Approach)
```php
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
    ]);

    User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
    ]);

    return redirect()->route('users.index')
        ->with('success', 'User created successfully.');
}
```

### After (Action Pattern)
```php
public function store(Request $request, CreateUserAction $createUserAction)
{
    try {
        $createUserAction->execute([$request->all()]);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    } catch (ValidationException $e) {
        return back()->withErrors($e->errors())->withInput();
    }
}
```

## Usage Examples

### In Controllers
```php
class UserController extends Controller
{
    public function index(GetUsersAction $getUsersAction)
    {
        $users = $getUsersAction->execute([request()->only(['search'])]);
        return Inertia::render('users/Index', compact('users'));
    }

    public function store(Request $request, CreateUserAction $createUserAction)
    {
        $createUserAction->execute([$request->all()]);
        return redirect()->route('users.index');
    }
}
```

### In Commands
```php
class CreateUserCommand extends Command
{
    public function handle(CreateUserAction $createUserAction)
    {
        $userData = ['name' => 'Admin', 'email' => 'admin@example.com', 'password' => 'password'];
        $user = $createUserAction->execute([$userData]);
        
        $this->info("User {$user->name} created successfully.");
    }
}
```

### In Jobs
```php
class ProcessUserJob implements ShouldQueue
{
    public function handle(UpdateUserAction $updateUserAction)
    {
        $updateUserAction->execute([$this->user, $this->data]);
    }
}
```

## Best Practices

### 1. **Naming Convention**
- Action classes should end with "Action"
- Use descriptive names that indicate the purpose
- Follow PSR-4 autoloading standards

### 2. **Method Signature**
- Always use `execute(...$parameters)` method signature
- Extract parameters from the array in the method body
- Use type hints in method documentation

### 3. **Validation**
- Handle validation within the action
- Throw `ValidationException` for validation errors
- Let controllers handle the exception response

### 4. **Return Types**
- Use specific return types (User, bool, LengthAwarePaginator, etc.)
- Avoid returning mixed types
- Document return types in method comments

### 5. **Error Handling**
- Use try-catch blocks in controllers
- Let actions throw exceptions
- Handle exceptions appropriately in the calling code

## Future Enhancements

1. **Action Collections**: Group related actions together
2. **Action Events**: Fire events when actions are executed
3. **Action Logging**: Log action executions for audit trails
4. **Action Caching**: Cache action results when appropriate
5. **Action Queues**: Queue long-running actions

## Testing Actions

```php
class CreateUserActionTest extends TestCase
{
    public function test_creates_user_with_valid_data()
    {
        $action = new CreateUserAction();
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $user = $action->execute([$data]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
    }

    public function test_throws_validation_exception_with_invalid_data()
    {
        $this->expectException(ValidationException::class);

        $action = new CreateUserAction();
        $data = ['name' => '', 'email' => 'invalid-email'];

        $action->execute([$data]);
    }
}
```

This Action pattern implementation provides a clean, maintainable, and testable architecture for the user management system while following Laravel best practices. 