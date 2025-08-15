```mermaid
erDiagram
    ORGANIZATIONS {
        bigint id PK
        varchar name UK
        varchar abbreviation
        boolean is_host
        timestamp created_at
        timestamp updated_at
    }
    
    RANKS {
        bigint id PK
        varchar name UK
        varchar abbreviation
        integer order
        timestamp created_at
        timestamp updated_at
    }
    
    USERS {
        bigint id PK
        varchar google_id UK
        varchar full_name
        varchar war_name
        varchar email UK
        timestamp email_verified_at
        varchar password
        varchar avatar_url
        bigint rank_id FK
        bigint organization_id FK
        varchar subunit
        enum armed_force
        char gender
        date ready_at_om_date
        enum role
        boolean is_active
        varchar remember_token
        timestamp created_at
        timestamp updated_at
    }
    
    BOOKINGS {
        bigint id PK
        bigint user_id FK
        date booking_date
        enum meal_type
        enum status
        timestamp created_at
        timestamp updated_at
    }
    
    WEEKLY_MENUS {
        bigint id PK
        date week_start
        json menu_data
        boolean is_active
        bigint created_by FK
        bigint updated_by FK
        timestamp created_at
        timestamp updated_at
    }
    
    SESSIONS {
        varchar id PK
        bigint user_id FK
        varchar ip_address
        text user_agent
        text payload
        integer last_activity
    }

    %% Relationships
    ORGANIZATIONS ||--o{ USERS : "organização"
    RANKS ||--o{ USERS : "posto/graduação"
    USERS ||--o{ BOOKINGS : "reservas"
    USERS ||--o{ WEEKLY_MENUS : "criado_por"
    USERS ||--o{ WEEKLY_MENUS : "atualizado_por"
    USERS ||--o{ SESSIONS : "sessões"
```

## Modelo de Dados Detalhado - SAGA

### 📊 Estatísticas do Schema
- **6 Tabelas Principais**
- **5 Foreign Keys**
- **4 Unique Constraints**
- **3 Composite Indexes**
- **Múltiplos Enums para Validação**

### 🏗️ Arquitetura do Sistema

#### 1. **Módulo de Autenticação e Usuários**
```
USERS (Tabela Central)
├── Autenticação Híbrida (Google OAuth + Tradicional)
├── Perfil Militar Completo
├── Hierarquia de Permissões
└── Dados Pessoais e Funcionais
```

#### 2. **Módulo Organizacional**
```
ORGANIZATIONS + RANKS
├── Estrutura Militar
├── Hierarquia de Comando
├── Subunidades (SU)
└── Forças Armadas (EB/MB/FAB)
```

#### 3. **Sistema de Reservas**
```
BOOKINGS
├── Controle por Data/Refeição
├── Status de Confirmação
├── Restrições de Unicidade
└── Auditoria Temporal
```

#### 4. **Gestão de Cardápios**
```
WEEKLY_MENUS
├── Estrutura Semanal
├── Dados JSON Flexíveis
├── Controle de Versões
└── Sistema de Ativação
```

### 🔍 Queries Comuns Otimizadas

#### Booking por Usuário e Data
```sql
SELECT b.*, u.war_name, u.full_name 
FROM bookings b 
JOIN users u ON b.user_id = u.id 
WHERE b.booking_date = '2025-08-08' 
  AND b.meal_type = 'lunch'
  AND b.status = 'confirmed';
```

#### Usuários por Organização
```sql
SELECT u.*, r.name as rank_name, o.name as org_name
FROM users u
JOIN ranks r ON u.rank_id = r.id
JOIN organizations o ON u.organization_id = o.id
WHERE o.name = '11º Depósito de Suprimento'
  AND u.is_active = true;
```

#### Cardápio da Semana Ativo
```sql
SELECT * FROM weekly_menus 
WHERE week_start <= '2025-08-08' 
  AND week_start > '2025-08-08' - INTERVAL '7 days'
  AND is_active = true;
```

### 🚀 Performance e Indexes

#### Indexes Existentes:
- `bookings(user_id, booking_date, meal_type)` - UNIQUE
- `weekly_menus(week_start)` - INDEX
- `weekly_menus(week_start, is_active)` - COMPOSITE INDEX
- Primary Keys automáticos em todas as tabelas

#### Indexes Recomendados:
```sql
-- Para queries de booking por data
CREATE INDEX idx_bookings_date_status ON bookings(booking_date, status);

-- Para filtros de usuário ativo
CREATE INDEX idx_users_active_org ON users(is_active, organization_id);

-- Para busca por posto/graduação
CREATE INDEX idx_users_rank_org ON users(rank_id, organization_id);
```
