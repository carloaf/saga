import { validateDate } from './util.js';

export async function toolBookingsByDate(pool, { date }) {
  validateDate(date);
  const { rows } = await pool.query(`SELECT b.id, b.user_id, b.booking_date, b.meal_type, u.full_name as user_name, u.war_name
    FROM bookings b JOIN users u ON u.id = b.user_id
    WHERE b.booking_date = $1
    ORDER BY b.meal_type, u.full_name`, [date]);
  return rows;
}
