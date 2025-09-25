# Portfolio CMS

Laravel-based Content Management System for managing portfolio data stored in Supabase.

## Features

- ✅ Work Experience Management (with company logos)
- ✅ Project Management
- ✅ CV Document Management
- ✅ Certificate Management
- ✅ Profile Management
- ✅ Social Links Management
- ✅ Education Management
- ✅ Tech Tags Management
- ✅ Supabase Storage Integration

## Deployment

This CMS is configured for deployment on Vercel with Supabase as the backend.

### Environment Variables

Copy `.env.example` to `.env` and configure:

- `SUPABASE_URL`: Your Supabase project URL
- `SUPABASE_KEY`: Your Supabase anon key
- `SUPABASE_SERVICE_KEY`: Your Supabase service key
- `SUPABASE_BUCKET`: Storage bucket name (default: portfolio-images)

## Tech Stack

- Laravel 12
- Supabase (Database + Storage)
- SQLite (local development)
- Vercel (deployment)
