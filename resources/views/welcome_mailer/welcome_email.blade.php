<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Welcome Email</title>
  <style>
    body {
      background-color: #f9fafb; 
      font-family: Arial, sans-serif; 
      line-height: 1.5; 
      color: #374151; 
      margin: 0; 
      padding: 0;
    }
    .container {
      max-width: 42rem; 
      margin: 2rem auto; 
      background-color: white; 
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
      color: white; 
      margin: 0;
    }
    .content {
      padding: 2rem 1.5rem;
    }
    .welcome-section {
      display: flex; 
      align-items: flex-start; 
      margin-bottom: 1.5rem;
    }
    .welcome-text h2 {
      font-size: 1.125rem; 
      font-weight: 600; 
      color: #1f2937; 
      margin: 0;
    }
    .welcome-text p {
      color: #4b5563; 
      margin-top: 0.25rem; 
      margin-bottom: 0;
    }
    .divider {
      border-top: 1px solid #f3f4f6; 
      margin: 1.5rem 0;
    }
    .features {
      margin-bottom: 1.5rem;
    }
    .features h3 {
      font-weight: 500; 
      color: #1f2937; 
      margin-bottom: 0.5rem; 
      margin-top: 0;
    }
    .features ul {
      list-style-type: disc; 
      padding-left: 1.25rem; 
      color: #4b5563; 
      margin-top: 0;
    }
    .features li {
      margin-bottom: 0.25rem;
    }
    .cta-section {
      text-align: center; 
      margin: 2rem 0;
    }
    .cta-button {
      display: inline-block; 
      padding: 0.75rem 1.5rem; 
      background-color: #059669; 
      color: white !important;
      font-weight: 500; 
      border-radius: 0.5rem; 
      text-decoration: none;
    }
    .cta-note {
      color: #6b7280; 
      font-size: 0.875rem; 
      margin-top: 0.5rem; 
      margin-bottom: 0;
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
      <h1>Welcome Aboard, {{ $user->name }}!</h1>
    </div>
    
    <div class="content">
      <div class="welcome-section">
        <div class="welcome-text">
          <h2>Account Successfully Verified</h2>
          <p>Your account is now fully activated</p>
        </div>
      </div>
      
      <div class="divider"></div>
      
      <div class="features">
        <h3>Get started with:</h3>
        <ul>
          <li>Access to all platform features</li>
          <li>Personalized dashboard</li>
        </ul>
      </div>
      
      <div class="cta-section">
        <a href="{{ route('login') }}" class="cta-button">Access Your Account Now</a>
        <p class="cta-note">Secure login with your registered credentials</p>
      </div>
    </div>
    
    <div class="footer">
      <p>© {{ date('Y') }} Balantro. All rights reserved.</p>
    </div>
  </div>
</body>
</html>