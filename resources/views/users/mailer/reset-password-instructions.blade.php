<head>
    <style>
        body {
            background-color: #f9fafb;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 42rem;
            margin: 2rem auto;
            background-color: #ffffff;
            border-radius: 0.5rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            border: 1px solid #e5e7eb;
            overflow: hidden;
        }

        .header {
            background-color: #059669;
            padding: 2rem 1.5rem;
            text-align: center;
        }

        .header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #ffffff;
            margin: 0;
        }

        .content {
            padding: 2rem 1.5rem;
        }

        .alert {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1.5rem;
        }

        .alert-icon {
            background-color: #d1fae5;
            padding: 0.75rem;
            border-radius: 9999px;
            margin-right: 1rem;
        }

        .alert-icon svg {
            width: 1.5rem;
            height: 1.5rem;
            color: #059669;
        }

        .alert-content h2 {
            font-size: 1.125rem;
            font-weight: 600;
            color: #1f2937;
            margin: 0;
        }

        .alert-content p {
            color: #4b5563;
            margin-top: 0.25rem;
        }

        .divider {
            border-top: 1px solid #f3f4f6;
            margin: 1.5rem 0;
        }

        .message {
            margin-bottom: 1.5rem;
        }

        .message p {
            color: #4b5563;
            margin-bottom: 1rem;
        }

        .cta {
            text-align: center;
            margin: 2rem 0;
        }

        .cta-button {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background-color: #059669;
            color: #ffffff !important;
            font-weight: 500;
            border-radius: 0.5rem;
            text-decoration: none;
            transition: background-color 0.2s ease-in-out;
        }

        .cta-button:hover {
            background-color: #047857;
        }

        .secure-text {
            color: #6b7280;
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }

        .footer {
            background-color: #f9fafb;
            padding: 1rem 1.5rem;
            text-align: center;
            color: #6b7280;
            font-size: 0.875rem;
            border-top: 1px solid #f3f4f6;
        }

        .footer p {
            margin: 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>{{ __('Reset Your Password') }}</h1>
        </div>

        <div class="content">
            <div class="alert">
                <div class="alert-content">
                    <h2>{{ __('Password Reset Request') }}</h2>
                    <p>{{ __('Hello :name,', ['name' => $user->name]) }}</p>
                </div>
            </div>

            <div class="divider"></div>

            <div class="message">
                <p>{{ __('You are receiving this email because we received a password reset request for your account.') }}
                </p>
                <p>{{ __('Click the button below to reset your password:') }}</p>
                <p>{{ __('If you did not initiate this password reset request, you can safely disregard this message. Your account will remain secure.') }}
                </p>
                <p>{{ __('Your password will not change until you access the link above and create a new one.') }}</p>
            </div>

            <div class="cta">
                <a href="{{ route('password.reset', ['token' => $token, 'email' => $user->email]) }}" class="cta-button">
                    {{ __('Reset Password') }}
                </a>
                <p class="secure-text">
                    {{ __('This link will expire in :count minutes.', ['count' => config('auth.passwords.' . config('auth.defaults.passwords') . '.expire')]) }}
                </p>
            </div>
        </div>

        <div class="footer">
            <p>{{ __('Copyright © :year Your Company. All rights reserved.', ['year' => now()->year]) }}</p>
        </div>
    </div>
</body>
