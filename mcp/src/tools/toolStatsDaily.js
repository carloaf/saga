import { validateDate, daysDiff } from './util.js';

export async function toolStatsDaily(pool, { start_date, end_date }) {
  validateDate(start_date); validateDate(end_date);
  if (daysDiff(start_date, end_date) < 0) throw new Error('Intervalo invertido');
  if (daysDiff(start_date, end_date) > 60) throw new Error('Intervalo máximo 60 dias');
  const { rows } = await pool.query(`SELECT booking_date,
      sum(case when meal_type='breakfast' then 1 else 0 end) as breakfast_count,
      sum(case when meal_type='lunch' then 1 else 0 end) as lunch_count
    FROM bookings
    WHERE booking_date BETWEEN $1 AND $2
    GROUP BY booking_date
    ORDER BY booking_date`, [start_date, end_date]);
  return rows;
}
