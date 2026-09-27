# 🏨 LuxStay — Hotel Management System

**LuxStay** is a production-shaped, full-stack Hotel Management System built with Laravel 11. It covers the complete guest journey—from browsing rooms and checking live availability to booking, payment simulation, and automated PDF invoice generation—while providing powerful operational tooling for staff and administrators.

---

## ✨ Features

### 👤 Authentication & Role-Based Access Control (RBAC)
* Secure registration and login for **Customers**, **Staff**, and **Admins**.
* Route protection via custom `RoleMiddleware` gating access by roles (`role:admin`, `role:staff,admin`, etc.).
* Public sign-ups automatically provision a Customer account; higher-level accounts are managed securely by administrators.

### 🛏️ Room Management & Catalog
* Public room catalog featuring filters (room type, maximum price range, date availability) and pagination.
* Detailed room view with image galleries and amenity lists (Wi-Fi, A/C, TV, minibar, jacuzzi, etc.).
* Admin CRUD operations for managing rooms and real-time room status toggling (*Available*, *Occupied*, *Cleaning*, *Maintenance*).

### 📅 Advanced Booking Engine
* Interactive, multi-step booking wizard workflow: *Dates selection* ➔ *Guest details* ➔ *Review & Payment*.
* Live AJAX availability validation preventing double bookings.
* Server-side overlap re-checks at submission to eliminate race conditions.
* Automatic pricing calculation handling subtotals, configurable taxes, and grand totals.

### 💳 Payments & Invoicing
* Simulated payment gateway (instant "Paid" status) alongside Cash-on-Arrival options—no external API keys required.
* Automated PDF invoice generation upon booking confirmation (`barryvdh/laravel-dompdf`), downloadable directly from the customer dashboard.

### 🧾 Customer Dashboard
* Overview of upcoming, active, and past bookings.
* Self-service profile management (name, phone, address, and password updates).
* Automated cancellation handling respecting configurable free-cancellation windows.

### 🛎️ Staff Front-Desk Console
* Daily operational summary tracking arrivals, departures, and in-house guest counts.
* One-click check-in and check-out management.
* Walk-in and phone reservation processing that provisions guest accounts on the fly.
* Live room status board complete with an audit trail logging updates.

### 📊 Admin Analytics & Reporting
* Executive dashboard displaying total revenue, live occupancy rates, booking volumes, and user metrics.
* Monthly booking and revenue trend visualization using Chart.js.
* One-click CSV export for booking records across custom date ranges.

---

## 🛠️ Tech Stack

| Layer | Technology |
| :--- | :--- |
| **Backend** | PHP 8.2+, Laravel 11, Eloquent ORM |
| **Database** | MySQL 8 / MariaDB 10.6+ |
| **Frontend** | Blade Templates, Tailwind CSS, Alpine.js, Chart.js, FontAwesome |
| **PDF & Export** | `barryvdh/laravel-dompdf`, `maatwebsite/excel` |
| **Authentication** | Laravel Session Auth + Custom Middleware |

---

## 📁 Project Directory Structure

```text
app/
  Models/                User, Room, Amenity, RoomImage, RoomStatusLog, Booking, Payment, Invoice
  Http/
    Controllers/
      RoomController.php          # Public catalog & room details
      BookingController.php       # Booking wizard, availability API, cancellation, PDF invoice
      Auth/                       # Authentication handlers
      Customer/DashboardController.php
      Staff/FrontDeskController.php
      Admin/RoomManagementController.php
      Admin/UserManagementController.php
      Admin/AnalyticsController.php # Dashboard metrics & CSV exports
    Middleware/RoleMiddleware.php # Role-gated access control
database/
  migrations/             Database schema migrations
  seeders/                UserSeeder, AmenitySeeder, RoomSeeder
resources/views/          Blade templates organized by area (rooms, bookings, customer, staff, admin)
routes/web.php            Role-gated route definitions
config/booking.php        Cancellation windows, tax rates, and gateway modes
