<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HireFlow API Documentation</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swagger-ui-dist@5/swagger-ui.css">
    <style>
        :root {
            color-scheme: light;
            --bg: #f4f1ea;
            --panel: #ffffff;
            --ink: #132238;
            --muted: #5d6b84;
            --accent: #0f766e;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background:
                radial-gradient(circle at top left, rgba(15, 118, 110, 0.14), transparent 35%),
                linear-gradient(180deg, #fbfaf7 0%, var(--bg) 100%);
            color: var(--ink);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .hero {
            padding: 32px 24px 16px;
            max-width: 1320px;
            margin: 0 auto;
        }

        .hero-card {
            background: rgba(255, 255, 255, 0.78);
            border: 1px solid rgba(19, 34, 56, 0.08);
            border-radius: 24px;
            padding: 24px 28px;
            box-shadow: 0 24px 80px rgba(19, 34, 56, 0.08);
            backdrop-filter: blur(10px);
        }

        h1 {
            margin: 0 0 8px;
            font-size: clamp(28px, 4vw, 44px);
            letter-spacing: -0.03em;
        }

        p {
            margin: 0;
            color: var(--muted);
            line-height: 1.6;
            max-width: 72ch;
        }

        .meta {
            margin-top: 14px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(15, 118, 110, 0.08);
            color: var(--accent);
            font-size: 14px;
            font-weight: 600;
        }

        .swagger-shell {
            max-width: 1320px;
            margin: 0 auto 32px;
            padding: 0 24px 32px;
        }

        #swagger-ui {
            background: var(--panel);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 24px 80px rgba(19, 34, 56, 0.08);
        }
    </style>
</head>

<body>
    <div class="hero">
        <div class="hero-card">
            <h1>HireFlow API Documentation</h1>
            <p>Interactive Swagger docs for auth, profile, uploads, and portfolio endpoints. Use the Authorize button
                with a Bearer token for protected routes.</p>
            <div class="meta">
                <span class="pill">OpenAPI 3.0</span>
                <span class="pill">Bearer auth</span>
                <span class="pill">{{ $specUrl }}</span>
            </div>
        </div>
    </div>

    <div class="swagger-shell">
        <div id="swagger-ui"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/swagger-ui-dist@5/swagger-ui-bundle.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swagger-ui-dist@5/swagger-ui-standalone-preset.js"></script>
    <script>
        window.onload = function() {
            window.ui = SwaggerUIBundle({
                url: @json($specUrl),
                dom_id: '#swagger-ui',
                deepLinking: true,
                docExpansion: 'list',
                presets: [
                    SwaggerUIBundle.presets.apis,
                    SwaggerUIStandalonePreset,
                ],
                layout: 'BaseLayout',
            });
        };
    </script>
</body>

</html>
