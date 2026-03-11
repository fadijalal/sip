<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIP - Professional Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            --primary: #8b1cf4;
            --secondary: #1d4ed8;
            --soft-purple: #f3e8ff;
            --text-dark: #1f2937;
            --text-muted: #6b7280;
            --border: #d1d5db;
            --card-bg: rgba(255, 255, 255, 0.88);
            --page-bg: #edf1f5;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: var(--page-bg);
            font-family: "Inter", sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 28px 16px;
            color: var(--text-dark);
        }

        .page-wrap {
            width: 100%;
            max-width: 560px;
        }

        .top-brand {
            text-align: center;
            margin-bottom: 26px;
        }

        .logo-box {
            width: 66px;
            height: 66px;
            margin: 0 auto 14px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.45);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.45),
                0 10px 25px rgba(0, 0, 0, 0.05);
            backdrop-filter: blur(8px);
        }

        .logo-box i {
            font-size: 30px;
            color: var(--primary);
        }

        .top-brand h1 {
            font-size: 27px;
            font-weight: 500;
            margin: 0 0 8px;
            letter-spacing: 0.2px;
        }

        .top-brand p {
            margin: 0;
            color: #5f6673;
            font-size: 15px;
            font-weight: 400;
        }

        .login-card {
            border: none;
            border-radius: 20px;
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            box-shadow:
                0 16px 35px rgba(0, 0, 0, 0.10),
                0 2px 8px rgba(0, 0, 0, 0.03);
            padding: 32px 32px 26px;
        }

        .form-label {
            font-size: 15px;
            color: #4b5563;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .switch-link {
            color: #6b7280;
            text-decoration: none;
            cursor: pointer;
            transition: .2s ease;
            background: none;
            border: none;
            padding: 0;
        }

        .switch-link.active {
            color: #374151;
            font-weight: 600;
        }

        .switch-link:hover {
            color: #4f46e5;
        }

        .input-group {
            margin-bottom: 24px;
        }

        .input-group-text {
            height: 50px;
            border-radius: 16px 0 0 16px !important;
            border: 1.5px solid var(--border);
            border-right: 0;
            background: #fff;
            color: #9ca3af;
            font-size: 20px;
            padding-left: 16px;
            padding-right: 10px;
        }

        .form-control {
            height: 50px;
            border-radius: 0 16px 16px 0 !important;
            border: 1.5px solid var(--border);
            border-left: 0;
            box-shadow: none !important;
            font-size: 15px;
            color: #374151;
            padding-left: 8px;
        }

        .form-control::placeholder {
            color: #9ca3af;
        }

        .password-toggle {
            height: 50px;
            border-radius: 0 16px 16px 0 !important;
            border: 1.5px solid var(--border);
            border-left: 0;
            background: #fff;
            color: #9ca3af;
            cursor: pointer;
            padding: 0 16px;
        }

        .input-group:focus-within .input-group-text,
        .input-group:focus-within .form-control,
        .input-group:focus-within .password-toggle {
            border-color: #b7bccc;
        }

        .options-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-top: 2px;
            margin-bottom: 26px;
            flex-wrap: wrap;
        }

        .remember-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #4b5563;
            font-size: 14px;
        }

        .remember-wrap input[type="checkbox"] {
            width: 16px;
            height: 16px;
            border-radius: 4px;
            accent-color: #7c3aed;
        }

        .forgot-link {
            text-decoration: none;
            color: #5b46db;
            font-size: 14px;
            font-weight: 500;
        }

        .forgot-link:hover {
            color: #412bb8;
        }

        .login-btn {
            height: 52px;
            border: none;
            border-radius: 16px;
            color: #fff;
            font-size: 16px;
            font-weight: 500;
            background: linear-gradient(90deg, #a21cf3 0%, #1d4ed8 100%);
            box-shadow: 0 10px 18px rgba(102, 51, 255, 0.20);
            transition: .25s ease;
        }

        .login-btn:hover {
            transform: translateY(-1px);
            color: #fff;
        }

        .login-btn i {
            margin-right: 10px;
        }

        .bottom-text {
            text-align: center;
            margin-top: 22px;
            font-size: 14px;
            color: #5f6673;
        }

        .bottom-text a {
            color: #5b46db;
            text-decoration: none;
            font-weight: 500;
        }

        .bottom-text a:hover {
            color: #412bb8;
        }

        .alert {
            border-radius: 14px;
            font-size: 14px;
        }

        @media (max-width: 576px) {
            .page-wrap {
                max-width: 100%;
            }

            .top-brand {
                margin-bottom: 20px;
            }

            .top-brand h1 {
                font-size: 24px;
            }

            .top-brand p {
                font-size: 14px;
            }

            .login-card {
                padding: 22px 18px 20px;
                border-radius: 18px;
            }

            .options-row {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

    <div class="page-wrap">

        <div class="top-brand">
            <div class="logo-box">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>

            <h1>SIP</h1>
            <p>Sign in to access your training dashboard</p>
        </div>

        <div class="card login-card">

            @if(session('success'))
            <div class="alert alert-success small mb-3">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger small mb-3">
                {{ session('error') }}
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger small mb-3">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('login.process') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">
                        <button type="button" id="idSwitch" class="switch-link active" onclick="setIdentifierMode('id')">University ID</button>
                        /
                        <button type="button" id="emailSwitch" class="switch-link" onclick="setIdentifierMode('email')">Email</button>
                    </label>

                    <div class="input-group">
                        <span class="input-group-text">
                            <i id="identifierIcon" class="fa-solid fa-hashtag"></i>
                        </span>

                        <input
                            type="text"
                            name="identifier"
                            id="identifierInput"
                            class="form-control"
                            placeholder="STU-2024-001"
                            value="{{ old('identifier') }}"
                            required>
                    </div>
                </div>

                <div class="mb-2">
                    <label class="form-label">Password</label>

                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fa-solid fa-lock"></i>
                        </span>

                        <input
                            type="password"
                            name="password"
                            id="password"
                            class="form-control"
                            placeholder="••••••••"
                            required>

                        <span class="input-group-text password-toggle" id="togglePass">
                            <i class="fa-solid fa-eye" id="eyeIcon"></i>
                        </span>
                    </div>
                </div>

                <div class="options-row">
                    <label class="remember-wrap">
                        <input type="checkbox" name="remember">
                        <span>Remember me</span>
                    </label>

                    <a href="#" class="forgot-link">Forgot password?</a>
                </div>

                <button type="submit" class="btn login-btn w-100">
                    <i class="fa-solid fa-right-to-bracket me-2"></i>
                    Sign In
                </button>

                <div class="bottom-text">
                    Don't have an account?
                    <a href="{{ route('register') }}">Register now</a>
                </div>
            </form>

        </div>
    </div>

    <script>
        const togglePass = document.getElementById("togglePass");
        const passwordInput = document.getElementById("password");
        const eyeIcon = document.getElementById("eyeIcon");

        const identifierInput = document.getElementById("identifierInput");
        const identifierIcon = document.getElementById("identifierIcon");
        const idSwitch = document.getElementById("idSwitch");
        const emailSwitch = document.getElementById("emailSwitch");

        togglePass.addEventListener("click", () => {
            const type = passwordInput.type === "password" ? "text" : "password";
            passwordInput.type = type;

            eyeIcon.classList.toggle("fa-eye");
            eyeIcon.classList.toggle("fa-eye-slash");
        });

        function setIdentifierMode(mode) {
            if (mode === "email") {
                identifierInput.type = "email";
                identifierInput.placeholder = "student@example.com";
                identifierIcon.className = "fa-solid fa-envelope";
                emailSwitch.classList.add("active");
                idSwitch.classList.remove("active");
            } else {
                identifierInput.type = "text";
                identifierInput.placeholder = "STU-2024-001";
                identifierIcon.className = "fa-solid fa-hashtag";
                idSwitch.classList.add("active");
                emailSwitch.classList.remove("active");
            }

            identifierInput.focus();
        }
    </script>

</body>

</html>