<x-mail::message>
    # Balantro - Password Reset Request

    Hello!

    You are receiving this email because we received a password reset request for your Balantro account.

    <x-mail::button :url="$url">
        Reset Password
    </x-mail::button>

    This password reset link will expire in 60 minutes.

    If you did not initiate this password reset request, you can safely disregard this message. Your account will remain secure.

    **Regards,**
    Balantro Team
</x-mail::message>
