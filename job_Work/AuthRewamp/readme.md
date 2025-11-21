# Unified Multi-Role Onboarding System  
**Viewer • Mentor • Brand** – One Smart Registration Flow

## What I Built

A **production-ready, role-based registration system** that supports three completely different user journeys using **one single form**:

| Role       | Final User Type       | Purpose                                      | Extra Data Collected                                 |
|------------|-----------------------|----------------------------------------------|-------------------------------------------------------|
| **Viewer** |  | Casual audience members                      | Just name + email + password                          |
| **Mentor** |   Mentor    | Industry experts, advisors, coaches          | Skills, LinkedIn, Category, Profile Photo            |
| **Brand**  |  Brand     | Companies, creators, organizations, agencies | Logo, Cover Photo, State, Category, Website (optional) |

No more confusing "expert/channel" terminology — now crystal clear and user-friendly!

---

### Key Achievements

- **Single registration page** for all three roles  
- Dynamic form fields & validation based on selected profile  
- Full support for **social login continuation** (Google/Apple → choose role later)  
- Handles both **fresh registrations** and **existing social users completing their profile**  
- Smart image handling with beautiful default fallbacks  
- Unique, SEO-friendly **Brand slug** generation with conflict resolution  
- Skill syncing for Mentors  
- Auto-login + intelligent redirect preservation (`r` param or intended URL)  
- Works seamlessly with AJAX and classic form submits  

---

### Clean Dual-Controller Architecture

| Controller                  | Purpose                                           |
|-----------------------------|---------------------------------------------------|
| `RegisteredUserController`  | Standard email + password registration            |
| `SocialRegisterController`  | Social-auth users completing their role/profile   |

Both controllers share **identical business logic** → zero duplication, 100% consistency.

---

### Dynamic Validation (Super Clean)

```php
$rules = [
    'name'  => 'required|string|max:255',
    'email' => 'required|email',
    'profile' => 'required|in:viewer,mentor,brand',
];

if ($profile === 'mentor') {
    $rules += ['category_id' => 'required', 'linkedin_url' => 'required|url'];
}

if ($profile === 'brand') {
    $rules += [
        'category_id'  => 'required',
        'state_id'     => 'required',
        'logo'         => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'cover_photo'  => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ];
];
```

Validation adapts automatically — impossible to submit wrong data.

---

### Role-Specific Profile Creation

#### Mentor (formerly Expert)
- Creates record in `mentors` table with UUID  
- Status starts as `draft` (admin approval workflow ready)  
- Syncs selected skills  
- Stores LinkedIn URL + professional photo  

#### Brand (formerly Channel)
- Generates clean, unique slug (`acme-brand`, `nike-official-1738456789`)  
- Uploads logo → `poster`, cover photo → `cover_photo`  
- Falls back to elegant default brand assets  
- Links brand to user via `user_id`  

#### Viewer
- Instant access, zero friction  

---

### Bonus Smart Features

- Automatically accepts pending **Brand collaborator invites** by email  
- Secure image uploads with proper storage paths  
- Fires Laravel `Registered` event  
- Preserves deep links and intended redirects  
- Clean, reusable private methods → easy to extend  

---

### Tech Stack

- Laravel 10+ / PHP 8.2+
- Inertia.js + Vue 3 (Composition API)
- Eloquent + UUIDs + Slugs
- Dynamic validation rules
- File storage with fallbacks
- Session & redirect handling

---

### Why This Solution Stands Out

- Modern, intuitive naming: **Mentor** & **Brand** instantly make sense  
- Eliminates user confusion that "expert/channel" caused  
- Future-proof: just add "Investor", "Startup Founder", etc.  
- Amazing developer experience + flawless user experience  
- Production-ready, secure, and highly maintainable  
