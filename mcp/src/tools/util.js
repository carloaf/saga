export function validateDate(str) {
  if (!/^\d{4}-\d{2}-\d{2}$/.test(str)) throw new Error('Formato de data inválido');
}
export function isWeekend(dateStr) {
  const d = new Date(dateStr + 'T00:00:00');
  const day = d.getUTCDay(); // 0 dom, 6 sáb
  return day === 0 || day === 6;
}
export function isFriday(dateStr) {
  const d = new Date(dateStr + 'T00:00:00');
  return d.getUTCDay() === 5;
}
export function todayISO() {
  return new Date().toISOString().slice(0,10);
}
export function daysDiff(a, b) {
  const ms = (new Date(b+'T00:00:00') - new Date(a+'T00:00:00'));
  return Math.round(ms / 86400000);
}
