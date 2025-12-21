<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Dashboard')</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        .header { background: #333; color: white; padding: 15px; margin: -20px -20px 20px -20px; border-radius: 8px 8px 0 0; }
        .content { padding: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>@yield('page-title', 'Admin Dashboard')</h1>
            <p>@yield('page-description', 'Welcome to your admin dashboard')</p>
        </div>
        <div class="content">
            @yield('content')
        </div>
    </div>
</body>
</html>
