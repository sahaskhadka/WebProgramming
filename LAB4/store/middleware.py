import time

# function based middleware
# in store/middleware.py

def RequestLoggingMiddleware(get_response):
    def middleware(request):
      
          # 1. Log the request info (Demonstrates Middleware Interception)
        print(f"--- Request Logging ---")
        print(f"Path: {request.path}")
        print(f"Method: {request.method}")
        print(f"User: {request.user}")
        print(f"-----------------------")
        
        # 2. Process request & get response
        start_time = time.time()
        response = get_response(request)
        duration = time.time() - start_time
        
        # 3. Log the response info
        print(f"--- Response Logging ---")
        print(f"Response Status: {response.status_code}")
        print(f"Duration: {duration:.4f}s")
        print(f"-----------------------")
        
        return response

    return middleware

# class based middleware
'''
class RequestLoggingMiddleware:
    def __init__(self, get_response):
        self.get_response = get_response

    def __call__(self, request):
        # 1. Capture request info (Demonstrates Middleware Interception)
        print(f"--- Request Logging ---")
        print(f"Path: {request.path}")
        print(f"Method: {request.method}")
        print(f"User: {request.user}")
        print(f"-----------------------")
        
        # 2. Process request & get response
        start_time = time.time()
        response = self.get_response(request)
        duration = time.time() - start_time
        
        # 3. Capture response info
        print(f"--- Response Logging ---")
        print(f"Response Status: {response.status_code}")
        print(f"Duration: {duration:.4f}s")
        print(f"-----------------------")
        
        return response

        '''
