@extends('emails.layout')

@section('content')
    <style>
        /* Clients like Gmail ignore <style> for most layout, so keep critical styles inline below */
        @media (max-width: 620px) {
            .container {
                width: 100% !important;
            }

            .px {
                padding-left: 16px !important;
                padding-right: 16px !important;
            }

            .btn {
                display: block !important;
                width: 100% !important;
            }
        }
    </style>
    @php
        $password = $plainPassword ?? 'Please use the password shared by your administrator.';
    @endphp
    {{-- <p>Login Details as below : </p>
    <p>User Name : {{ $user->email }}</p>
    <p>Password : {{ $password }}</p>
    <p>Thank you for registering with us.</p> --}}
    <div style="display:none;max-height:0;overflow:hidden;opacity:0;color:transparent;">
        Your Balantro account setup is complete. Here are your sign-in details.
    </div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f6f7f9;">
        <tr>
            <td align="center" style="padding:24px;">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" class="container"
                    style="width:600px;max-width:100%;background:#ffffff;border-radius:12px;box-shadow:0 1px 2px rgba(0,0,0,.06),0 8px 24px rgba(0,0,0,.06);overflow:hidden;">
                    <!-- Header -->
                    <tr>
                        <td style="background:#111827;color:#ffffff;padding:20px 24px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="font-size:18px;font-weight:600;">Balantro</td>
                                    <td align="right" style="font-size:12px;opacity:.85;">Account Setup</td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td class="px" style="padding:28px 32px;">
                            <h1 style="margin:0 0 12px;font-size:22px;line-height:1.3;color:#111827;">Your Balantro Account Is Ready,
                                {{ $user->name }}!</h1>
                            <p style="margin:0 0 16px;font-size:15px;line-height:1.6;color:#374151;">
                                Your client account has been created successfully. Please use the secure setup details below to sign in.
                            </p>

                            <!-- Credentials card -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="margin:16px 0 20px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:10px;">
                                <tr>
                                    <td style="padding:16px 20px;">
                                        <p
                                            style="margin:0 0 8px;font-size:13px;letter-spacing:.02em;color:#6b7280;text-transform:uppercase;">
                                            Account setup details</p>
                                        <table role="presentation" cellpadding="0" cellspacing="0" style="width:100%;">
                                            <tr>
                                                <td style="padding:6px 0;font-size:15px;color:#111827;width:130px;">Username
                                                </td>
                                                <td style="padding:6px 0;font-size:15px;color:#374151;">{{ $user->email }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding:6px 0;font-size:15px;color:#111827;">Password</td>
                                                <td style="padding:6px 0;font-size:15px;color:#374151;">
                                                    <strong>{{ $password }}</strong>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- CTA -->
                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin:8px 0 18px;">
                                <tr>
                                    <td>
                                        <a href="{{ route('login') }}" class="btn"
                                            style="display:inline-block;background:#2563eb;color:#ffffff;text-decoration:none;border-radius:8px;padding:12px 18px;font-size:15px;font-weight:600;">
                                            Sign in to Balantro
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 16px;font-size:14px;line-height:1.6;color:#4b5563;">
                                For your security, please change this password after your first login and do not share it with anyone.
                            </p>

                            <hr style="border:none;border-top:1px solid #e5e7eb;margin:20px 0;">

                            <p style="margin:0;font-size:13px;line-height:1.6;color:#6b7280;">
                                Need help? Reply to this email or visit our support center.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding:16px 24px;background:#ffffff;border-top:1px solid #f3f4f6;">
                            <p style="margin:0;font-size:12px;color:#9ca3af;">
                                © {{ date('Y') }} Balantro. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>

                <!-- Small footer note -->
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" class="container"
                    style="width:600px;max-width:100%;margin-top:12px;">
                    <tr>
                        <td align="center" style="font-size:12px;color:#9ca3af;padding:8px 12px;">
                            You’re receiving this because an account was created for {{ $user->email }}.
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>
@endsection
