from django.shortcuts import render, redirect, get_object_or_404
from django.contrib.auth import login, authenticate, logout
from django.contrib.auth.forms import UserCreationForm, AuthenticationForm
from django.contrib.auth.decorators import login_required
from .models import Product
from django.contrib import messages

def home(request):
    """Simple Home View - Request & Response Handling"""
    return render(request, 'store/home.html')

def product_list(request):
    """Product Listing - Database Integration (Read)"""
    products = Product.objects.all()


    cart = request.session.get('cart')

    if not cart:
        cart = {}

    #cart = request.session.get('cart', {}) # This is a more concise way to do the same thing as the code above
    
    # Calculate total items in cart
    if cart:
        total_quantity = sum(cart.values())
    else:
        total_quantity = 0

    return render(request, 'store/products.html', {
        'products': products,
        'total_quantity': total_quantity
    })

def add_to_cart(request, product_id):

    product = get_object_or_404(Product, id=product_id)


    cart = request.session.get('cart')

    if not cart:
        cart = {}

    #cart = request.session.get('cart', {}) # This is a more concise way to do the same thing as the code above
    
    product_id = str(product_id)  # Session Stores Data as JSON, and JSON does not support integer keys properly



    if product_id in cart:
        cart[product_id] += 1
    else:
        cart[product_id] = 1

    request.session['cart'] = cart
    request.session.modified = True   # Ensures session saves

    messages.success(request, f"{product.name} added to cart successfully!")

    return redirect('products')

def view_cart(request):
    """View Cart - Sessions & Cookies"""
    cart = request.session.get('cart', {})
    cart_items = []
    total_price = 0
    
    for product_id, quantity in cart.items():
        product = get_object_or_404(Product, id=int(product_id))
        item_total = product.price * quantity
        total_price += item_total
        cart_items.append({
            'product': product,
            'quantity': quantity,
            'total': item_total
        })
    
    return render(request, 'store/cart.html', {
        'cart_items': cart_items, 
        'total_price': total_price
    })

@login_required
def checkout(request):
    """Checkout - Authorization & Cookies/Sessions"""
    # Clear cart after checkout (simple demonstration)
    request.session['cart'] = {}
    return render(request, 'store/checkout.html')

def register_user(request):
    """Register - Authentication (Form Data Handling)"""
    if request.method == 'POST':
        form = UserCreationForm(request.POST)
        if form.is_valid():
            user = form.save()
            login(request, user)
            messages.success(request, "Registration successful!")
            return redirect('home')
    else:
        form = UserCreationForm()
    return render(request, 'store/register.html', {'form': form})

def login_user(request):
    """Login - Authentication & Sessions"""
    if request.method == 'POST':
        form = AuthenticationForm(data=request.POST)
        if form.is_valid():
            user = form.get_user()
            login(request, user)
            # Demonstrate Cookies
            response = redirect('home')
            response.set_cookie('last_login_user', user.username)
            return response
    else:
        form = AuthenticationForm()
    return render(request, 'store/login.html', {'form': form})

def logout_user(request):
    """Logout - Sessions"""
    logout(request)
    return redirect('home')


  
