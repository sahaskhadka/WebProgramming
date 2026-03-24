# SmartShop Django E-Commerce App
This application demonstrates core Django concepts.


## 🚀 How to Run

1. **Create a Superuser** (to add products):
   ```bash
   python manage.py createsuperuser
   ```
   Follow the prompts to set a username and password.

2. **Start the Server**:
   ```bash
   python manage.py runserver
   ```

3. **Access the App**:
   - Store: [http://127.0.0.1:8000/](http://127.0.0.1:8000/)
   - Admin (for adding products): [http://127.0.0.1:8000/admin/](http://127.0.0.1:8000/admin/)

## 🎓 Concepts Illustrated

### 1. Handling Requests & Responses
Every view in [store/views.py] demonstrate this. 
For example, [home](store/views.py#8-11) takes a `request` and returns a rendered HTML `response`.

### 2. Form Data Handling & Sessions
- **Forms**: Used in [register](store/views.py#57-69) and [login](store/views.py#70-84) views via Django's built-in forms (UserCreationForm,AuthenticationForm).
- **Sessions**: The Shopping Cart in [add_to_cart](store/views.py#17-28) and [view_cart](store/views.py#29-49) uses `request.session` to store items without requiring a database for temporary data.

### 3. Routing & Middleware
- **Routing**: Defined in [smartshop/urls.py](smartshop/urls.py) and [store/urls.py](store/urls.py).
- **Middleware**: [RequestLoggingMiddleware](store/middleware.py#3-24) in [store/middleware.py](store/middleware.py) intercepts every request to log its path and response status to the terminal.

### 4. Database (Relational & ORM)
- Uses **SQLite** (Relational) by default.
- **ORM**: The [Product](store/models.py#3-10) model in [store/models.py](store/models.py) maps to a database table. CRUD is demonstrated in [product_list](store/views.py#12-16) (Read) and via the Admin panel (Create/Update/Delete).

### 5. Authentication & Authorization
- **Authentication**: [login_user](store/views.py#70-84) and [logout_user](store/views.py#85-89) handle user sessions. Authentication is handled automatically by Django's `UserCreationForm` and `AuthenticationForm`. Also by authentication middleware `AUTH_PASSWORD_VALIDATORS`.
- **Authorization**: The `@login_required` decorator on the [checkout](store/views.py#50-56) view ensures only logged-in users can access it.

### 6. Cookies & Sessions
- In [login_user](store/views.py#70-84), we demonstrate setting a cookie: `response.set_cookie('last_login_user', user.username)`.
- Sessions are used extensively for the shopping cart.

### 7. Security
- **CSRF Protection**: Every POST form includes `{% csrf_token %}`.
- **Password Hashing**: Handled automatically by Django's `UserCreationForm`.
- **Middleware Security**: Built-in Django middleware (like `Security`, `Session`, `CSRF`, `Auth`, `Message`, and `X-Frame`) automatically handles core security and session management.

### 8. Error Handling
- **Graceful Failure**: In `store/views.py`, we use `get_object_or_404(Product, id=product_id)`. This ensures that if an invalid ID is requested, the user sees a "Page Not Found" error instead of a server crash.
