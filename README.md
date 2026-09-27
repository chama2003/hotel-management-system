Overview

LuxStay is a demo-grade but production-shaped Hotel Management System. It covers the full guest journey — browsing rooms, checking live availability, booking, paying, and downloading an invoice — alongside the operational tooling a real hotel needs: a staff front-desk console for walk-ins and check-in/out, and an admin panel for room management, user management, and revenue/occupancy analytics.

It's built with plain Laravel MVC (no SPA framework required), Blade templates styled with Tailwind CSS, and Alpine.js for the interactive pieces (the booking wizard, dashboards, modals). Everything runs from a single Laravel app and a MySQL database.

Features
👤 Authentication & Role-Based Access Control
Secure registration/login for Customers, Staff, and Admins
Role middleware gates every route group (role:admin, role:staff,admin, etc.)
Public sign-up always creates a Customer account; Staff/Admin accounts are provisioned only by an Admin
🛏️ Room Management & Catalog
Public catalog with filters (type, max price, date range) and pagination
Room detail pages with image gallery and amenity list (Wi-Fi, A/C, TV, minibar, jacuzzi, etc.)
Admin CRUD for rooms, including real-time status toggling (available / occupied / cleaning / maintenance)
📅 Advanced Booking Engine
Interactive, multi-step booking flow: Dates → Guest Details → Review & Pay
Live AJAX availability check as the guest picks dates — no double-bookings
Server-side overlap re-check at submission time to close race conditions
Automatic pricing breakdown (subtotal, tax, total) with a configurable tax rate
💳 Payments & Invoicing
Sandbox/mock payment gateway (instant "paid" simulation) or Cash-on-Arrival — no real charges, no external API keys required
Auto-generated PDF invoice on every confirmed booking (via barryvdh/laravel-dompdf), downloadable anytime from the guest dashboard
🧾 Guest Dashboard
Upcoming / active / past bookings at a glance
Profile editing (name, phone, address, password)
Self-service cancellation, respecting a configurable free-cancellation window
🛎️ Staff Front-Desk Console
Today's arrivals, departures, and in-house guest counts
One-click check-in / check-out
Walk-in / phone reservation form (creates the guest account on the fly)
Room status board (available / occupied / cleaning / maintenance) with an audit log
📊 Admin Analytics & Reporting
Total revenue, live occupancy rate, booking volume, user counts
Monthly bookings & revenue trend chart (Chart.js)
Room-type breakdown
One-click CSV export of bookings for a given date range (opens directly in Excel/Sheets)
Tech Stack
Layer	Technology
Backend	PHP 8.2+, Laravel 11, Eloquent ORM
Database	MySQL 8 (or MariaDB 10.6+)
Frontend	Blade templates, Tailwind CSS, Alpine.js, Chart.js, FontAwesome
PDF/Export	barryvdh/laravel-dompdf, maatwebsite/excel
Auth	Laravel's built-in session auth + custom RoleMiddleware
Project Structure
app/
  Models/                     User, Room, Amenity, RoomImage, RoomStatusLog, Booking, Payment, Invoice
  Http/
    Controllers/
      RoomController.php               # public catalog + room detail
      BookingController.php            # booking wizard, availability API, cancel, PDF invoice
      Auth/                            # register / login / logout
      Customer/DashboardController.php
      Staff/FrontDeskController.php
      Admin/RoomManagementController.php
      Admin/UserManagementController.php
      Admin/AnalyticsController.php    # dashboard metrics + CSV export
    Middleware/RoleMiddleware.php      # role:admin / role:staff,admin / ...
database/
  migrations/                 users, rooms, amenities (+pivot), room_images, room_status_logs,
                               bookings, payments, invoices
  seeders/                    UserSeeder, AmenitySeeder, RoomSeeder
resources/views/              Blade + Tailwind, organized by area (rooms, bookings, customer, staff, admin)
routes/web.php                Role-gated route groups
config/booking.php            Cancellation window, tax rate, payment gateway mode
Getting Started
Requirements
PHP 8.2+ with pdo_mysql, mbstring, openssl, gd, zip
Composer 2
MySQL 8 (or MariaDB 10.6+)
Node.js not required — Tailwind/Alpine/Chart.js/FontAwesome are loaded via CDN
