<?php

session_start();

// Mock Database Connection (File-based SQLite)
$dbFile = 'database.sqlite';
try {
    $db = new PDO("sqlite:$dbFile");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create table if it doesn't exist
    $db->exec("CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY, username TEXT, bio TEXT)");
    
    // Check if empty and seed
    $count = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
    if ($count == 0) {
        $db->exec("INSERT INTO users (username, bio) VALUES ('admin', 'System Administrator')");
    }
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Generate CSRF Token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}


$message = "";

// --- 1. SQL INJECTION (SQLi) ---
// Vulnerable: Directly concatenating user input into query
if (isset($_GET['search_vulnerable'])) {
    $username = $_GET['search_vulnerable'];
    $query = "SELECT * FROM users WHERE username = '$username'";
    // Try: ' OR '1'='1
    try {
        $result = $db->query($query);
        $foundUser = $result->fetch(PDO::FETCH_ASSOC);
        $sqli_result = $foundUser ? "Found: " . $foundUser['username'] : "Not found.";
    } catch (Exception $e) {
        $sqli_result = "Error: " . $e->getMessage();
    }
}

// Secure: Using Prepared Statements
if (isset($_GET['search_secure'])) {
    $username = $_GET['search_secure'];
    $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $foundUser = $stmt->fetch(PDO::FETCH_ASSOC);
    $sqli_result_secure = $foundUser ? "Found: " . $foundUser['username'] : "Not found.";
}


// --- 2. CROSS-SITE SCRIPTING (XSS) ---
// Vulnerable: Just echoes whatever you type
$xss_vulnerable = $_GET['xss_vulnerable'] ?? "";

// Secure: Escapes the input before echoing
$xss_secure_input = $_GET['xss_secure'] ?? "";
$xss_secure_output = htmlspecialchars($xss_secure_input, ENT_QUOTES, 'UTF-8');


// --- 3. CROSS-SITE REQUEST FORGERY (CSRF) ---
// Vulnerable: Processing action without token validation
if (isset($_POST['action_vulnerable'])) {
    $message = "⚠️ ALLOWED! (No key was checked. This is where an attack happens!)";
}

// Secure: Validating CSRF Token
if (isset($_POST['action_secure'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $message = "🛑 REJECTED! (Security Key is missing or wrong!)";
    } else {
        $message = "✅ SUCCESS! (Security Key verified. Action safe.)";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Common Vulnerabilities Lab </title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; max-width: 800px; margin: 20px auto; padding: 20px; background: #f4f4f4; }
        .card { background: #fff; padding: 20px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        h2 { color: #333; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        .vulnerable { color: #d9534f; font-weight: bold; }
        .secure { color: #5cb85c; font-weight: bold; }
        code { background: #eee; padding: 2px 5px; border-radius: 4px; }
        pre { background: #2d2d2d; color: #ccc; padding: 15px; border-radius: 5px; overflow-x: auto; }
        .result { margin-top: 10px; padding: 10px; background: #e9ecef; border-left: 5px solid #ccc; }
    </style>
</head>
<body>

    <h1>Common Vulnerabilities Demonstrations</h1>
    <p>This page demonstrates common vulnerabilities and how to fix them.</p>

    <!-- 1. SQL Injection -->
    <div class="card">
    <h2>1. SQL Injection</h2>
    <p>SQL Injection allows an attacker to manipulate database queries by injecting malicious SQL.</p>

    <!-- Vulnerable Section -->
    <div style="border: 2px solid #feb2b2; background: #fff5f5; padding: 15px; margin-bottom: 20px; border-radius: 8px;">
        <h4 style="margin-top: 0; color: #c53030;">❌ Vulnerable</h4>
        <p>Try: <code>' OR '1'='1</code></p>

        <form method="GET">
            <input type="text" name="search_vulnerable" placeholder="Enter username...">
            <button type="submit">Search</button>
        </form>

        <?php if(isset($sqli_result)): ?>
            <div class="result">
                <strong>Result:</strong>
                <div><?php echo htmlspecialchars($sqli_result); ?></div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Secure Section -->
    <div style="border: 2px solid #9ae6b4; background: #f0fff4; padding: 15px; border-radius: 8px;">
        <h4 style="margin-top: 0; color: #276749;">✅ Secure</h4>
        <p>This version uses <code>Prepared Statements</code>.</p>
        <p>Try: <code>' OR '1'='1</code></p>

        <form method="GET">
            <input type="text" name="search_secure" placeholder="Enter username...">
            <button type="submit">Search</button>
        </form>

        <?php if(isset($sqli_result_secure)): ?>
            <div class="result">
                <strong>Result:</strong>
                <div><?php echo htmlspecialchars($sqli_result_secure); ?></div>
            </div>
        <?php endif; ?>
    </div>

    <p style="margin-top: 20px;">
        <strong>Best Practice:</strong> Always use <code>Prepared Statements</code> and never concatenate user input directly into SQL queries.
    </p>
</div>

    <!-- 2. Cross-Site Scripting (XSS) -->
    <div class="card">
        <h2>2. Cross-Site Scripting (XSS)</h2>
        <p>XSS occurs when an application includes untrusted data in a web page without proper validation or escaping.</p>
        
        <!-- Section 1: Vulnerable -->
        <div style="border: 2px solid #feb2b2; background: #fff5f5; padding: 15px; margin-bottom: 20px; border-radius: 8px;">
            <h4 style="margin-top: 0; color: #c53030;">❌ Vulnerable</h4>
            <p>Try: <code>&lt;script&gt;alert('Hacked!')&lt;/script&gt;</code></p>
            <form method="GET">
                <input type="text" name="xss_vulnerable" placeholder="Type script here...">
                <button type="submit">Print Directly</button>
            </form>
            <?php if($xss_vulnerable): ?>
                <div class="result">
                    <strong>Browser Result:</strong> 
                    <div><?php echo $xss_vulnerable; ?></div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Section 2: Secure -->
        <div style="border: 2px solid #9ae6b4; background: #f0fff4; padding: 15px; border-radius: 8px;">
            <h4 style="margin-top: 0; color: #276749;">✅ Secure</h4>
            <p>Try: <code>&lt;script&gt;alert('Hacked!')&lt;/script&gt;</code></p>
            <p>The code uses <code>htmlspecialchars()</code> to make scripts harmless.</p>
            <form method="GET">
                <input type="text" name="xss_secure" placeholder="Type script here...">
                <button type="submit">Print Safely</button>
            </form>
            <?php if($xss_secure_input): ?>
                <div class="result">
                    <strong>Browser Result:</strong> 
                    <div><?php echo $xss_secure_output; ?></div>
                </div>
            <?php endif; ?>
        </div>

        <p style="margin-top: 20px;"><strong>Best Practice:</strong> Always escape data on output using <code>htmlspecialchars()</code>.</p>
    </div>
    <!-- 3. Cross-Site Request Forgery (CSRF) -->
    <div class="card">
        <h2>3. Cross-Site Request Forgery (CSRF)</h2>
        
        <?php if($message): ?>
            <div style="margin-bottom: 20px; padding: 10px; background: #fff3cd; border: 1px solid #ffeeba; border-radius: 4px; text-align: center;">
                <strong>Status:</strong> <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Section 1: No Key -->
        <div style="border: 2px solid #feb2b2; background: #fff5f5; padding: 15px; margin-bottom: 20px; border-radius: 8px;">
            
            <h4 style="margin-top: 0; color: #c53030;">❌ Vulnerable: No Key Check</h4>
            <p>The server doesn't ask for a key. Anyone can trigger this action!</p>
            <form method="POST">
                <button type="submit" name="action_vulnerable" style="padding: 10px 20px; background: #f56565; color: white; border: none; cursor: pointer; border-radius: 4px;">
                    Delete Photos (No Key)
                </button>
            </form>
        </div>

        <!-- Section 2: Requires Key -->
        <div style="border: 2px solid #9ae6b4; background: #f0fff4; padding: 15px; border-radius: 8px;">
            <h4 style="margin-top: 0; color: #276749;">✅ Secure: Requires Key</h4>
            <p>The server checks for a unique key. Only this page can trigger it!</p>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <button type="submit" name="action_secure" style="padding: 10px 20px; background: #48bb78; color: white; border: none; cursor: pointer; border-radius: 4px;">
                    Delete Photos (Requires Key)
                </button>
            </form>
            
        </div>

        <!-- Section 3: Attack Simulation -->
    <div style="border: 2px solid #fbd38d; background: #fffaf0; padding: 15px; margin-top:20px; border-radius: 8px;">
    <h4 style="margin-top: 0; color: #c05621;">⚠️ Attack Simulation</h4>
    <p>This simulates a malicious website sending a request with a <strong>fake key</strong>.</p>

    <form method="POST">
        <!-- attacker does not know the real token -->
        <input type="hidden" name="csrf_token" value="fake_token_123">
        
        <button type="submit" name="action_secure"
            style="padding: 10px 20px; background: #ed8936; color: white; border: none; cursor: pointer; border-radius: 4px;">
            Attack Request (Fake Token)
        </button>
    </form>

    <p style="margin-top:10px;font-size:14px;color:#555;">
        Since the key does not match the server session, the request will be rejected.
    </p>
</div>

        <p style="margin-top: 20px;"><strong>The "Key" is the CSRF Token.</strong> This secret prevents other websites from making requests on your behalf.</p>
    </div>
</body>
</html>

