export async function toolUsersSearch(pool, { q, limit = 20 }) {
  if (!q) throw new Error('q obrigatório');
  limit = Number(limit);
  if (isNaN(limit) || limit < 1 || limit > 50) throw new Error('limit inválido (1-50)');
  const like = `%${q}%`;
  const { rows } = await pool.query(`SELECT id, idt, full_name, war_name, email, role FROM users
    WHERE full_name ILIKE $1 OR war_name ILIKE $1 OR email ILIKE $1
    ORDER BY full_name
    LIMIT $2`, [like, limit]);
  return rows;
}
