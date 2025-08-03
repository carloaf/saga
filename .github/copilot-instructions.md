# SAGA - Sistema de Agendamento e Gestão de Arranchamento

<!-- Use this file to provide workspace-specific custom instructions to Copilot. For more details, visit https://code.visualstudio.com/docs/copilot/copilot-customization#_use-a-githubcopilotinstructionsmd-file -->

## Project Overview

This is a Laravel-based military meal booking system (SAGA) that allows military personnel to schedule their meals (breakfast and lunch) for weekdays. The system includes:

- **Authentication**: Google OAuth integration
- **User Management**: Role-based access (user, manager)
- **Booking System**: Interactive calendar for meal scheduling
- **Dashboard**: Analytics and reporting with Chart.js
- **Reports**: PDF/Excel export capabilities

## Tech Stack

- **Backend**: Laravel 11, PHP 8.4
- **Frontend**: Blade templates, Laravel Livewire, Tailwind CSS
- **Database**: PostgreSQL
- **Infrastructure**: Docker, Apache
- **Charts**: Chart.js
- **Authentication**: Laravel Socialite (Google)

## Key Features

1. **Meal Booking Rules**:
   - Only weekdays (Monday-Friday)
   - Friday: Only breakfast available
   - 30-day advance booking window
   - Real-time updates with Livewire

2. **User Roles**:
   - `user`: Standard military personnel
   - `manager`: Administrative access

3. **Data Models**:
   - `User`: Military personnel with rank and organization
   - `Booking`: Individual meal reservations
   - `Rank`: Military ranks/positions
   - `Organization`: Military units

## Development Guidelines

- Follow Laravel best practices and conventions
- Use Livewire for interactive components
- Implement proper validation and security measures
- Maintain responsive design with Tailwind CSS
- Use Portuguese language for user interface
- Ensure military-appropriate terminology and respect

## Business Rules

- Weekends are blocked for booking
- Friday lunch is not available
- Users can only book for future dates
- Maximum 30 days advance booking
- Each user can book once per meal type per day
- Superusers have access to management features

## Database Schema

Key relationships:
- Users belong to Ranks and Organizations
- Bookings belong to Users
- Unique constraint on (user_id, booking_date, meal_type)
