<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create an Account</title>
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
            <h2>Create an account</h2>
            <p>Already have an account? <a href="login.php">Log in</a></p>
        </div>
        
        <form id="signup-form">
            <div class="form-group">
                <label class="form-label">Enter your email</label>
                <input type="email" placeholder="Email" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Create password</label>
                <div class="input-wrapper">
                    <input type="password" id="password" placeholder="Password" required>
                    <span class="password-toggle" onclick="togglePassword('password')">Hide</span>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Confirm password</label>
                <div class="input-wrapper">
                    <input type="password" id="confirm-password" placeholder="Password" required>
                    <span class="password-toggle" onclick="togglePassword('confirm-password')">Hide</span>
                </div>
            </div>
            
            <button type="submit" class="submit-btn">Create an account</button>
        </form>
        
        <div class="login-link">
            Already have an account? <a href="#">Log in</a>
        </div>
    </div>

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const toggle = input.parentElement.querySelector('.password-toggle');
            
            if (input.type === 'password') {
                input.type = 'text';;
                toggle.textContent = 'Show';
            } else {
                input.type = 'password';
                toggle.textContent = 'Hide';
            }
        }

        document.getElementById('signup-form').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Success.');
        });
    </script>
</body>
</html>

