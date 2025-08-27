import { validateDate, isWeekend, isFriday, todayISO, daysDiff } from './util.js';

export async function toolCreateBooking(pool, { user_id, date, meal_type }) {
  if (!user_id) throw new Error('user_id obrigatório');
  if (!meal_type) throw new Error('meal_type obrigatório');
  meal_type = meal_type.toLowerCase();
  if (!['breakfast','lunch'].includes(meal_type)) throw new Error('meal_type inválido');
  validateDate(date);

  if (isWeekend(date)) throw new Error('Fim de semana não permitido');
  if (isFriday(date) && meal_type === 'lunch') throw new Error('Almoço não disponível na sexta-feira');
  if (daysDiff(todayISO(), date) < 0) throw new Error('Data no passado');
  if (daysDiff(todayISO(), date) > 30) throw new Error('Janela máxima de 30 dias');

  // Verifica duplicidade
  const dup = await pool.query('SELECT 1 FROM bookings WHERE user_id=$1 AND booking_date=$2 AND meal_type=$3', [user_id, date, meal_type]);
  if (dup.rowCount) throw new Error('Reserva já existente para este usuário/data/refeição');

  const { rows } = await pool.query('INSERT INTO bookings (user_id, booking_date, meal_type, created_at, updated_at) VALUES ($1,$2,$3, now(), now()) RETURNING id', [user_id, date, meal_type]);
  return { id: rows[0].id, status: 'created' };
}
