<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify your Balantro account</title>
</head>
<body style="background:#f3f4f6;font-family:Arial,sans-serif;color:#1f2937;padding:24px">
    <div style="max-width:560px;margin:auto;background:#fff;border-radius:12px;padding:32px">
        <h1 style="margin-top:0;font-size:24px">Verify your email address</h1>
        <p>Hi <?php echo e($name); ?>,</p>
        <p>Enter this one-time verification code to finish creating your Balantro account:</p>
        <p style="font-size:32px;font-weight:bold;letter-spacing:8px;text-align:center;color:#0891b2"><?php echo e($otp); ?></p>
        <p>This code expires in <?php echo e($expiryMinutes); ?> minutes. If you did not request this account, you can ignore this email.</p>
    </div>
</body>
</html><?php /**PATH D:\xampp\htdocs\balantro\resources\views/emails/registration_otp.blade.php ENDPATH**/ ?>