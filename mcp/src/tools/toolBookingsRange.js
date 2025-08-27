import { validateDate, daysDiff } from './util.js';

export async function toolBookingsRange(pool, { start_date, end_date }) {
  validateDate(start_date); validateDate(end_date);
  if (daysDiff(start_date, end_date) < 0) throw new Error('Intervalo invertido');
  if (daysDiff(start_date, end_date) > 31) throw new Error('Intervalo máximo 31 dias');
  const { rows } = await pool.query(`SELECT b.id, b.user_id, u.full_name as user_name, u.war_name, b.booking_date, b.meal_type
    FROM bookings b JOIN users u ON u.id=b.user_id
    WHERE b.booking_date BETWEEN $1 AND $2
    ORDER BY b.booking_date, b.meal_type, u.full_name`, [start_date, end_date]);
  return rows;
}
