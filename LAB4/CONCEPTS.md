# SmartShop: Django Concepts Implementation

This project implements all the concepts..

## 1. Handling Requests & Responses
- **Where**: `store/views.py`
- **How**: Every function (like `home`, `product_list`) takes a `request` object and returns a `render` or `redirect` response.
- **Illustration**: You can see it in `home(request)`, which returns the HTML for the landing page.

## 2. Form Data Handling & Sessions
- **Where**: `store/views.py` (specifically `add_to_cart` and `register_user`)
- **How**:
    - **Forms**: User registration and login use Django's form system (`UserCreationForm`, `AuthenticationForm`) to process user input safely.
    - **Sessions**: The shopping cart is stored in `request.session['cart']`. This allows the server to "remember" what a user added to their cart as they navigate between pages.

## 3. Routing, Middleware, and Templating
- **Routing**: Defined in `smartshop/urls.py` and `store/urls.py` using `path()`.
- **Middleware**: 
    - **Custom Middleware**: Found in `store/middleware.py`. The `RequestLoggingMiddleware` intercepts every single request to log the path and timing to your terminal.
    - **Built-in Middleware**: Found in `smartshop/settings.py`.
- **Templating**: Found in `store/templates/store/`. It uses **Template Inheritance** (`{% extends %}`), **Variables** (`{{ product.name }}`), and **Logic** (`{% if user.is_authenticated %}`).

## 4. Database Integration (Relational, CRUD, ORM)
- **Where**: `store/models.py` and `store/views.py`
- **How**:
    - **Relational**: The app uses SQLite (a relational database) by default.
    - **ORM**: Instead of writing SQL, we use `Product.objects.all()` or `Product.objects.get()` in `views.py`.
    - **CRUD**:
        - **Create**: Done via the `/admin` panel.
        - **Read**: Handled in the `product_list` view.
        - **Update/Delete**: Handled via the `/admin` panel.

## 5. Authentication & Authorization
- **Where**: `store/views.py`
- **How**:
    - **Authentication**: The `login_user` and `logout_user` views handle logging people in and out. Authentication is handled automatically by Django's `UserCreationForm` and `AuthenticationForm`. Also by authentication middleware `AUTH_PASSWORD_VALIDATORS`.
    - **Authorization**: The `@login_required` decorator on the `checkout` view ensures only logged-in users can reach that page.

## 6. Cookies & Sessions
- **Where**: `store/views.py`
- **How**:
    - **Sessions**: Used for the cart storage (`request.session`).
    - **Cookies**: In the `login_user` view, we explicitly set a cookie: `response.set_cookie('last_login_user', user.username)`.

## 7. Security
- **Where**: Every template and view.
- **How**:
    - **CSRF Protection**: Every form has `{% csrf_token %}` to prevent cross-site request forgery.
    - **Password Hashing**: Django automatically hashes passwords during registration.
    - **Logging/Security Middleware**: Custom middleware logs activity, while built-in middleware handles security headers.
    
## 8. Error Handling
- **Where**: `store/views.py`
- **How**: Used `get_object_or_404` when fetching products from the database.
- **Concept**: If a user tries to access a product that doesn't exist (e.g., by manual URL manipulation), Django will automatically return a clean 404 page instead of crashing the server with a `DoesNotExist` exception.

---
*You can verify the middleware logging by looking at the terminal where your server is running; you should see the **Custom Middleware** logging every click you make on the site!*
