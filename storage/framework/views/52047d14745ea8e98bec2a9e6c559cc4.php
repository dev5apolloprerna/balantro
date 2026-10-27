<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Your Balantro account details</title>
</head>
<body style="background:#f3f4f6;font-family:Arial,sans-serif;color:#1f2937;padding:24px">
    <div style="max-width:560px;margin:auto;background:#fff;border-radius:12px;padding:32px">
        <h1 style="margin-top:0;font-size:24px">Welcome to Balantro</h1>
        <p>Hi <?php echo e($name); ?>,</p>
        <p>Your Balantro account is almost ready. Verify your mobile number with the code we sent by SMS to finish signing up.</p>
        <p>After that you can also sign in with these details:</p>
        <table role="presentation" style="width:100%;border-collapse:collapse;margin:16px 0">
            <tr>
                <td style="padding:8px 0;color:#6b7280;width:120px">Email</td>
                <td style="padding:8px 0;font-weight:bold"><?php echo e($email); ?></td>
            </tr>
            <tr>
                <td style="padding:8px 0;color:#6b7280">Password</td>
                <td style="padding:8px 0;font-weight:bold;font-family:Consolas,Menlo,monospace;font-size:18px;letter-spacing:1px"><?php echo e($password); ?></td>
            </tr>
        </table>
        <p>For your security, change this password after you sign in (Profile &rarr; Change password). Balantro staff will never ask you for it.</p>
        <p style="color:#6b7280;font-size:13px">If you did not sign up for Balantro, ignore this email. The account is not created until the mobile number is verified.</p>
    </div>
</body>
</html>
<?php /**PATH D:\xampp\htdocs\balantro\resources\views/emails/registration_password.blade.php ENDPATH**/ ?>