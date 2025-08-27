import { todayISO, daysDiff } from './util.js';

export async function toolUserUpcoming(pool, { user_id, days = 30 }) {
  if (!user_id) throw new Error('user_id obrigatório');
  days = Number(days);
  if (isNaN(days) || days < 1 || days > 60) throw new Error('days inválido (1-60)');
  const start = todayISO();
  const { rows } = await pool.query(`SELECT id, booking_date, meal_type
    FROM bookings
    WHERE user_id=$1 AND booking_date BETWEEN $2 AND ($2::date + ($3||' days')::interval)
    ORDER BY booking_date, meal_type`, [user_id, start, days]);
  return rows;
}
