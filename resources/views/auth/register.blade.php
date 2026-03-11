<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIP Registration</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #eef2ff, #f8fafc);
            font-family: system-ui;
            padding: 30px 10px;
        }

        .register-card {
            max-width: 720px;
            width: 100%;
            padding: 35px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(15px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, .15);
            transition: .4s;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header i {
            font-size: 30px;
            color: #7c3aed;
            background: #f3f0ff;
            padding: 15px;
            border-radius: 12px;
        }

        .header h2 {
            margin-top: 15px;
            font-weight: 700;
        }

        .header p {
            color: #666;
        }

        .tabs {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        .tab-btn {
            border: none;
            padding: 10px 25px;
            border-radius: 30px;
            background: #f1f5f9;
            font-weight: 600;
            transition: .3s;
        }

        .student-btn {
            background: #f3e8ff;
            color: #7747ca;
        }

        .student-btn.active {
            background: #7747ca;
            color: white;
        }

        .supervisor-btn {
            background: #fff1e6;
            color: #db733a;
        }

        .supervisor-btn.active {
            background: #db733a;
            color: white;
        }

        .company-btn {
            background: #e6f9f0;
            color: #16a34a;
        }

        .company-btn.active {
            background: #16a34a;
            color: white;
        }

        .form-section {
            display: none;
            animation: fadeSlide .4s ease;
        }

        .form-section.active {
            display: block;
        }

        @keyframes fadeSlide {
            from {
                opacity: 0;
                transform: translateY(15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .input-group {
            margin-bottom: 18px;
        }

        .input-group-text {
            background: white;
            border-right: 0;
        }

        .form-control {
            border-left: 0;
        }

        .supervisor-box {
            border: 2px solid #e9d5ff;
            border-radius: 15px;
            padding: 20px;
            background: #faf5ff;
            margin-top: 20px;
        }

        .main-btn {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 30px;
            color: white;
            font-weight: 600;
            background: linear-gradient(90deg, #9333ea, #2563eb);
            transition: .3s;
        }

        .main-btn:hover {
            transform: scale(1.02);
        }

        .footer {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
        }

        .alert ul {
            margin-bottom: 0;
            padding-left: 18px;
        }

        @media(max-width:900px) {
            .register-card {
                width: 95%;
                padding: 25px;
            }

            .row {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

    <div class="register-card">

        <div class="header">
            <i class="fa-solid fa-graduation-cap"></i>
            <h2>SIP Registration</h2>
            <p>Create your account to start your training journey</p>
        </div>

        <div class="tabs">
            <button type="button" class="tab-btn student-btn active" onclick="showForm('student', this)">Student Registration</button>
            <button type="button" class="tab-btn supervisor-btn" onclick="showForm('supervisor', this)">Supervisor Registration</button>
            <button type="button" class="tab-btn company-btn" onclick="showForm('company', this)">Company Registration</button>
        </div>

        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('register.process') }}" id="registerForm" novalidate>
            @csrf

            <input type="hidden" name="role" id="selectedRole" value="{{ old('role', 'student') }}">

            <!-- STUDENT -->
            <div id="student" class="form-section">
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-user"></i></span>
                            <input type="text" name="name" class="form-control student-input" placeholder="Full Name" value="{{ old('role') === 'student' ? old('name') : '' }}">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-id-badge"></i></span>
                            <input type="text" name="university_id" class="form-control student-input" placeholder="University ID" value="{{ old('role') === 'student' ? old('university_id') : '' }}">
                        </div>
                    </div>
                </div>

                <div class="input-group">
                    <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                    <input type="email" name="email" class="form-control student-input" placeholder="Email Address" value="{{ old('role') === 'student' ? old('email') : '' }}">
                </div>

                <div class="input-group">
                    <span class="input-group-text"><i class="fa fa-phone"></i></span>
                    <input type="text" name="phone_number" class="form-control student-input" placeholder="Phone Number" value="{{ old('role') === 'student' ? old('phone_number') : '' }}">
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-lock"></i></span>
                            <input type="password" name="password" class="form-control student-input" placeholder="Password">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-lock"></i></span>
                            <input type="password" name="password_confirmation" class="form-control student-input" placeholder="Confirm Password">
                        </div>
                    </div>
                </div>

                <div class="supervisor-box">
                    <strong>Supervisor Code</strong>
                    <p style="font-size:13px;color:#666;">Enter your assigned supervisor code</p>

                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-hashtag"></i></span>
                        <input type="text" name="supervisor_code" class="form-control student-input" placeholder="Supervisor Code" value="{{ old('role') === 'student' ? old('supervisor_code') : '' }}">
                    </div>
                </div>
            </div>

            <!-- SUPERVISOR -->
            <div id="supervisor" class="form-section">
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-user"></i></span>
                            <input type="text" name="name" class="form-control supervisor-input" placeholder="Full Name" value="{{ old('role') === 'supervisor' ? old('name') : '' }}">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-id-badge"></i></span>
                            <input type="text" name="university_id" class="form-control supervisor-input" placeholder="University ID" value="{{ old('role') === 'supervisor' ? old('university_id') : '' }}">
                        </div>
                    </div>
                </div>

                <div class="input-group">
                    <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                    <input type="email" name="email" class="form-control supervisor-input" placeholder="Email" value="{{ old('role') === 'supervisor' ? old('email') : '' }}">
                </div>

                <div class="input-group">
                    <span class="input-group-text"><i class="fa fa-phone"></i></span>
                    <input type="text" name="phone_number" class="form-control supervisor-input" placeholder="Phone Number" value="{{ old('role') === 'supervisor' ? old('phone_number') : '' }}">
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-lock"></i></span>
                            <input type="password" name="password" class="form-control supervisor-input" placeholder="Password">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-lock"></i></span>
                            <input type="password" name="password_confirmation" class="form-control supervisor-input" placeholder="Confirm Password">
                        </div>
                    </div>
                </div>
            </div>

            <!-- COMPANY -->
            <div id="company" class="form-section">
                <div class="input-group">
                    <span class="input-group-text"><i class="fa fa-building"></i></span>
                    <input type="text" name="name" class="form-control company-input" placeholder="Company Name" value="{{ old('role') === 'company' ? old('name') : '' }}">
                </div>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa fa-building"></i></span>
                    <input type="text" name="company_name" class="form-control company-input" placeholder="Company Name" value="{{ old('role') === 'company' ? old('company_name') : '' }}">
                </div>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                    <input type="email" name="email" class="form-control company-input" placeholder="Email" value="{{ old('role') === 'company' ? old('email') : '' }}">
                </div>

                <div class="input-group">
                    <span class="input-group-text"><i class="fa fa-phone"></i></span>
                    <input type="text" name="phone_number" class="form-control company-input" placeholder="Phone Number" value="{{ old('role') === 'company' ? old('phone_number') : '' }}">
                </div>

                <div class="input-group">
                    <span class="input-group-text"><i class="fa fa-location-dot"></i></span>
                    <input type="text" name="company_address" class="form-control company-input" placeholder="Company Address" value="{{ old('role') === 'company' ? old('company_address') : '' }}">
                </div>

                <div class="input-group">
                    <span class="input-group-text"><i class="fa fa-globe"></i></span>
                    <input type="text" name="company_website" class="form-control company-input" placeholder="Company Website" value="{{ old('role') === 'company' ? old('company_website') : '' }}">
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-lock"></i></span>
                            <input type="password" name="password" class="form-control company-input" placeholder="Password">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-lock"></i></span>
                            <input type="password" name="password_confirmation" class="form-control company-input" placeholder="Confirm Password">
                        </div>
                    </div>
                </div>
            </div>

            <br>

            <button type="submit" class="main-btn">
                <i class="fa-solid fa-arrow-right-to-bracket me-2"></i>
                Create Account
            </button>

            <div class="footer">
                Already have an account?
                <a href="{{ route('login') }}">Sign in</a>
            </div>
        </form>

    </div>

    <script>
        function setSectionState(role) {
            const allSections = document.querySelectorAll('.form-section');
            const roleInput = document.getElementById('selectedRole');

            allSections.forEach(section => {
                const isActive = section.id === role;
                section.classList.toggle('active', isActive);

                section.querySelectorAll('input').forEach(input => {
                    if (input.type !== 'hidden') {
                        input.disabled = !isActive;
                        input.required = false;
                    }
                });
            });

            roleInput.value = role;

            if (role === 'student') {
                document.querySelectorAll('.student-input').forEach(input => {
                    input.disabled = false;
                    input.required = true;
                });
            }

            if (role === 'supervisor') {
                document.querySelectorAll('.supervisor-input').forEach(input => {
                    input.disabled = false;
                    input.required = true;
                });
            }

            if (role === 'company') {
                document.querySelectorAll('.company-input').forEach(input => {
                    input.disabled = false;
                    input.required = true;
                });
            }
        }

        function showForm(type, btn) {
            document.querySelectorAll(".tab-btn").forEach(el => {
                el.classList.remove("active");
            });

            btn.classList.add("active");
            setSectionState(type);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const oldRole = "{{ old('role', 'student') }}";

            const studentBtn = document.querySelector('.student-btn');
            const supervisorBtn = document.querySelector('.supervisor-btn');
            const companyBtn = document.querySelector('.company-btn');

            document.querySelectorAll(".tab-btn").forEach(el => el.classList.remove("active"));

            if (oldRole === 'supervisor') {
                supervisorBtn.classList.add('active');
            } else if (oldRole === 'company') {
                companyBtn.classList.add('active');
            } else {
                studentBtn.classList.add('active');
            }

            setSectionState(oldRole);
        });
    </script>

</body>

</html>