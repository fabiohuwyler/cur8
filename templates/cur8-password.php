<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cur8 - Access Required - <?php bloginfo('name'); ?></title>
    <?php wp_head(); ?>
    <style>
        body.cur8-password-page {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .cur8-password-container {
            max-width: 450px;
            width: 90%;
            background: white;
            border-radius: 20px;
            padding: 48px;
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.3);
            animation: fadeInUp 0.6s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .cur8-password-header {
            text-align: center;
            margin-bottom: 32px;
        }
        
        .cur8-password-header h1 {
            font-size: 2.5em;
            margin: 0 0 12px 0;
            color: #2c3e50;
            font-weight: 800;
        }
        
        .cur8-password-header p {
            color: #666;
            font-size: 16px;
            margin: 0;
        }
        
        .cur8-password-form {
            margin-top: 32px;
        }
        
        .cur8-form-group {
            margin-bottom: 24px;
        }
        
        .cur8-form-group label {
            display: block;
            margin-bottom: 10px;
            font-weight: 700;
            color: #2c3e50;
            font-size: 15px;
        }
        
        .cur8-form-group input[type="password"] {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid #e5e5e5;
            border-radius: 12px;
            font-size: 16px;
            font-family: inherit;
            transition: all 0.3s;
            box-sizing: border-box;
            background: #fafafa;
        }
        
        .cur8-form-group input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }
        
        .cur8-submit-btn {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 18px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .cur8-submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(102, 126, 234, 0.5);
        }
        
        .cur8-error {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
            border: 2px solid #dc3545;
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .cur8-error::before {
            content: '✕';
            font-size: 24px;
        }
        
        .cur8-back-link {
            display: block;
            text-align: center;
            margin-top: 24px;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }
        
        .cur8-back-link:hover {
            color: #764ba2;
        }
    </style>
</head>
<body class="cur8-password-page">
    <div class="cur8-password-container">
        <div class="cur8-password-header">
            <h1>🔒 Cur8</h1>
            <p>Password required to access</p>
        </div>
        
        <?php if (isset($_POST['cur8_password']) && !empty($_POST['cur8_password'])): ?>
            <div class="cur8-error">
                Incorrect password. Please try again.
            </div>
        <?php endif; ?>
        
        <form method="post" class="cur8-password-form">
            <div class="cur8-form-group">
                <label for="cur8_password">Enter Password</label>
                <input type="password" name="cur8_password" id="cur8_password" required autofocus>
            </div>
            
            <button type="submit" class="cur8-submit-btn">Access Cur8</button>
        </form>
        
        <a href="<?php echo home_url(); ?>" class="cur8-back-link">← Back to Site</a>
    </div>
    
    <?php wp_footer(); ?>
</body>
</html>
