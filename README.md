# PHP_Laravel12_Has_Many_Through_Relationship

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel">
  <img src="https://img.shields.io/badge/PHP-8%2B-777BB4?style=for-the-badge&logo=php">
  <img src="https://img.shields.io/badge/Eloquent-ORM-blue?style=for-the-badge">
  <img src="https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql">
</p>

---

##  Overview

This project explains **Has Many Through Relationship in Laravel 12**
using a **complete working example**.

### Relationship Structure

```
Country → Users → Posts
```

- One **Country** has many **Users**
- One **User** has many **Posts**
- Therefore:

```
Country has many Posts through Users
```

Laravel relationship used:

```php
hasManyThrough()
```

This project is useful for:
- Laravel beginners & intermediates
- Understanding advanced Eloquent relationships
- Interview preparation
- Real database relationship concepts


---

##  Features

- Laravel 12 framework
- Demonstrates **Has Many Through** Eloquent relationship
- Real-world example (Country → Users → Posts)
- Proper database migration order
- Clean MVC architecture
- Simple controller-based data fetch
- MySQL database support
- Beginner & interview friendly example

---

##  Folder Structure

```text
has-many-through/
│
├── app/
│   ├── Models/
│   │   ├── Country.php
│   │   ├── User.php
│   │   └── Post.php
│   └── Http/
│       └── Controllers/
│           └── UserController.php
│
├── database/
│   └── migrations/
│       ├── xxxx_create_countries_table.php
│       ├── xxxx_create_users_table.php
│       └── xxxx_create_posts_table.php
│
├── routes/
│   └── web.php
│
├── .env
├── artisan
└── README.md
```

---

##  STEP 1: New Laravel Project Installation

###  Create Laravel Project

```bash
composer create-project laravel/laravel has-many-through
```

###  Start Laravel Server

```bash
php artisan serve
```

Open browser:

```
http://127.0.0.1:8000
```

---

##  STEP 2: Database Configuration

###  Update `.env` File

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=has_many
DB_USERNAME=root
DB_PASSWORD=
```

###  Create Database

```sql
CREATE DATABASE has_many;
```

---

##  STEP 3: Create Migrations (VERY IMPORTANT ORDER)

Migration order **must be**:

```
countries → users → posts
```

---

###  Countries Migration

```bash
php artisan make:migration create_countries_table
```

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
```

---

###  Users Migration

```bash
php artisan make:migration create_users_table
```

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->foreignId('country_id')->constrained('countries');
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
```

---

###  Posts Migration

```bash
php artisan make:migration create_posts_table
```

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
```

---

### Run All Migrations

```bash
php artisan migrate
```

---

##  STEP 4: Create Models

---

###  Country Model

```bash
php artisan make:model Country
```

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Country extends Model
{
    
    public function posts(): HasManyThrough
    {
        return $this->hasManyThrough(
            Post::class,
            User::class,
            'country_id', 
            'user_id',    
            'id',        
            'id'         
        );
    }
}
```

---

###  User Model

```bash
php artisan make:model User
```

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = [
        'name',
        'email',
        'password',
        'country_id'
    ];
}
```

---

###  Post Model

```bash
php artisan make:model Post
```

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'name',
        'user_id'
    ];
}
```

---

##  STEP 5: Create Controller

```bash
php artisan make:controller UserController
```

```php
<?php

namespace App\Http\Controllers;

use App\Models\Country;

class UserController extends Controller
{
    public function index()
    {
        // Find country with ID = 1
        $country = Country::find(1);

        dd($country->posts);
    }
}
```

---

##  STEP 6: Routes

`routes/web.php`

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/country-posts', [UserController::class, 'index']);
```

---

##  STEP 7: Insert Sample Data (MySQL)

### Insert Countries

```sql
INSERT INTO countries (name, created_at, updated_at) 
VALUES 
('India', NOW(), NOW()),
('USA', NOW(), NOW()),
('Canada', NOW(), NOW());
```

---

### Insert Users

⚠️ bcrypt() **does not work directly in MySQL**, this is only for demo.

```sql
INSERT INTO users (name, email, password, country_id, created_at, updated_at) 
VALUES 
('Harry', 'harry@test.com', '123456', 1, NOW(), NOW()),
('John', 'john@test.com', '123456', 2, NOW(), NOW()),
('Alex', 'alex@test.com', '123456', 3, NOW(), NOW());
```

---

### Insert Posts

```sql
INSERT INTO posts (name, user_id, created_at, updated_at) 
VALUES 
('India Post 1', 1, NOW(), NOW()),
('USA Post 1', 2, NOW(), NOW()),
('Canada Post 1', 3, NOW(), NOW());
```

---

##  STEP 8: Final Output Test

Open in browser:

```
http://127.0.0.1:8000/country-posts
```

### Expected Output

<img width="754" height="782" alt="Screenshot 2025-12-25 170813" src="https://github.com/user-attachments/assets/0bb3c070-049c-466f-8492-081657695ef4" />

