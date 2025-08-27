export async function toolUserLookup(pool, { id, email }) {
  if (!id && !email) throw new Error('Informe id ou email');
  let where, param;
  if (id) { where = 'id = $1'; param = id; }
  else { where = 'email = $1'; param = email; }
  const { rows } = await pool.query(`SELECT id, idt, full_name, war_name, email, role, rank_id, organization_id FROM users WHERE ${where} LIMIT 1`, [param]);
  if (!rows.length) return null;
  return rows[0];
}
