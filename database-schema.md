# Diagrama do Banco de Dados - SAGA
## Sistema de Agendamento e Gestão de Arranchamento

```
┌─────────────────────────────────────────────────────────────────────────────────────┐
│                                   SAGA Database Schema                               │
└─────────────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────┐     ┌─────────────────────────────┐     ┌─────────────────────────────┐
│         ORGANIZATIONS       │     │            RANKS            │     │           USERS             │
├─────────────────────────────┤     ├─────────────────────────────┤     ├─────────────────────────────┤
│ 🔑 id (PK)                  │     │ 🔑 id (PK)                  │     │ 🔑 id (PK)                  │
│ 📝 name (unique)            │     │ 📝 name (unique)            │     │ 📝 google_id (unique, null) │
│ 📝 abbreviation (nullable)  │     │ 📝 abbreviation (nullable)  │     │ 📝 full_name               │
│ ✅ is_host (default: false) │     │ 🔢 order (default: 0)      │     │ 📝 war_name                │
│ 📅 created_at               │     │ 📅 created_at               │     │ 📧 email (unique)           │
│ 📅 updated_at               │     │ 📅 updated_at               │     │ 📅 email_verified_at (null) │
└─────────────────────────────┘     └─────────────────────────────┘     │ 🔐 password (nullable)      │
                                                                         │ 🖼️ avatar_url (nullable)    │
                                                                         │ 🔗 rank_id (FK)            │
                                                                         │ 🔗 organization_id (FK)     │
                                                                         │ 📝 subunit (nullable)       │
                                                                         │ 🪖 armed_force (FAB/MB/EB) │
                                                                         │ 👤 gender (M/F, char(1))   │
                                                                         │ 📅 ready_at_om_date        │
                                                                         │ 👥 role (user/manager/su)   │
                                                                         │ ✅ is_active (default: true)│
                                                                         │ 🎫 remember_token           │
                                                                         │ 📅 created_at               │
                                                                         │ 📅 updated_at               │
                                                                         └─────────────────────────────┘

         │                                   │                                        │
         │                                   │                                        │
         └──────────────────────────────────┐└─────────────────────────────────┐     │
                                           ▼                                   ▼     │
┌─────────────────────────────┐     ┌─────────────────────────────────────────────────▼─────┐
│         BOOKINGS            │     │                  WEEKLY_MENUS                         │
├─────────────────────────────┤     ├───────────────────────────────────────────────────────┤
│ 🔑 id (PK)                  │     │ 🔑 id (PK)                                             │
│ 🔗 user_id (FK)            │◄────│ 📅 week_start (start of week - Monday)                │
│ 📅 booking_date             │     │ 📋 menu_data (JSON - weekly menu structure)          │
│ 🍽️ meal_type (breakfast/lunch)│  │ ✅ is_active (default: true)                          │
│ 📊 status (confirmed/cancel)│     │ 🔗 created_by (FK to users)                          │
│ 📅 created_at               │     │ 🔗 updated_by (FK to users, nullable)                │
│ 📅 updated_at               │     │ 📅 created_at                                          │
│                             │     │ 📅 updated_at                                          │
│ UNIQUE(user_id, booking_date,│     │                                                       │
│        meal_type)           │     │ INDEX: week_start                                      │
└─────────────────────────────┘     │ INDEX: (week_start, is_active)                        │
                                    └───────────────────────────────────────────────────────┘

┌─────────────────────────────┐
│          SESSIONS           │
├─────────────────────────────┤
│ 📝 id (string, PK)          │
│ 🔗 user_id (nullable FK)    │
│ 🌐 ip_address (nullable)    │
│ 🖥️ user_agent (text)        │
│ 📋 payload (text)           │
│ 📅 last_activity (int)      │
└─────────────────────────────┘
```

## Relacionamentos e Constraints

### 🔗 Foreign Keys
- `users.rank_id` → `ranks.id`
- `users.organization_id` → `organizations.id`
- `bookings.user_id` → `users.id`
- `weekly_menus.created_by` → `users.id`
- `weekly_menus.updated_by` → `users.id`
- `sessions.user_id` → `users.id`

### 🎯 Unique Constraints
- `organizations.name`
- `ranks.name`
- `users.google_id` (when not null)
- `users.email`
- `bookings(user_id, booking_date, meal_type)` - Um usuário só pode reservar uma vez cada tipo de refeição por dia

### 📊 Enums e Valores Permitidos
- `users.armed_force`: 'FAB', 'MB', 'EB'
- `users.gender`: 'M', 'F'
- `users.role`: 'user', 'manager', 'superuser'
- `bookings.meal_type`: 'breakfast', 'lunch'
- `bookings.status`: 'confirmed', 'cancelled', 'pending'

### 🎲 Campos com Valores Padrão
- `organizations.is_host`: false
- `ranks.order`: 0
- `users.role`: 'user'
- `users.is_active`: true
- `bookings.status`: 'confirmed'
- `weekly_menus.is_active`: true

## Funcionalidades Implementadas

### 👤 Gestão de Usuários
- Autenticação via Google OAuth ou tradicional
- Hierarquia militar com postos/graduações
- Organização militar com subunidades
- Diferentes níveis de acesso (user/manager/superuser)

### 🍽️ Sistema de Reservas
- Reserva de café da manhã e almoço
- Controle por data e tipo de refeição
- Status de confirmação/cancelamento
- Único booking por tipo de refeição por dia

### 📋 Cardápio Semanal
- Gestão de cardápios por semana
- Estrutura JSON flexível para dados do menu
- Controle de versões e histórico
- Sistema de ativação/desativação

### 🔐 Segurança e Auditoria
- Controle de sessões
- Rastreamento de criação e edição
- Campos de timestamp automáticos
- Constraints para integridade de dados
