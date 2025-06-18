<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require 'db.php'; // Ensure $pdo is defined before use
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management Dashboard</title>
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
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            color: white;
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .card {
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

        .card-header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            padding: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title {
            font-size: 1.8rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .add-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
            padding: 12px 24px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .add-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.5);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        .table-container {
            overflow-x: auto;
        }

        .users-table {
            width: 100%;
            border-collapse: collapse;
        }

        .users-table th {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            color: #334155;
            padding: 20px;
            text-align: left;
            font-weight: 600;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e2e8f0;
        }

        .users-table td {
            padding: 20px;
            border-bottom: 1px solid #f1f5f9;
            transition: all 0.3s ease;
        }

        .users-table tr:hover {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            transform: scale(1.01);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .user-id {
            font-weight: 700;
            color: #4f46e5;
            font-size: 1.1rem;
        }

        .user-name {
            font-weight: 600;
            color: #1e293b;
            font-size: 1.05rem;
        }

        .user-email {
            color: #64748b;
            font-style: italic;
        }

        .actions {
            display: flex;
            gap: 12px;
        }

        .action-btn {
            padding: 8px 16px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 6px;
            min-width: 80px;
            justify-content: center;
        }

        .edit-btn {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
        }

        .edit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(16, 185, 129, 0.4);
        }

        .delete-btn {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            border: none;
        }

        .delete-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(239, 68, 68, 0.4);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #64748b;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: #334155;
        }

        .status-indicator {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
            padding: 15px 25px;
            border-radius: 10px;
            margin-bottom: 30px;
            font-weight: 500;
        }

        .floating-stats {
            position: fixed;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            min-width: 200px;
            z-index: 1000;
        }

        .stat-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .stat-label {
            color: #64748b;
            font-size: 0.9rem;
        }

        .stat-value {
            font-weight: 700;
            color: #4f46e5;
            font-size: 1.2rem;
        }

        .error-message {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
            padding: 15px 25px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        @media (max-width: 768px) {
            .card-header {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }

            .floating-stats {
                position: static;
                margin-bottom: 20px;
            }

            .actions {
                flex-direction: column;
            }

            .header h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-users"></i> User Management Dashboard</h1>
            <p>Manage your users with style and efficiency</p>
        </div>

        <?php
        $userCount = 0;
        ob_start(); // Start output buffering to capture table content
        ?>

        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <i class="fas fa-table"></i>
                    All Users
                </div>
                <a href="create.php" class="add-btn">
                    <i class="fas fa-plus"></i>
                    Add New User
                </a>
            </div>

            <div class="table-container">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-hashtag"></i> ID</th>
                            <th><i class="fas fa-user"></i> Name</th>
                            <th><i class="fas fa-envelope"></i> Email</th>
                            <th><i class="fas fa-cogs"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        try {
                            $stmt = $pdo->query("SELECT * FROM users");
                            $hasUsers = false;
                            
                            while ($row = $stmt->fetch()) {
                                $hasUsers = true;
                                $userCount++;
                                echo "<tr>
                                    <td class='user-id'>{$row['id']}</td>
                                    <td class='user-name'>" . htmlspecialchars($row['name']) . "</td>
                                    <td class='user-email'>" . htmlspecialchars($row['email']) . "</td>
                                    <td>
                                        <div class='actions'>
                                            <a href='update.php?id={$row['id']}' class='action-btn edit-btn'>
                                                <i class='fas fa-edit'></i> Edit
                                            </a>
                                            <a href='delete.php?id={$row['id']}' class='action-btn delete-btn'>
                                                <i class='fas fa-trash'></i> Delete
                                            </a>
                                        </div>
                                    </td>
                                </tr>";
                            }
                            
                            if (!$hasUsers) {
                                echo "<tr><td colspan='4'>
                                    <div class='empty-state'>
                                        <i class='fas fa-users-slash'></i>
                                        <h3>No users found</h3>
                                        <p>Get started by adding your first user</p>
                                    </div>
                                </td></tr>";
                            }
                        } catch (PDOException $e) {
                            echo "<tr><td colspan='4'>
                                <div class='error-message'>
                                    <i class='fas fa-exclamation-triangle'></i>
                                    Error: " . htmlspecialchars($e->getMessage()) . "
                                </div>
                            </td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="floating-stats">
            <div class="stat-item">
                <span class="stat-label">Total Users</span>
                <span class="stat-value"><?php echo $userCount; ?></span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Active</span>
                <span class="stat-value"><?php echo $userCount; ?></span>
            </div>
        </div>
    </div>

    <script>
        // Add interactive animations
        document.addEventListener('DOMContentLoaded', function() {
            // Add click animations to buttons
            const buttons = document.querySelectorAll('.action-btn, .add-btn');
            buttons.forEach(button => {
                button.addEventListener('click', function(e) {
                    // Create ripple effect
                    const ripple = document.createElement('span');
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;
                    
                    ripple.style.cssText = `
                        position: absolute;
                        width: ${size}px;
                        height: ${size}px;
                        left: ${x}px;
                        top: ${y}px;
                        background: rgba(255,255,255,0.5);
                        border-radius: 50%;
                        transform: scale(0);
                        animation: ripple 0.6s ease-out;
                        pointer-events: none;
                    `;
                    
                    this.style.position = 'relative';
                    this.style.overflow = 'hidden';
                    this.appendChild(ripple);
                    
                    setTimeout(() => ripple.remove(), 600);
                });
            });
            
            // Add smooth scrolling for better UX
            document.documentElement.style.scrollBehavior = 'smooth';
        });

        // Add ripple animation keyframes
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(2);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
