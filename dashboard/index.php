<?php
// Check MySQL service status
function checkMySQLStatus()
{
    try {
        $connection = new mysqli('127.0.0.1', 'root', 'root');
        if ($connection->connect_error) {
            return false;
        }
        $connection->close();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Check Redis service status
function checkRedisStatus()
{
    try {
        // Try to connect to Redis using fsockopen
        $connection = @fsockopen('127.0.0.1', 6379, $errno, $errstr, 1);
        if (!$connection) {
            return false;
        }
        fclose($connection);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

$mysqlStatus = checkMySQLStatus();
$redisStatus = checkRedisStatus();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🐳 Fairway Dev Container</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.5;
            color: #1a1a1a;
            background: #ffffff;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 32px 24px;
        }

        header {
            margin-bottom: 48px;
            text-align: center;
        }

        .header-title {
            font-size: 2.25rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: #1a1a1a;
        }

        .header-subtitle {
            font-size: 1.125rem;
            color: #6b7280;
            font-weight: 400;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(360px, 1fr));
            gap: 24px;
            margin-bottom: 48px;
        }

        .card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 24px;
        }

        .card h2 {
            color: #1a1a1a;
            margin-bottom: 20px;
            font-size: 1.25rem;
            font-weight: 600;
        }

        .card-header h2 {
            margin-bottom: 0;
        }

        .status {
            font-size: 0.75rem;
            font-weight: 500;
            padding: 2px 8px;
            border-radius: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status.running {
            background: #dcfce7;
            color: #166534;
        }

        .status.available {
            background: #f3f4f6;
            color: #374151;
        }

        .status.error {
            background: #fee2e2;
            color: #dc2626;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .refresh-btn {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 6px 8px;
            color: #374151;
            text-decoration: none;
            font-size: 0.875rem;
            transition: background 0.2s;
            cursor: pointer;
        }

        .refresh-btn:hover {
            background: #f3f4f6;
            color: #1a1a1a;
        }

        .service-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .service-item:last-child {
            border-bottom: none;
        }

        .service-name {
            font-weight: 500;
            color: #1a1a1a;
        }

        .service-info {
            font-size: 0.875rem;
            color: #6b7280;
            margin-top: 2px;
        }

        .extension-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 8px;
        }

        .extension-item {
            background: #f9fafb;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 0.875rem;
            text-align: center;
            font-weight: 500;
            color: #374151;
        }

        .port-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px;
        }

        .port-item {
            background: #f9fafb;
            padding: 12px;
            border-radius: 6px;
        }

        .port-number {
            font-weight: 600;
            color: #1a1a1a;
            font-family: monospace;
        }

        .directory-item {
            background: #f9fafb;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 8px;
        }

        .directory-path {
            font-family: monospace;
            font-weight: 600;
            color: #1a1a1a;
        }

        .directory-desc {
            font-size: 0.875rem;
            color: #6b7280;
            margin-top: 4px;
        }

        .directory-desc.nowrap {
            overflow-x: auto;
            white-space: nowrap;
        }

        .quick-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 48px;
        }

        .quick-link {
            border: 1px solid #ccc;
            color: #1a1a1a;
            padding: 16px;
            border-radius: 8px;
            text-decoration: none;
            text-align: center;
            font-weight: 500;
            transition: background 0.2s;
        }

        .quick-link:hover {
            background: #efefef;
        }

        .credentials {
            background: #fffbeb;
            border: 1px solid #fde68a;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 32px;
        }

        .credentials h3 {
            color: #92400e;
            margin-bottom: 12px;
            font-size: 1rem;
            font-weight: 600;
        }

        .cred-item {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
            font-family: monospace;
            font-size: 0.875rem;
        }

        .footer {
            text-align: center;
            padding: 24px 0;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            margin-top: 48px;
        }

        .version-badge {
            background: #e5e7eb;
            color: #374151;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.75rem;
            margin-left: 8px;
            font-weight: 500;
        }
    </style>
</head>

<body>
    <div class="container">
        <header>
            <div class="header-title">🐳 Fairway Dev Container</div>
            <div class="header-subtitle">Complete PHP & JavaScript Development Container - Perfect for learning, prototyping, and quick development</div>
        </header>

        <div class="credentials">
            <h3>🔓 Default Credentials</h3>
            <div class="cred-item">
                <span><strong>MySQL:</strong></span>
                <span>root / root</span>
            </div>
            <div class="cred-item">
                <span><strong>Redis:</strong></span>
                <span>root / root</span>
            </div>
        </div>

        <div class="quick-links">
            <a href="/phpmyadmin/" class="quick-link">
                🐬 phpMyAdmin
            </a>
            <a href="https://github.com/eimg/fairway-dev-container" class="quick-link" target="_blank" rel="noopener noreferrer">
                🐙 Source Code
            </a>
        </div>

        <div class="grid">
            <div class="card">
                <div class="card-header">
                    <h2>🟢 Core Services</h2>
                    <a href="/" class="refresh-btn">🔄 Refresh</a>
                </div>
                <div class="service-item">
                    <div>
                        <div class="service-name">Nginx</div>
                        <div class="service-info">Web Server</div>
                    </div>
                    <span class="status running">Running</span>
                </div>
                <div class="service-item">
                    <div>
                        <div class="service-name">MySQL <span class="version-badge">8.0</span></div>
                        <div class="service-info">Database Server</div>
                    </div>
                    <span class="status <?php echo $mysqlStatus ? 'running' : 'error'; ?>">
                        <?php echo $mysqlStatus ? 'Running' : 'Error'; ?>
                    </span>
                </div>
                <div class="service-item">
                    <div>
                        <div class="service-name">Redis <span class="version-badge">7.0</span></div>
                        <div class="service-info">In-Memory Cache</div>
                    </div>
                    <span class="status <?php echo $redisStatus ? 'running' : 'error'; ?>">
                        <?php echo $redisStatus ? 'Running' : 'Error'; ?>
                    </span>
                </div>
                <div class="service-item">
                    <div>
                        <div class="service-name">PHP <span class="version-badge">8.4</span></div>
                        <div class="service-info">FastCGI Process Manager</div>
                    </div>
                    <span class="status running">Running</span>
                </div>
                <div class="service-item">
                    <div>
                        <div class="service-name">Node <span class="version-badge">22</span></div>
                        <div class="service-info">JavaScript Runtime</div>
                    </div>
                    <span class="status available">Available</span>
                </div>
            </div>

            <div class="card">
                <h2>🛠️ Development Tools</h2>
                <div class="service-item">
                    <div>
                        <div class="service-name">Oh My Zsh</div>
                        <div class="service-info">Enhanced shell with dev indicator</div>
                    </div>
                    <span class="status available">Available</span>
                </div>
                <div class="service-item">
                    <div>
                        <div class="service-name">Git</div>
                        <div class="service-info">Version control system</div>
                    </div>
                    <span class="status available">Available</span>
                </div>
                <div class="service-item">
                    <div>
                        <div class="service-name">GitHub CLI</div>
                        <div class="service-info">Command-line interface for GitHub</div>
                    </div>
                    <span class="status available">Available</span>
                </div>
                <div class="service-item">
                    <div>
                        <div class="service-name">Build Tools</div>
                        <div class="service-info">GCC, Make, development headers</div>
                    </div>
                    <span class="status available">Available</span>
                </div>
                <div class="service-item">
                    <div>
                        <div class="service-name">Utilities</div>
                        <div class="service-info">curl, wget, nano, tree, zip</div>
                    </div>
                    <span class="status available">Available</span>
                </div>
            </div>

            <div class="card">
                <h2>📦 Package Managers</h2>
                <div class="service-item">
                    <div>
                        <div class="service-name">Composer</div>
                        <div class="service-info">PHP Package Manager</div>
                    </div>
                    <span class="status available">Available</span>
                </div>
                <div class="service-item">
                    <div>
                        <div class="service-name">npm</div>
                        <div class="service-info">Node Package Manager</div>
                    </div>
                    <span class="status available">Available</span>
                </div>
                <div class="service-item">
                    <div>
                        <div class="service-name">yarn</div>
                        <div class="service-info">Fast Package Manager</div>
                    </div>
                    <span class="status available">Available</span>
                </div>
                <div class="service-item">
                    <div>
                        <div class="service-name">pnpm</div>
                        <div class="service-info">Efficient Package Manager</div>
                    </div>
                    <span class="status available">Available</span>
                </div>
            </div>

            <div class="card">
                <h2>📦 Global Packages</h2>
                <div class="service-item">
                    <div>
                        <div class="service-name">nodemon</div>
                        <div class="service-info">Node Development Tool</div>
                    </div>
                    <span class="status available">Available</span>
                </div>
                <div class="service-item">
                    <div>
                        <div class="service-name">typescript</div>
                        <div class="service-info">TypeScript Compiler</div>
                    </div>
                    <span class="status available">Available</span>
                </div>
                <div class="service-item">
                    <div>
                        <div class="service-name">ts-node</div>
                        <div class="service-info">TypeScript Execution</div>
                    </div>
                    <span class="status available">Available</span>
                </div>
                <div class="service-item">
                    <div>
                        <div class="service-name">http-server</div>
                        <div class="service-info">Simple HTTP Server</div>
                    </div>
                    <span class="status available">Available</span>
                </div>
                <div class="service-item">
                    <div>
                        <div class="service-name">prettier</div>
                        <div class="service-info">Code Formatter</div>
                    </div>
                    <span class="status available">Available</span>
                </div>
                <div class="service-item">
                    <div>
                        <div class="service-name">eslint</div>
                        <div class="service-info">JavaScript/TypeScript Linter</div>
                    </div>
                    <span class="status available">Available</span>
                </div>
            </div>

            <div class="card">
                <h2>🐘 PHP Extensions</h2>
                <div class="extension-grid">
                    <div class="extension-item">fpm</div>
                    <div class="extension-item">cli</div>
                    <div class="extension-item">mysql</div>
                    <div class="extension-item">xml</div>
                    <div class="extension-item">xmlrpc</div>
                    <div class="extension-item">curl</div>
                    <div class="extension-item">gd</div>
                    <div class="extension-item">imagick</div>
                    <div class="extension-item">mbstring</div>
                    <div class="extension-item">opcache</div>
                    <div class="extension-item">soap</div>
                    <div class="extension-item">zip</div>
                    <div class="extension-item">intl</div>
                    <div class="extension-item">bcmath</div>
                    <div class="extension-item">gmp</div>
                    <div class="extension-item">pdo</div>
                    <div class="extension-item">sqlite3</div>
                    <div class="extension-item">pgsql</div>
                    <div class="extension-item">redis</div>
                    <div class="extension-item">memcached</div>
                    <div class="extension-item">xdebug</div>
                </div>
            </div>

            <div class="card">
                <h2>🌐 Available Ports</h2>
                <div class="port-grid">
                    <div class="port-item">
                        <div class="port-number">80</div>
                        <div class="service-info">Web Server (mapped to 90)</div>
                    </div>
                    <div class="port-item">
                        <div class="port-number">8081</div>
                        <div class="service-info">React Native Metro</div>
                    </div>
                    <div class="port-item">
                        <div class="port-number">19000-19002</div>
                        <div class="service-info">Expo DevTools</div>
                    </div>
                    <div class="port-item">
                        <div class="port-number">8097</div>
                        <div class="service-info">React Native Debugger</div>
                    </div>
                    <div class="port-item">
                        <div class="port-number">3000</div>
                        <div class="service-info">Development Server</div>
                    </div>
                    <div class="port-item">
                        <div class="port-number">4000</div>
                        <div class="service-info">Development Server (Alt)</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <h2>📁 Data Directories</h2>
                <div class="directory-item">
                    <div class="directory-path">/mysql/data</div>
                    <div class="directory-desc">MySQL database files (persistent)</div>
                </div>
                <div class="directory-item">
                    <div class="directory-path">/redis/data</div>
                    <div class="directory-desc">Redis data files (persistent)</div>
                </div>
                <div class="directory-item">
                    <div class="directory-path">/var/www/html</div>
                    <div class="directory-desc">Web server document root</div>
                </div>
                <div class="directory-item">
                    <div class="directory-path">/app</div>
                    <div class="directory-desc">Project files (Docker direct)</div>
                </div>
                <div class="directory-item">
                    <div class="directory-path">/workspaces</div>
                    <div class="directory-desc">Project files (VS Code Dev Container)</div>
                </div>
            </div>

            <div class="card">
                <h2>⚡ Vite Configuration</h2>
                <div class="directory-item">
                    <div class="directory-path">vite.config.js</div>
                    <div class="directory-desc">Set server: { host: "0.0.0.0" } for dev container port forwarding</div>
                </div>
                <div class="directory-item">
                    <div class="directory-path">Why 0.0.0.0?</div>
                    <div class="directory-desc">Vite requires 0.0.0.0 instead of the default localhost to allow connections from outside the container.</div>
                </div>
                <div class="directory-item">
                    <div class="directory-path">Laravel Vite</div>
                    <div class="directory-desc">Also requires host: "0.0.0.0" in vite.config.js</div>
                </div>
            </div>

            <div class="card">
                <h2>⚛️ React Native & Expo</h2>
                <div class="directory-item">
                    <div class="directory-path">⚠️ Learning & Prototyping Only</div>
                    <div class="directory-desc">This setup is primarily for quick start learning and prototyping. For production development builds, work directly on the host machine.</div>
                </div>
                <div class="directory-item">
                    <div class="directory-path">Network Configuration Required</div>
                    <div class="directory-desc">Set REACT_NATIVE_PACKAGER_HOSTNAME to your host IP address</div>
                </div>
                <div class="directory-item">
                    <div class="directory-path">Get Host IP (macOS)</div>
                    <div class="directory-desc">ifconfig | grep "inet " | grep -v 127.0.0.1 | awk '{print $2}'</div>
                </div>
                <div class="directory-item">
                    <div class="directory-path">Get Host IP (Windows)</div>
                    <div class="directory-desc">ipconfig | findstr "IPv4"</div>
                </div>
                <div class="directory-item">
                    <div class="directory-path">Export in Container</div>
                    <div class="directory-desc nowrap">export REACT_NATIVE_PACKAGER_HOSTNAME=192.168.1.100</div>
                </div>
            </div>
        </div>

        <div class="footer">
            <p><strong>Ubuntu 24.04 LTS</strong> • Container runs as <strong>root</strong> for development simplicity</p>
            <p>All services are pre-configured and ready for development</p>
        </div>
    </div>
</body>

</html>