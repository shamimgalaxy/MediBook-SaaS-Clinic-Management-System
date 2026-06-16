# 🏥 MediBook

**Multi-tenant SaaS Clinic Management System**

MediBook is a full-featured, multi-tenant SaaS platform built for modern clinics and healthcare providers. Each clinic operates in complete isolation with its own subdomain, data, and branding — powered by Laravel 12 and `stancl/tenancy`.

---

## ✨ Features

### 🏢 Multi-Tenancy
- Subdomain-based tenant isolation (`clinic.medibook.test`)
- Super Admin panel for managing all tenants
- Per-tenant database separation via `stancl/tenancy`
- Tenant onboarding with SSLCommerz subscription payment

### 👥 Role-Based Access Control (5 Roles)
| Role | Capabilities |
|------|-------------|
| **Super Admin** | Manage tenants, plans, global settings |
| **Clinic Admin** | Full clinic control — staff, billing, reports |
| **Doctor** | View appointments, write prescriptions, manage schedule |
| **Receptionist** | Book appointments, manage patients, generate invoices |
| **Patient** | Self-register, book appointments, view prescriptions |

### 📅 Appointments
- Smart scheduling with doctor availability & time slots
- Auto fee population (Alpine.js)
- Status tracking: Pending → Confirmed → Completed → Cancelled
- Email & in-app notifications for bookings

### 💊 Prescriptions
- Doctor-issued prescriptions per appointment
- Medication, dosage, instructions
- Printable PDF via DomPDF

### 🧾 Billing & Invoices
- Invoice generation per appointment
- Payment status tracking (Paid / Unpaid / Partial)
- PDF invoice download (DomPDF)
- Email invoice on payment (InvoiceSent notification)

### 🩺 Patient Portal
- Patient self-registration & login
- View upcoming/past appointments
- Access prescriptions and invoices
- Book new appointments directly

### 📊 Reports
- Appointment reports (by date, doctor, status)
- Revenue reports
- Patient visit history

### ⚙️ Clinic Settings
- Clinic profile & branding
- Working hours configuration
- Doctor schedule management
- Notification preferences

### 💳 Subscription Management
- Subscription plans (Basic, Pro, Enterprise)
- SSLCommerz payment gateway integration
- Plan upgrade/downgrade support
- Subscription expiry alerts

### 🔔 Notifications
- Database notifications (bell dropdown)
- Email notifications (appointment booked, invoice sent, etc.)
- Real-time bell badge with unread count

---

## 🛠️ Tech Stack

| Layer | Technology |
|-------|-----------|
| **Backend** | PHP 8, Laravel 12 |
| **Multi-Tenancy** | stancl/tenancy |
| **Auth & RBAC** | Laravel Breeze + Spatie Permission |
| **Frontend** | Blade, Tailwind CSS, Alpine.js |
| **Icons** | Tabler Icons |
| **PDF** | DomPDF (barryvdh/laravel-dompdf) |
| **Payment** | SSLCommerz |
| **Database** | MySQL |
| **Queue** | Laravel Queue (database driver) |
| **Mail** | Laravel Mail (SMTP) |
| **Build Tool** | Vite + NPM |

---

## 🚀 Installation

### Requirements
- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL
- Laravel 12

### Steps

```bash
# 1. Clone the repository
git clone https://github.com/shamimgalaxy/medibook.git
cd medibook

# 2. Install PHP dependencies
composer install

# 3. Install JS dependencies
npm install && npm run build

# 4. Copy environment file
cp .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Configure your .env
# Set DB_*, MAIL_*, SSLCOMMERZ_*, APP_URL

# 7. Run migrations (central DB)
php artisan migrate

# 8. Seed super admin
php artisan db:seed --class=SuperAdminSeeder

# 9. Configure subdomain routing
# Point *.yourdomain.com to your server
# Update APP_URL and TENANCY_CENTRAL_DOMAINS in .env

# 10. Serve the application
php artisan serve
```

### Environment Variables (Key)

```env
APP_URL=http://medibook.test
TENANCY_CENTRAL_DOMAINS=medibook.test

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=medibook_central
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password

SSLCOMMERZ_STORE_ID=your_store_id
SSLCOMMERZ_STORE_PASSWORD=your_store_password
SSLCOMMERZ_IS_SANDBOX=true
```

---

## 🗂️ Project Structure

```
app/
├── Http/Controllers/
│   ├── Central/          # Super Admin controllers
│   └── Tenant/           # Clinic controllers (flat structure)
├── Models/               # Eloquent models
├── Notifications/        # Mail & DB notifications
└── Providers/

resources/views/
├── central/              # Super Admin views
└── tenant/               # Clinic views per module
    ├── appointments/
    ├── billing/
    ├── doctors/
    ├── patients/
    ├── prescriptions/
    ├── reports/
    └── settings/
```

---

## 🎨 Theme

| Panel | Primary Color |
|-------|--------------|
| Super Admin | Indigo |
| Clinic Admin | Blue `#185FA5` |
| Doctor | Green `#0F6E56` |
| Patient Portal | Teal |

---

## 📸 Screenshots

> *(Add screenshots here)*

| Dashboard | Appointments | Prescriptions |
|-----------|-------------|---------------|
| ![Dashboard](#) | ![Appointments](#) | ![Prescriptions](#) |

---

## 📄 License

This project is open-sourced under the [MIT License](LICENSE).

---

## 👨‍💻 Author

**Shamim Ahmed**
Full Stack Laravel Developer
📧 shamimgalaxy@gmail.com
🔗 [GitHub](https://github.com/shamimgalaxy) · [LinkedIn](https://linkedin.com/in/shamimgalaxy)

---

> Built with ❤️ using Laravel 12 · stancl/tenancy · Spatie Permission · SSLCommerz
