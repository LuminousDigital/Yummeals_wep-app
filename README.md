```markdown
# Yummeals API Documentation

## Setup Instructions

```bash
# Clone and setup
git clone https://github.com/LuminousDigital/Yummeals_wep-app.git
cd Yummeals_wep-app
cp .env.example .env
composer install
php artisan key:generate

# Database
php artisan migrate --seed

# Permissions
php artisan storage:link
chmod -R 775 storage bootstrap/cache
```

## API Base URL

```
https://app.yummealsapp.com
```

## Authentication

```
x-api-key: z8p53xn6-n2f5-29w7-7193-s500c15553h171620
Content-Type: application/json
Authorization: Bearer {token}  # For protected routes
```

## API Endpoints

### Authentication

#### Login

- **Method**: POST
- **Endpoint**: `/auth/login`

**Request:**

```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

**Response:**

```json
{
  "token": "eyJhbGci...",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "user@example.com"
  }
}
```

#### Logout

- **Method**: POST
- **Endpoint**: `/auth/logout`

**Response:**

```json
{
  "message": "Successfully logged out"
}
```

### User Management

#### Registration

- **Method**: POST
- **Endpoint**: `/auth/signup/register`

**Request:**

```json
{
  "first_name": "John",
  "last_name": "Doe",
  "email": "john.doe@example.com",
  "phone": "1234567890",
  "country_code": "+234",
  "password": "SecurePass123!",
  "password_confirmation": "SecurePass123!",
  "referral_code": "FRIEND123"
}
```

**Response:**

```json
{
  "status": true,
  "message": "Registration successful",
  "data": {
    "user": {
      "id": 7,
      "name": "John Doe",
      "email": "john.doe@example.com",
      "referral_code": "JOHND123"
    },
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
  }
}
```

### Referral System

#### Get Referral Info

- **Method**: GET
- **Endpoint**: `/referral`

**Response:**

```json
{
  "referral_code": "JOHND123",
  "total_referrals": 5,
  "referral_balance": 50.00,
  "referrals": [
    {
      "id": 8,
      "name": "Jane Smith",
      "email": "jane@example.com",
      "created_at": "2023-05-15T10:00:00Z"
    }
  ]
}
```

#### Leaderboard

- **Method**: GET
- **Endpoint**: `/referral/leaderboard`

**Response:**

```json
{
  "leaderboard": [
    {
      "id": 1,
      "name": "Top Referrer",
      "referral_code": "TOP123",
      "total_referrals": 50,
      "referral_balance": 500.00
    }
  ],
  "updated_at": "2023-05-15T12:00:00Z"
}
```

#### Claim Bonus

- **Method**: POST
- **Endpoint**: `/referral/claim`

**Request:**

```json
{
  "amount": 25.00
}
```

**Response:**

```json
{
  "message": "Bonus claimed successfully",
  "new_balance": 25.00
}
```

### Profile Management

#### Get Profile

- **Method**: GET
- **Endpoint**: `/profile`

**Response:**

```json
{
  "data": {
    "id": 7,
    "name": "John Doe",
    "first_name": "John",
    "last_name": "Doe",
    "phone": "1234569890",
    "email": "john.doe@example.com",
    "username": "john-doe",
    "balance": "0.00",
    "currency_balance": "$0.00",
    "referral_code": "JOHNDO123",
    "referrals": [
      {
        "id": 8,
        "name": "Referred User 1",
        "email": "user1@example.com",
        "phone": "1235439890",
        "referral_code": "USER1CODE",
        "referred_by": 7,
        "referral_balance": "0.00",
        "total_referrals": 0
      }
    ],
    "referrer": {
      "id": 4,
      "name": "Referrer User",
      "email": "referrer@example.com",
      "phone": "1234577890",
      "referral_code": "REFERRER1",
      "total_referrals": 2
    },
    "referral_balance": "0.00",
    "total_referrals": 4,
    "image": "https://app.yummealsapp.com/images/default/profile.png",
    "country_code": "+1"
  }
}
```

#### Update Profile

- **Method**: PUT
- **Endpoint**: `/profile`

**Request:**

```json
{
  "name": "John Updated",
  "email": "updated@example.com",
  "phone": "+1234567891"
}
```

**Response:**

```json
{
  "data": {
    "id": 7,
    "name": "John Updated",
    "email": "updated@example.com",
    "phone": "+1234567891"
  },
  "message": "Profile updated successfully"
}
```