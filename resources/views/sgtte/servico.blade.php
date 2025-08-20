@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-4">Serviço - Arranchamento Companhia (Sgtte)</h1>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="GET" class="mb-4 flex gap-3 items-end">
        <div>
            <label class="block text-sm font-medium">Data</label>
                <input id="datePicker" type="date" name="date" value="{{ $targetDate }}" class="border rounded px-2 py-1" />
        </div>
        <div class="flex-1">
            <label class="block text-sm font-medium">Buscar por Nome de Guerra</label>
            <input type="text" name="q" value="{{ $search }}" placeholder="Ex: FULANO" class="w-full border rounded px-2 py-1" />
        </div>
        <button class="bg-blue-600 text-white px-4 py-2 rounded">Filtrar</button>
    </form>

    <form method="POST" action="{{ route('sgtte.store') }}" class="space-y-6">
        @csrf
        <input type="hidden" name="booking_date" value="{{ $targetDate }}" />
        <div class="overflow-x-auto bg-white shadow rounded">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-3 py-2 text-left">Posto/Grad</th>
                        <th class="px-3 py-2 text-left">Nome Guerra</th>
                        <th class="px-3 py-2 text-center">Café</th>
                        <th class="px-3 py-2 text-center">Almoço</th>
                        <th class="px-3 py-2 text-center">Jantar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($militares as $m)
                        @php
                            $existing = $existingBookings->get($m->id, collect())->pluck('meal_type')->toArray();
                        @endphp
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-3 py-2">{{ $m->rank->abbreviation ?? $m->rank->name ?? '-' }}</td>
                            <td class="px-3 py-2 font-medium">{{ $m->war_name }}</td>
                            <td class="px-3 py-2 text-center">
                                <input type="checkbox" name="bookings[{{ $loop->index }}][meals][]" value="breakfast" {{ in_array('breakfast',$existing) ? 'checked' : '' }} @unless($editable) disabled @endunless>
                            </td>
                            <td class="px-3 py-2 text-center">
                                <input type="checkbox" name="bookings[{{ $loop->index }}][meals][]" value="lunch" {{ in_array('lunch',$existing) ? 'checked' : '' }} @unless($editable) disabled @endunless>
                            </td>
                            <td class="px-3 py-2 text-center">
                                <input type="checkbox" name="bookings[{{ $loop->index }}][meals][]" value="dinner" {{ in_array('dinner',$existing) ? 'checked' : '' }} @unless($editable) disabled @endunless>
                            </td>
                            <input type="hidden" name="bookings[{{ $loop->index }}][user_id]" value="{{ $m->id }}" />
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-3 py-4 text-center text-gray-500">Nenhum militar encontrado.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($editable)
            <div class="flex justify-end">
                <button class="bg-green-600 text-white px-6 py-2 rounded shadow">Salvar Serviço</button>
            </div>
        @else
            <div class="text-sm text-gray-600">Data não editável (somente visualização). Selecione uma data futura para alterar reservas.</div>
        @endif
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const dateInput = document.getElementById('datePicker');
    const tableBody = document.querySelector('table tbody');
    // Capturar user_ids na mesma ordem para aplicar marcações
    const rowUserIds = Array.from(tableBody.querySelectorAll('input[type="hidden"][name$="[user_id]"]')).map(h => h.value);

    async function fetchBookings(date) {
        if(!date) return;
        try {
            const url = new URL("{{ route('sgtte.servico.bookings') }}", window.location.origin);
            url.searchParams.set('date', date);
            rowUserIds.forEach(id => url.searchParams.append('user_ids[]', id));
            const resp = await fetch(url.toString(), {headers: {'Accept': 'application/json'}});
            if(!resp.ok) throw new Error('Erro ao carregar reservas');
            const data = await resp.json();
            applyBookings(data.bookings || {});
        } catch(e) {
            console.error(e);
        }
    }

    function applyBookings(bookingsMap) {
        const editable = new Date(dateInput.value) > new Date(new Date().toISOString().substring(0,10));
        // Limpar todos os checkboxes antes
        tableBody.querySelectorAll('input[type="checkbox"]').forEach(cb => { cb.checked = false; cb.disabled = !editable; });
        rowUserIds.forEach((userId, idx) => {
            const meals = bookingsMap[userId];
            if(!meals) return;
            meals.forEach(meal => {
                const selector = `input[name="bookings[${idx}][meals][]"][value="${meal}"]`;
                const cb = tableBody.querySelector(selector);
                if(cb) { cb.checked = true; cb.disabled = !editable; }
            });
        });
        // Atualizar hidden booking_date
        const hiddenDate = document.querySelector('input[type="hidden"][name="booking_date"]');
        if (hiddenDate) hiddenDate.value = dateInput.value;
        // Mostrar/ocultar botão
        const submitBtn = document.querySelector('form[action*="sgtte"][method="POST"] button');
        if(submitBtn){
            if(editable){
                submitBtn.closest('div').classList.remove('hidden');
            } else {
                submitBtn.closest('div').classList.add('hidden');
            }
        }
    }

    dateInput.addEventListener('change', (e) => {
        fetchBookings(e.target.value);
    });
});
</script>
@endpush
