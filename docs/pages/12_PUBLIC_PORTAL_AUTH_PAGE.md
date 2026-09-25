# 12. Public Portal, Landing Page & Authentication Specification (`/`, `/login`, `/register`)

## 1. Overview & Purpose
The Public Portal represents the customer-facing SaaS website and user authentication flows, fully driven by the backend CMS routes (`/api/web`).

## 2. Views & Pages
1. **Homepage / Landing Page (`/`)**: Hero banner with live interactive WhatsApp mockup, channel badges, feature showcases, testimonials, dynamic pricing cards, and contact form.
2. **Login Page (`/login`)**: Clean, responsive authentication card supporting email/password login, Google OAuth login (`g_auth`), "Remember Me", and Agent login portal switch.
3. **Register Page (`/register`)**: New tenant signup with name, email, password, and automatic assignment to the default Free Trial plan.
4. **Forgot & Reset Password (`/forgot-password`)**: Email validation and secure password reset token delivery via SMTP.
5. **Pricing Page (`/pricing`)**: Dynamic comparison grid pulling live subscription packages from `GET /api/web/get_plans`.
6. **Blog & FAQ Pages (`/blogs`, `/faq`)**: Dynamic articles and collapsible FAQs managed from the admin CMS.
