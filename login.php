<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #4A90E2;
        }

    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>App Name / Logo</h1>
            <h2>Log in</h2>
            <p>Don't have an account? <a href="#">Sign up</a></p>
        </div>
        
        <form id="login-form">
            <div class="form-group">
                <label class="form-label">Your email</label>
                <input type="email" placeholder="Email" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Your password</label>
                <div class="input-wrapper">
                    <input type="password" id="password" placeholder="Password" required>
                    <span class="password-toggle" onclick="togglePassword('password')">Hide</span>
                </div>
                <div class="forgot-password">
                    <a href="#">Forget your password</a>
                </div>
            </div>
            
            <button type="submit" class="submit-btn">Log in</button>
        </form>
        
        <div class="signup-link">
            Don't have an account? <a href="register.php">Sign up</a>
        </div>
    </div>

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const toggle = input.parentElement.querySelector('.password-toggle');
            
            if (input.type === 'password') {
                input.type = 'text';
                toggle.textContent = 'Show';
            } else {
                input.type = 'password';
                toggle.textContent = 'Hide';
            }
        }
        
        // Form submission handler (for demonstration purposes)
        document.getElementById('login-form').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Login would happen here in a real application.');
        });
    </script>
</body>
</html>

