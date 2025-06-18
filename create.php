<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require 'db.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    
    // Basic validation
    if (empty($username) || empty($password) || empty($name) || empty($email)) {
        $message = 'All fields are required!';
        $messageType = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Please enter a valid email address!';
        $messageType = 'error';
    } else {
        try {
            // Check if username or email already exists
            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ? OR name = ?");
            $checkStmt->execute([$email, $username]);
            
            if ($checkStmt->fetchColumn() > 0) {
                $message = 'Username or email already exists!';
                $messageType = 'error';
            } else {
                // Insert new user
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                if ($stmt->execute([$name, $email, $hashedPassword])) {
                    $message = 'User created successfully!';
                    $messageType = 'success';
                    // Clear form data on success
                    $username = $password = $name = $email = '';
                } else {
                    $message = 'Failed to create user. Please try again.';
                    $messageType = 'error';
                }
            }
        } catch (PDOException $e) {
            $message = 'Database error: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New User - User Management Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            color: white;
        }

        .header h1 {
            font-size: 2.2rem;
            margin-bottom: 8px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .header p {
            font-size: 1rem;
            opacity: 0.9;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: white;
            text-decoration: none;
            font-size: 0.95rem;
            margin-bottom: 20px;
            padding: 8px 16px;
            border-radius: 25px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        }

        .form-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .form-header h2 {
            font-size: 1.6rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .form-body {
            padding: 40px;
        }

        .message {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .message.success {
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .message.error {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .input-wrapper {
            position: relative;
        }

        .form-control {
            width: 100%;
            padding: 15px 20px;
            padding-left: 50px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f9fafb;
        }

        .form-control:focus {
            outline: none;
            border-color: #4f46e5;
            background: white;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
            transform: translateY(-1px);
        }

        .input-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus + .input-icon {
            color: #4f46e5;
        }

        .submit-btn {
            width: 100%;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            border: none;
            padding: 16px 20px;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(79, 70, 229, 0.4);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .form-footer {
            text-align: center;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px solid #e5e7eb;
        }

        .form-footer a {
            color: #4f46e5;
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .form-footer a:hover {
            color: #3730a3;
            transform: translateX(-2px);
        }

        .password-strength {
            margin-top: 8px;
            font-size: 0.85rem;
        }

        .strength-indicator {
            height: 4px;
            background: #e5e7eb;
            border-radius: 2px;
            margin: 6px 0;
            overflow: hidden;
        }

        .strength-bar {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, #ef4444, #f59e0b, #10b981);
            transition: width 0.3s ease;
            border-radius: 2px;
        }

        .floating-elements {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            pointer-events: none;
            z-index: -1;
        }

        .floating-element {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            
            .header h1 {
                font-size: 1.8rem;
            }
            
            .form-body {
                padding: 30px 25px;
            }
        }

        /* Loading state */
        .loading {
            opacity: 0.7;
            pointer-events: none;
        }

        .loading .submit-btn {
            background: linear-gradient(135deg, #9ca3af 0%, #6b7280 100%);
        }
    </style>
</head>
<body>
    <div class="floating-elements">
        <div class="floating-element" style="width: 60px; height: 60px; top: 10%; left: 10%; animation-delay: 0s;"></div>
        <div class="floating-element" style="width: 40px; height: 40px; top: 70%; left: 80%; animation-delay: 2s;"></div>
        <div class="floating-element" style="width: 80px; height: 80px; top: 40%; left: 5%; animation-delay: 4s;"></div>
    </div>

    <div class="container">
        <a href="index.php" class="back-btn">
            <i class="fas fa-arrow-left"></i>
            Back to Dashboard
        </a>
        
        <div class="header">
            <h1><i class="fas fa-user-plus"></i> Add New User</h1>
            <p>Create a new user account</p>
        </div>

        <div class="form-card">
            <div class="form-header">
                <h2>
                    <i class="fas fa-user-circle"></i>
                    User Information
                </h2>
            </div>

            <div class="form-body">
                <?php if (!empty($message)): ?>
                    <div class="message <?php echo $messageType; ?>">
                        <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" id="createUserForm">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <div class="input-wrapper">
                            <input type="text" 
                                   id="username" 
                                   name="username" 
                                   class="form-control" 
                                   value="<?php echo htmlspecialchars($username ?? ''); ?>"
                                   required>
                            <i class="fas fa-user input-icon"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-wrapper">
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   class="form-control" 
                                   required>
                            <i class="fas fa-lock input-icon"></i>
                        </div>
                        <div class="password-strength">
                            <div class="strength-indicator">
                                <div class="strength-bar" id="strengthBar"></div>
                            </div>
                            <span id="strengthText">Enter a password</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <div class="input-wrapper">
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   class="form-control" 
                                   value="<?php echo htmlspecialchars($name ?? ''); ?>"
                                   required>
                            <i class="fas fa-id-card input-icon"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-wrapper">
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   class="form-control" 
                                   value="<?php echo htmlspecialchars($email ?? ''); ?>"
                                   required>
                            <i class="fas fa-envelope input-icon"></i>
                        </div>
                    </div>

                    <button type="submit" class="submit-btn">
                        <i class="fas fa-plus-circle"></i>
                        Create User
                    </button>
                </form>

                <div class="form-footer">
                    <a href="index.php">
                        <i class="fas fa-list"></i>
                        View All Users
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Password strength checker
        const passwordInput = document.getElementById('password');
        const strengthBar = document.getElementById('strengthBar');
        const strengthText = document.getElementById('strengthText');

        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strength = calculatePasswordStrength(password);
            
            strengthBar.style.width = strength.percentage + '%';
            strengthText.textContent = strength.text;
            strengthText.style.color = strength.color;
        });

        function calculatePasswordStrength(password) {
            let score = 0;
            let feedback = 'Weak';
            let color = '#ef4444';
            
            if (password.length >= 8) score += 25;
            if (password.match(/[a-z]/)) score += 25;
            if (password.match(/[A-Z]/)) score += 25;
            if (password.match(/[0-9]/)) score += 25;
            if (password.match(/[^a-zA-Z0-9]/)) score += 25;
            
            if (score >= 100) {
                feedback = 'Very Strong';
                color = '#10b981';
            } else if (score >= 75) {
                feedback = 'Strong';
                color = '#059669';
            } else if (score >= 50) {
                feedback = 'Medium';
                color = '#f59e0b';
            } else if (score >= 25) {
                feedback = 'Fair';
                color = '#f97316';
            }
            
            return {
                percentage: Math.min(score, 100),
                text: feedback,
                color: color
            };
        }

        // Form submission with loading state
        const form = document.getElementById('createUserForm');
        const submitBtn = document.querySelector('.submit-btn');

        form.addEventListener('submit', function() {
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating User...';
            form.classList.add('loading');
        });

        // Auto-hide success message
        const successMessage = document.querySelector('.message.success');
        if (successMessage) {
            setTimeout(() => {
                successMessage.style.opacity = '0';
                successMessage.style.transform = 'translateY(-10px)';
                setTimeout(() => successMessage.remove(), 300);
            }, 3000);
        }

        // Add input focus animations
        const inputs = document.querySelectorAll('.form-control');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('focused');
            });
        });
    </script>
</body>
</html>
