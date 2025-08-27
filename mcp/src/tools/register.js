import { toolBookingsByDate } from './toolBookingsByDate.js';
import { toolCreateBooking } from './toolCreateBooking.js';
import { toolUserLookup } from './toolUserLookup.js';
import { toolBookingsRange } from './toolBookingsRange.js';
import { toolUserUpcoming } from './toolUserUpcoming.js';
import { toolStatsDaily } from './toolStatsDaily.js';
import { toolUsersSearch } from './toolUsersSearch.js';
import { toolUsersCount } from './toolUsersCount.js';

export async function registerTools(server, pool) {
  server.registerTool('bookings.byDate', (input) => toolBookingsByDate(pool, input), {
    description: 'Lista reservas por data (YYYY-MM-DD).',
    input: { date: 'string (YYYY-MM-DD)' }
  });
  server.registerTool('booking.create', (input) => toolCreateBooking(pool, input), {
    description: 'Cria uma reserva obedecendo regras de negócio.',
    input: { user_id: 'number', date: 'string (YYYY-MM-DD)', meal_type: 'breakfast|lunch' }
  });
  server.registerTool('user.lookup', (input) => toolUserLookup(pool, input), {
    description: 'Busca usuário por email ou id',
    input: { id: 'number?', email: 'string?' }
  });
  server.registerTool('bookings.range', (input) => toolBookingsRange(pool, input), {
    description: 'Lista reservas em intervalo [start_date, end_date] (máx 31 dias).',
    input: { start_date: 'string (YYYY-MM-DD)', end_date: 'string (YYYY-MM-DD)' }
  });
  server.registerTool('user.upcomingBookings', (input) => toolUserUpcoming(pool, input), {
    description: 'Lista próximas reservas de um usuário (default 30 dias).',
    input: { user_id: 'number', days: 'number? (1-60)' }
  });
  server.registerTool('stats.dailyCounts', (input) => toolStatsDaily(pool, input), {
    description: 'Retorna contagem diária de reservas por refeição no intervalo (máx 60 dias).',
    input: { start_date: 'string (YYYY-MM-DD)', end_date: 'string (YYYY-MM-DD)' }
  });
  server.registerTool('users.search', (input) => toolUsersSearch(pool, input), {
    description: 'Busca usuários por nome ou email (ILIKE).',
    input: { q: 'string', limit: 'number? (1-50)' }
  });
  server.registerTool('users.count', () => toolUsersCount(pool), {
    description: 'Retorna contagem total de usuários.',
    input: {}
  });
}
