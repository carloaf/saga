export async function toolUsersCount(pool) {
  const { rows } = await pool.query('SELECT COUNT(*)::int AS total FROM users');
  return { total: rows[0].total };
}
